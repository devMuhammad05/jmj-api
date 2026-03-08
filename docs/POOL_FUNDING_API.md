# Pool Funding API Documentation

## Overview
This document provides comprehensive API documentation for the Pool Funding feature client-side implementation.

---

## Authentication
All endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

---

## Endpoints

### 1. List Active Pools

Get all active investment pools.

**Endpoint:** `GET /api/v1/pools`

**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "Active pools retrieved successfully",
  "data": [
    {
      "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
      "name": "Main Trading Pool",
      "total_amount": "45000.00",
      "investor_count": 23,
      "last_return": "15.20",
      "minimum_investment": "1000.00",
      "status": "active",
      "created_at": "2026-03-01 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

---

### 2. Get Pool Details

Get detailed information about a specific pool.

**Endpoint:** `GET /api/v1/pools/{pool_id}`

**Authentication:** Required

**Parameters:**
- `pool_id` (UUID) - Pool identifier

**Response:**
```json
{
  "success": true,
  "message": "Pool details retrieved successfully",
  "data": {
    "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
    "name": "Main Trading Pool",
    "total_amount": "45000.00",
    "investor_count": 23,
    "last_return": "15.20",
    "minimum_investment": "1000.00",
    "status": "active",
    "created_at": "2026-03-01 10:00:00"
  }
}
```

---

### 3. Submit Pool Investment Application

Submit an application to join a pool with investment details.

**Endpoint:** `POST /api/v1/pool-investments`

**Authentication:** Required

**Request Body:**
```json
{
  "pool_id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
  "full_name": "John Doe",
  "phone_number": "+234 XXX XXX XXXX",
  "bank_name": "GTBank",
  "account_number": "0123456789",
  "account_name": "John Doe",
  "contribution": 1000.00,
  "payment_proof_path": "https://storage.example.com/proofs/payment123.jpg",
  "terms_accepted": true
}
```

**Validation Rules:**
- `pool_id`: Required, must be a valid UUID, must exist in pools table
- `full_name`: Required, string, max 255 characters
- `phone_number`: Required, string, max 20 characters
- `bank_name`: Required, string, max 255 characters
- `account_number`: Required, string, max 20 characters
- `account_name`: Required, string, max 255 characters
- `contribution`: Required, numeric, minimum 1000
- `payment_proof_path`: Required, must be a valid URL
- `terms_accepted`: Required, boolean, must be true

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Pool investment application submitted successfully. Payment verification will take 24-48 hours.",
  "data": {
    "id": "8c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6f",
    "pool": {
      "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
      "name": "Main Trading Pool",
      "total_amount": "45000.00",
      "investor_count": 23,
      "last_return": "15.20",
      "minimum_investment": "1000.00",
      "status": "active",
      "created_at": "2026-03-01 10:00:00"
    },
    "full_name": "John Doe",
    "phone_number": "+234 XXX XXX XXXX",
    "contribution": "1000.00",
    "share_percentage": "0.0000",
    "status": "pending",
    "terms_accepted": true,
    "verified_at": null,
    "submitted_at": "2026-03-08 20:30:00",
    "updated_at": "2026-03-08 20:30:00"
  }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "contribution": ["The minimum investment amount is $1,000."],
    "terms_accepted": ["You must accept the terms and conditions."]
  }
}
```

---

### 4. List User's Pool Investments

Get all pool investments for the authenticated user.

**Endpoint:** `GET /api/v1/pool-investments`

**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "Pool investments retrieved successfully",
  "data": [
    {
      "id": "8c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6f",
      "pool": {
        "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
        "name": "Main Trading Pool",
        "total_amount": "45000.00",
        "investor_count": 23,
        "last_return": "15.20",
        "minimum_investment": "1000.00",
        "status": "active",
        "created_at": "2026-03-01 10:00:00"
      },
      "full_name": "John Doe",
      "phone_number": "+234 XXX XXX XXXX",
      "contribution": "1000.00",
      "share_percentage": "2.2222",
      "status": "active",
      "terms_accepted": true,
      "verified_at": "2026-03-09 10:00:00",
      "submitted_at": "2026-03-08 20:30:00",
      "updated_at": "2026-03-09 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

---

### 5. Get Pool Investment Details

Get detailed information about a specific pool investment.

**Endpoint:** `GET /api/v1/pool-investments/{investment_id}`

**Authentication:** Required

**Parameters:**
- `investment_id` (UUID) - Pool investment identifier

**Authorization:** User can only view their own investments

**Response:**
```json
{
  "success": true,
  "message": "Pool investment details retrieved successfully",
  "data": {
    "id": "8c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6f",
    "pool": {
      "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
      "name": "Main Trading Pool",
      "total_amount": "45000.00",
      "investor_count": 23,
      "last_return": "15.20",
      "minimum_investment": "1000.00",
      "status": "active",
      "created_at": "2026-03-01 10:00:00"
    },
    "full_name": "John Doe",
    "phone_number": "+234 XXX XXX XXXX",
    "contribution": "1000.00",
    "share_percentage": "2.2222",
    "status": "active",
    "terms_accepted": true,
    "verified_at": "2026-03-09 10:00:00",
    "submitted_at": "2026-03-08 20:30:00",
    "updated_at": "2026-03-09 10:00:00"
  }
}
```

**Error Response (403 Forbidden):**
```json
{
  "success": false,
  "message": "You are not authorized to view this investment."
}
```

---

### 6. List User's Profit Distributions

Get all profit distributions for the authenticated user.

**Endpoint:** `GET /api/v1/profit-distributions`

**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "Profit distributions retrieved successfully",
  "data": [
    {
      "id": "7b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5e",
      "distribution_date": "2026-03-15",
      "profit_amount": "152.00",
      "pool_return": "15.20",
      "status": "processed",
      "processed_at": "2026-03-15 14:30:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

---

### 7. Get Profit Distribution Details

Get detailed information about a specific profit distribution.

**Endpoint:** `GET /api/v1/profit-distributions/{distribution_id}`

**Authentication:** Required

**Parameters:**
- `distribution_id` (UUID) - Profit distribution identifier

**Authorization:** User can only view their own distributions

**Response:**
```json
{
  "success": true,
  "message": "Profit distribution details retrieved successfully",
  "data": {
    "id": "7b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5e",
    "distribution_date": "2026-03-15",
    "profit_amount": "152.00",
    "pool_return": "15.20",
    "status": "processed",
    "processed_at": "2026-03-15 14:30:00"
  }
}
```

**Error Response (403 Forbidden):**
```json
{
  "success": false,
  "message": "You are not authorized to view this distribution."
}
```

---

## Investment Status Flow

```
pending → verified → active
   ↓
