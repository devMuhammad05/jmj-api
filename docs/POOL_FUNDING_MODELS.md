# Pool Funding Models Documentation

## Overview
This document outlines the database models created for the Pool Funding feature.

## Models Created

### 1. Pool Model
**Location:** `app/Models/Pool.php`

Represents a collective investment pool where multiple investors can contribute funds.

**Fields:**
- `id` (UUID) - Primary key
- `name` (string) - Pool identifier
- `total_amount` (decimal) - Current total pool capital
- `investor_count` (integer) - Number of active investors
- `last_return` (decimal) - Most recent return percentage
- `minimum_investment` (decimal) - Minimum contribution (default: $1,000)
- `status` (enum) - Pool status: active, closed, paused
- `timestamps` - created_at, updated_at

**Relationships:**
- `hasMany` PoolInvestment
- `hasMany` activeInvestments (filtered by status)

**Features:**
- UUID primary key
- Activity logging enabled
- Enum casting for status

---

### 2. PoolInvestment Model
**Location:** `app/Models/PoolInvestment.php`

Represents an individual investor's contribution to a pool.

**Fields:**
- `id` (UUID) - Primary key
- `user_id` (foreign key) - Reference to User
- `pool_id` (UUID foreign key) - Reference to Pool
- `full_name` (string) - Investor's full name
- `phone_number` (string) - Contact number
- `bank_name` (string) - Bank for profit disbursement
- `account_number` (string) - Bank account number
- `account_name` (string) - Name on bank account
- `contribution` (decimal) - Investment amount
- `share_percentage` (decimal) - Calculated share of total pool
- `payment_proof_path` (string) - Storage path for payment screenshot
- `status` (enum) - Investment status: pending, verified, active, rejected
- `terms_accepted` (boolean) - Terms and conditions acceptance
- `verified_at` (timestamp) - Admin verification date
- `rejection_reason` (text) - Reason for rejection if applicable
- `timestamps` - created_at, updated_at

**Relationships:**
- `belongsTo` User
- `belongsTo` Pool
- `hasMany` ProfitDistribution

**Features:**
- UUID primary key
- Activity logging enabled
- Enum casting for status
- Indexed on user_id, pool_id, and status

---

### 3. ProfitDistribution Model
**Location:** `app/Models/ProfitDistribution.php`

Tracks profit distributions to investors based on their pool share.

**Fields:**
- `id` (UUID) - Primary key
- `pool_investment_id` (UUID foreign key) - Reference to PoolInvestment
- `distribution_date` (date) - When profit was distributed
- `profit_amount` (decimal) - Amount sent to investor
- `pool_return` (decimal) - Overall pool return percentage
- `status` (enum) - Distribution status: pending, processed, failed
- `processed_at` (timestamp) - When distribution was processed
- `failure_reason` (text) - Reason for failure if applicable
- `timestamps` - created_at, updated_at

**Relationships:**
- `belongsTo` PoolInvestment

**Features:**
- UUID primary key
- Activity logging enabled
- Enum casting for status
- Indexed on pool_investment_id, distribution_date, and status

---

## Enums Created

### 1. PoolStatus
**Location:** `app/Enums/PoolStatus.php`

```php
- ACTIVE = 'active'
- CLOSED = 'closed'
- PAUSED = 'paused'
```

### 2. PoolInvestmentStatus
**Location:** `app/Enums/PoolInvestmentStatus.php`

```php
- PENDING = 'pending'
- VERIFIED = 'verified'
- ACTIVE = 'active'
- REJECTED = 'rejected'
```

### 3. ProfitDistributionStatus
**Location:** `app/Enums/ProfitDistributionStatus.php`

```php
- PENDING = 'pending'
- PROCESSED = 'processed'
- FAILED = 'failed'
```

---

## Migrations Created

1. **2026_03_08_200016_create_pools_table.php**
   - Creates pools table with UUID primary key
   - Sets default minimum_investment to 1000
   - Sets default status to 'active'

2. **2026_03_08_200121_create_pool_investments_table.php**
   - Creates pool_investments table with UUID primary key
   - Foreign keys to users and pools tables with cascade delete
   - Indexes on user_id, pool_id, and status
   - Sets default status to 'pending'

3. **2026_03_08_200419_create_profit_distributions_table.php**
   - Creates profit_distributions table with UUID primary key
   - Foreign key to pool_investments table with cascade delete
   - Indexes on pool_investment_id, distribution_date, and status
   - Sets default status to 'pending'

---

## User Model Update

Added relationship to User model:
```php
public function poolInvestments(): HasMany
{
    return $this->hasMany(PoolInvestment::class);
}
```

---

## Database Schema

```
users
  └─ pool_investments (user_id)
       ├─ pools (pool_id)
       └─ profit_distributions (pool_investment_id)
```

---

## Key Features

1. **UUID Primary Keys:** All pool-related tables use UUIDs for better security and scalability
2. **Activity Logging:** All models use Spatie Activity Log for audit trails
3. **Cascade Deletes:** Foreign keys configured to cascade on delete
4. **Enum Type Safety:** Status fields use PHP enums for type safety
5. **Decimal Precision:** Financial fields use appropriate decimal precision
6. **Indexes:** Strategic indexes on frequently queried columns
7. **Soft Validation:** Boolean flags and nullable fields for flexible workflows

---

## Next Steps

To complete the Pool Funding feature, you'll need to create:

1. **DTOs** (Data Transfer Objects)
   - PoolData.php
   - PoolInvestmentData.php
   - ProfitDistributionData.php

2. **Actions**
   - CreatePoolAction.php
   - CreatePoolInvestmentAction.php
   - VerifyPoolInvestmentAction.php
   - CalculateSharePercentageAction.php
   - DistributeProfitAction.php

3. **Controllers**
   - PoolController.php
   - PoolInvestmentController.php
   - ProfitDistributionController.php

4. **Requests**
   - StorePoolRequest.php
   - StorePoolInvestmentRequest.php
   - VerifyPoolInvestmentRequest.php

5. **Resources**
   - PoolResource.php
   - PoolInvestmentResource.php
   - ProfitDistributionResource.php

6. **Filament Resources** (Admin Panel)
   - PoolResource.php
   - PoolInvestmentResource.php
   - ProfitDistributionResource.php
