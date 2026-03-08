# Mobile App Integration Guide - Pool Funding

## Overview
This guide helps mobile app developers integrate the Pool Funding feature into the JMJ Trading Platform mobile application.

---

## Screen Flow

```
Portfolio Screen
    ↓
Join Pool Funding Screen (Pool Status)
    ↓
Application Form Screen
    ↓
Confirmation Screen
    ↓
My Investments Screen
```

---

## 1. Pool Status Screen

**API Endpoint:** `GET /api/v1/pools`

**Screen Components:**
- Current Pool Status Card
  - Total Pool Amount
  - Number of Investors
  - Last Return Percentage
  - Minimum Investment Info
  - Profit Distribution Info

- How It Works Section (4 steps)
- Join Pool Button

**Sample Code:**

```typescript
interface Pool {
  id: string;
  name: string;
  total_amount: string;
  investor_count: number;
  last_return: string;
  minimum_investment: string;
  status: string;
}

async function fetchActivePool(): Promise<Pool> {
  const response = await api.get('/api/v1/pools');
  return response.data.data[0]; // Get first active pool
}
```

---

## 2. Application Form Screen

**API Endpoint:** `POST /api/v1/pool-investments`

**Form Fields:**

| Field | Type | Validation | UI Component |
|-------|------|------------|--------------|
| Investor ID | Display Only | Auto-generated | Read-only Text |
| Full Name | Text Input | Required, max 255 | Text Input |
| Phone Number | Phone Input | Required, max 20 | Phone Input with country code |
| Bank Name | Text Input | Required, max 255 | Text Input with suggestions |
| Account Number | Number Input | Required, 10 digits | Numeric Keyboard |
| Account Name | Text Input | Required, max 255 | Text Input |
| Contribution Amount | Number Input | Required, min 1000 | Currency Input |
| Payment Proof | File Upload | Required, image | Image Picker |
| Terms Checkbox | Checkbox | Required, must be true | Checkbox with link |

**Form Validation:**

```typescript
interface PoolInvestmentForm {
  pool_id: string;
  full_name: string;
  phone_number: string;
  bank_name: string;
  account_number: string;
  account_name: string;
  contribution: number;
  payment_proof_path: string;
  terms_accepted: boolean;
}

function validateForm(form: PoolInvestmentForm): string[] {
  const errors: string[] = [];
  
  if (!form.full_name) errors.push('Full name is required');
  if (!form.phone_number) errors.push('Phone number is required');
  if (!form.bank_name) errors.push('Bank name is required');
  if (!form.account_number) errors.push('Account number is required');
  if (form.account_number.length !== 10) errors.push('Account number must be 10 digits');
  if (!form.account_name) errors.push('Account name is required');
  if (form.contribution < 1000) errors.push('Minimum investment is $1,000');
  if (!form.payment_proof_path) errors.push('Payment proof is required');
  if (!form.terms_accepted) errors.push('You must accept terms and conditions');
  
  return errors;
}
```

**Image Upload Flow:**

```typescript
async function uploadPaymentProof(imageUri: string): Promise<string> {
  const formData = new FormData();
  formData.append('file', {
    uri: imageUri,
    type: 'image/jpeg',
    name: 'payment_proof.jpg',
  });
  
  const response = await api.post('/api/v1/upload', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  });
  
  return response.data.url; // Return uploaded file URL
}

async function submitInvestment(form: PoolInvestmentForm) {
  try {
    // 1. Upload payment proof
    const paymentProofUrl = await uploadPaymentProof(form.payment_proof_path);
    
    // 2. Submit investment
    const response = await api.post('/api/v1/pool-investments', {
      ...form,
      payment_proof_path: paymentProofUrl
    });
    
    // 3. Show success message
    showSuccessMessage(response.data.message);
    
    // 4. Navigate to investments screen
    navigation.navigate('MyInvestments');
    
  } catch (error) {
    handleError(error);
  }
}
```

---

## 3. My Investments Screen

**API Endpoint:** `GET /api/v1/pool-investments`

**Screen Components:**
- List of investments with status badges
- Investment details card
- Profit history section

**Investment Card:**

```typescript
interface PoolInvestment {
  id: string;
  pool: Pool;
  full_name: string;
  phone_number: string;
  contribution: string;
  share_percentage: string;
  status: 'pending' | 'verified' | 'active' | 'rejected';
  verified_at: string | null;
  rejection_reason?: string;
  submitted_at: string;
}

function InvestmentCard({ investment }: { investment: PoolInvestment }) {
  const statusColors = {
    pending: '#FFA500',
    verified: '#4169E1',
    active: '#32CD32',
    rejected: '#DC143C'
  };
  
  return (
    <Card>
      <StatusBadge color={statusColors[investment.status]}>
        {investment.status.toUpperCase()}
      </StatusBadge>
      
      <Text>Pool: {investment.pool.name}</Text>
      <Text>Contribution: ${investment.contribution}</Text>
      <Text>Share: {investment.share_percentage}%</Text>
      
      {investment.status === 'pending' && (
        <Text>Verification in progress (24-48 hours)</Text>
      )}
      
      {investment.status === 'rejected' && (
        <Text style={{color: 'red'}}>
          Reason: {investment.rejection_reason}
        </Text>
      )}
      
      {investment.status === 'active' && (
        <Button onPress={() => viewProfits(investment.id)}>
          View Profits
        </Button>
      )}
    </Card>
  );
}
```

---

## 4. Profit History Screen

**API Endpoint:** `GET /api/v1/profit-distributions`