rejected
```

**Status Descriptions:**
- `pending`: Application submitted, awaiting admin verification
- `verified`: Payment verified by admin, calculating share percentage
- `active`: Investment is active and participating in pool trading
- `rejected`: Application rejected by admin (includes rejection_reason)

---

## Profit Distribution Status Flow

```
pending → processed
   ↓
failed
```

**Status Descriptions:**
- `pending`: Profit calculated, awaiting transfer
- `processed`: Profit successfully transferred to bank account
- `failed`: Transfer failed (includes failure_reason)

---

## Business Logic

### Share Percentage Calculation
When an investment is verified and becomes active:
```
share_percentage = (investor_contribution / total_pool_amount) * 100
```

### Profit Distribution Calculation
When profits are distributed:
```
investor_profit = (pool_total_profit * share_percentage) / 100
```

---

## Security Notes

1. **Bank Details Protection:** Bank account information is never exposed in API responses (only stored for admin use)
2. **Payment Proof:** Payment proof URLs are stored but not returned in client responses
3. **Authorization:** Users can only access their own investments and distributions
4. **Minimum Investment:** Enforced at validation level ($1,000 minimum)
5. **Terms Acceptance:** Required and validated before submission

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## Rate Limiting

API endpoints are rate-limited to prevent abuse. Default limits apply per authenticated user.

---

## Example Client Implementation

### JavaScript/TypeScript Example

```typescript
// Submit Pool Investment
async function submitPoolInvestment(data: PoolInvestmentData) {
  const response = await fetch('/api/v1/pool-investments', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify(data)
  });
  
  return await response.json();
}

// Get User's Investments
async function getMyInvestments() {
  const response = await fetch('/api/v1/pool-investments', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  return await response.json();
}

// Get Profit Distributions
async function getMyProfits() {
  const response = await fetch('/api/v1/profit-distributions', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  return await response.json();
}
```

---

## Testing

Use the following test data for development:

```json
{
  "pool_id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
  "full_name": "Test User",
  "phone_number": "+234 123 456 7890",
  "bank_name": "Test Bank",
  "account_number": "0123456789",
  "account_name": "Test User",
  "contribution": 1000.00,
  "payment_proof_path": "https://example.com/proof.jpg",
  "terms_accepted": true
}
```