**Screen Components:**
- List of profit distributions
- Total profits summary
- Distribution status

```typescript
interface ProfitDistribution {
  id: string;
  distribution_date: string;
  profit_amount: string;
  pool_return: string;
  status: 'pending' | 'processed' | 'failed';
  processed_at: string | null;
  failure_reason?: string;
}

function ProfitHistoryScreen() {
  const [distributions, setDistributions] = useState<ProfitDistribution[]>([]);
  
  useEffect(() => {
    fetchProfitDistributions();
  }, []);
  
  async function fetchProfitDistributions() {
    const response = await api.get('/api/v1/profit-distributions');
    setDistributions(response.data.data);
  }
  
  const totalProfits = distributions
    .filter(d => d.status === 'processed')
    .reduce((sum, d) => sum + parseFloat(d.profit_amount), 0);
  
  return (
    <View>
      <SummaryCard>
        <Text>Total Profits Received</Text>
        <Text style={{fontSize: 32}}>${totalProfits.toFixed(2)}</Text>
      </SummaryCard>
      
      <FlatList
        data={distributions}
        renderItem={({ item }) => <ProfitCard distribution={item} />}
      />
    </View>
  );
}
```

---

## State Management

### Redux/Context Example

```typescript
// Store
interface PoolState {
  activePool: Pool | null;
  myInvestments: PoolInvestment[];
  profitDistributions: ProfitDistribution[];
  loading: boolean;
  error: string | null;
}

// Actions
const poolSlice = createSlice({
  name: 'pool',
  initialState: {
    activePool: null,
    myInvestments: [],
    profitDistributions: [],
    loading: false,
    error: null
  },
  reducers: {
    setActivePool: (state, action) => {
      state.activePool = action.payload;
    },
    setMyInvestments: (state, action) => {
      state.myInvestments = action.payload;
    },
    setProfitDistributions: (state, action) => {
      state.profitDistributions = action.payload;
    },
    addInvestment: (state, action) => {
      state.myInvestments.unshift(action.payload);
    }
  }
});
```

---

## Error Handling

```typescript
function handleApiError(error: any) {
  if (error.response) {
    // Server responded with error
    const { status, data } = error.response;
    
    switch (status) {
      case 401:
        // Unauthorized - redirect to login
        navigation.navigate('Login');
        break;
        
      case 403:
        // Forbidden
        showAlert('Access Denied', data.message);
        break;
        
      case 422:
        // Validation errors
        const errors = Object.values(data.errors).flat();
        showAlert('Validation Error', errors.join('\n'));
        break;
        
      case 500:
        // Server error
        showAlert('Server Error', 'Please try again later');
        break;
        
      default:
        showAlert('Error', data.message || 'Something went wrong');
    }
  } else if (error.request) {
    // Network error
    showAlert('Network Error', 'Please check your internet connection');
  } else {
    // Other errors
    showAlert('Error', error.message);
  }
}
```

---

## Notifications

### Push Notification Events

```typescript
// Listen for investment status updates
onNotification('investment.verified', (data) => {
  showNotification({
    title: 'Investment Verified',
    body: `Your investment of $${data.contribution} has been verified!`,
    data: { investmentId: data.id }
  });
});

onNotification('investment.rejected', (data) => {
  showNotification({
    title: 'Investment Rejected',
    body: data.rejection_reason,
    data: { investmentId: data.id }
  });
});

onNotification('profit.distributed', (data) => {
  showNotification({
    title: 'Profit Received',
    body: `You received $${data.profit_amount} profit!`,
    data: { distributionId: data.id }
  });
});
```

---

## Offline Support

```typescript
// Cache pool data
async function fetchPoolWithCache() {
  try {
    const response = await api.get('/api/v1/pools');
    await AsyncStorage.setItem('cached_pool', JSON.stringify(response.data));
    return response.data;
  } catch (error) {
    // Return cached data if offline
    const cached = await AsyncStorage.getItem('cached_pool');
    if (cached) return JSON.parse(cached);
    throw error;
  }
}

// Queue investment submission for when online
async function submitInvestmentOffline(form: PoolInvestmentForm) {
  const queue = await AsyncStorage.getItem('investment_queue') || '[]';
  const investments = JSON.parse(queue);
  investments.push(form);
  await AsyncStorage.setItem('investment_queue', JSON.stringify(investments));
  
  showMessage('Investment saved. Will submit when online.');
}
```

---

## Testing Checklist

- [ ] Pool status displays correctly
- [ ] Form validation works for all fields
- [ ] Image upload succeeds
- [ ] Investment submission succeeds
- [ ] Success message displays
- [ ] Investment appears in list
- [ ] Status badges show correct colors
- [ ] Profit distributions load
- [ ] Error handling works
- [ ] Offline mode works
- [ ] Push notifications work
- [ ] Navigation flows correctly

---

## Performance Tips

1. **Pagination:** Load investments and profits in pages
2. **Image Optimization:** Compress images before upload
3. **Caching:** Cache pool data to reduce API calls
4. **Lazy Loading:** Load profit history only when needed
5. **Debouncing:** Debounce form inputs for better UX

---

## Security Considerations

1. **Token Storage:** Store auth tokens securely (Keychain/Keystore)
2. **HTTPS Only:** All API calls must use HTTPS
3. **Input Sanitization:** Sanitize all user inputs
4. **Sensitive Data:** Never log sensitive data (bank details, tokens)
5. **Certificate Pinning:** Consider SSL pinning for production
