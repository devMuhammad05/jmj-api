# JMJ API Documentation

This repository contains the backend API for the **JMJ Trading Platform** — a system that bridges investors and professional traders via MetaTrader 4/5 account management.

The API follows RESTful principles and returns JSON responses.

## Base URL

```
http://localhost:8000/api/v1
```

## Authentication

All protected routes require a **Laravel Sanctum** Bearer token in the `Authorization` header:

```
Authorization: Bearer <your-token>
```

You receive a token on successful `/auth/register` or `/auth/login`.

---

## Endpoints Overview

| Method | Endpoint                      | Auth | Description                        |
| ------ | ----------------------------- | ---- | ---------------------------------- |
| GET    | `/`                           | No   | Health check                       |
| POST   | `/auth/register`              | No   | Register new user (sends OTP)      |
| POST   | `/auth/verify-registration-otp` | No | Verify OTP to complete registration |
| POST   | `/auth/send-otp`              | No   | Resend OTP to email                |
| GET    | `/auth/get-otp`               | No   | Get OTP for testing (dev only)     |
| POST   | `/auth/login`                 | No   | Login user (requires verified email) |
| POST   | `/auth/logout`                | 🔒   | Logout user                        |
| GET    | `/auth/me`                    | 🔒   | Get authenticated user profile     |
| PUT    | `/auth/profile`               | 🔒   | Update user profile                |
| POST   | `/auth/pin/setup`             | 🔒   | Setup 4-digit security PIN         |
| POST   | `/auth/pin/verify`            | 🔒   | Verify security PIN                |
| POST   | `/auth/pin/change`            | 🔒   | Change existing security PIN       |
| POST   | `/auth/pin/reset`             | 🔒   | Reset PIN using account password   |
| GET    | `/plans`                      | No   | List all active plans grouped by type      |
| GET    | `/rates`                      | No   | List all exchange rates            |
| GET    | `/rates/{key}`                | No   | Get specific rate by key           |
| GET    | `/signals`                    | No   | Get signals (filtered by subscription tier) |
| GET    | `/signals/active`             | No   | Get active signals (filtered by subscription tier) |
| GET    | `/signals/statistics`         | No   | Get signal performance statistics  |
| GET    | `/signals/{id}`               | No   | Get specific signal details        |
| GET    | `/verifications`              | 🔒   | Get KYC verification status        |
| POST   | `/verifications`              | 🔒   | Submit KYC documents               |
| POST   | `/metatrader-credentials`     | 🔒   | Store MetaTrader credentials       |
| GET    | `/pools`                      | 🔒   | Get all active investment pools    |
| GET    | `/pools/{id}`                 | 🔒   | Get specific pool details          |
| GET    | `/pool-investments`           | 🔒   | Get user's pool investments        |
| POST   | `/pool-investments`           | 🔒   | Submit pool investment application |
| GET    | `/pool-investments/{id}`      | 🔒   | Get specific investment details    |
| GET    | `/profit-distributions`       | 🔒   | Get user's profit distributions    |
| GET    | `/profit-distributions/{id}`  | 🔒   | Get specific distribution details  |
| GET    | `/trading-classes`            | 🔒   | Get trading classes (filtered by subscription tier) |
| GET    | `/trading-classes/{id}`       | 🔒   | Get specific trading class (403 if paid & no subscription) |
| GET    | `/trading-stats`              | 🔒   | Get authenticated user trading stats |
| GET    | `/client-portfolio`           | 🔒   | Get top 3 clients by balance       |
| GET    | `/payment-gateways`           | 🔒   | List active payment gateways       |
| POST   | `/subscribe`                  | 🔒   | Submit a subscription request      |
| GET    | `/payments`                   | 🔒   | List user's payments               |
| GET    | `/payments/{id}`              | 🔒   | Get a specific payment             |
| GET    | `/subscriptions/current`      | 🔒   | Get current active subscription    |
| GET    | `/subscriptions`              | 🔒   | List subscription history          |
| GET    | `/notifications`              | 🔒   | List user notifications (paginated) |
| GET    | `/notifications/unread-count` | 🔒   | Get unread notification count      |
| POST   | `/notifications/{id}/read`    | 🔒   | Mark a notification as read        |
| POST   | `/notifications/read-all`     | 🔒   | Mark all notifications as read     |

---

## Detailed Endpoints

### Health Check

#### Check API Status

`GET /`

Returns a simple message confirming the API is running.

**Response:**

```json
"API is active"
```

---

### 1. Authentication — `/auth`

#### 1.1 Register

`POST /auth/register`

Create a new user account.

**Request Body:**

| Field                   | Type   | Required | Notes                 |
| ----------------------- | ------ | -------- | --------------------- |
| `full_name`             | string | ✓        |                       |
| `email`                 | string | ✓        | Must be unique        |
| `password`              | string | ✓        | Min 8 characters      |
| `password_confirmation` | string | ✓        | Must match `password` |
| `country`               | string | —        | Optional              |

**Response:**

```json
{
    "status": "success",
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "email": "john@example.com",
            "country": "Nigeria",
            "created_at": "2026-03-08T10:00:00.000000Z",
            "updated_at": "2026-03-08T10:00:00.000000Z"
        },
        "access_token": "1|abc123...",
        "token_type": "Bearer",
        "pin_configured": false
    }
}
```

---

#### 1.2 Verify Registration OTP

`POST /auth/verify-registration-otp`

Verify the OTP sent to the user's email after registration to complete email verification.

**Request Body:**

| Field   | Type    | Required | Notes                |
| ------- | ------- | -------- | -------------------- |
| `email` | string  | ✓        | Valid email address  |
| `otp`   | integer | ✓        | 6-digit OTP          |

**Response:**

```json
{
    "status": "success",
    "message": "OTP verified successfully. You can now login."
}
```

**Error Responses:**

- `404`: User not found
- `422`: Invalid OTP provided

---

#### 1.3 Send OTP

`POST /auth/send-otp`

Resend a verification OTP to the user's email address.

**Request Body:**

| Field   | Type   | Required | Notes               |
| ------- | ------ | -------- | ------------------- |
| `email` | string | ✓        | Valid email address |

**Response:**

```json
{
    "status": "success",
    "message": "OTP has been sent to your email."
}
```

**Error Responses:**

- `404`: No account found with the provided email
- `500`: Failed to send OTP

---

#### 1.4 Get OTP (Development Only)

`GET /auth/get-otp`

Retrieve the OTP stored in cache for a given email. **For development and testing purposes only — must not be exposed in production.**

**Query Parameters:**

| Field   | Type   | Required | Notes               |
| ------- | ------ | -------- | ------------------- |
| `email` | string | ✓        | Valid email address |

**Response:**

```json
{
    "status": "success",
    "message": "OTP retrieved successfully.",
    "data": {
        "email": "john@example.com",
        "otp": "123456"
    }
}
```

**Error Responses:**

- `404`: No OTP found for the given email (not yet sent or already expired)
- `422`: Validation error (missing or invalid email)

---

#### 1.5 Login

`POST /auth/login`

Authenticate an existing user.

**Request Body:**

| Field      | Type   | Required |
| ---------- | ------ | -------- |
| `email`    | string | ✓        |
| `password` | string | ✓        |

**Response:**

```json
{
    "status": "success",
    "message": "User logged in successfully",
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "email": "john@example.com",
            "country": "Nigeria",
            "created_at": "2026-03-08T10:00:00.000000Z",
            "updated_at": "2026-03-08T10:00:00.000000Z"
        },
        "access_token": "2|xyz789...",
        "token_type": "Bearer",
        "pin_configured": true
    }
}
```

---

#### 1.6 Logout

`POST /auth/logout` 🔒

Revoke the current access token and log out the user.

**Response:**

```json
{
    "status": "success",
    "message": "Logged out successfully",
    "data": []
}
```

---

#### 1.7 Get Profile

`GET /auth/me` 🔒

Retrieve the authenticated user's profile information.

**Response:**

```json
{
    "status": "success",
    "message": "User profile retrieved successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "phone_number": "+234123456789",
        "country": "Nigeria",
        "created_at": "2026-03-08T10:00:00.000000Z",
        "updated_at": "2026-03-08T10:00:00.000000Z"
    }
}
```

---

#### 1.8 Update Profile

`PUT /auth/profile` 🔒

Update the authenticated user's profile information.

**Request Body:**

| Field          | Type   | Required | Notes          |
| -------------- | ------ | -------- | -------------- |
| `full_name`    | string | —        |                |
| `email`        | string | —        | Must be unique |
| `phone_number` | string | —        |                |
| `country`      | string | —        |                |

**Response:**

```json
{
    "status": "success",
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe Updated",
        "email": "john.updated@example.com",
        "phone_number": "+234123456789",
        "country": "Nigeria",
        "created_at": "2026-03-08T10:00:00.000000Z",
        "updated_at": "2026-03-08T11:00:00.000000Z"
    }
}

---

#### 1.9 PIN Management

All PIN endpoints are throttled to prevent brute-force attacks.

##### 1.9.1 Setup PIN

`POST /auth/pin/setup` 🔒

Configure a new 4-digit security PIN. This can only be done if a PIN is not already set.

**Request Body:**

| Field              | Type    | Required | Notes            |
| ------------------ | ------- | -------- | ---------------- |
| `pin`              | integer | ✓        | 4 digits         |
| `pin_confirmation` | integer | ✓        | Must match `pin` |

**Response:**

```json
{
    "status": "success",
    "message": "PIN set up successfully.",
    "data": []
}
```

---

##### 1.7.2 Verify PIN

`POST /auth/pin/verify` 🔒

Verify the user's security PIN. 5 failed attempts will lock the PIN for 30 minutes.

**Request Body:**

| Field | Type    | Required | Notes    |
| ----- | ------- | -------- | -------- |
| `pin` | integer | ✓        | 4 digits |

**Response:**

```json
{
    "status": "success",
    "message": "PIN verified successfully.",
    "data": []
}
```

---

##### 1.7.3 Change PIN

`POST /auth/pin/change` 🔒

Change an existing security PIN. Requires the current PIN.

**Request Body:**

| Field              | Type    | Required | Notes                             |
| ------------------ | ------- | -------- | --------------------------------- |
| `current_pin`      | integer | ✓        | 4 digits                          |
| `pin`              | integer | ✓        | 4 digits, different from current |
| `pin_confirmation` | integer | ✓        | Must match `pin`                  |

**Response:**

```json
{
    "status": "success",
    "message": "PIN changed successfully.",
    "data": []
}
```

---

##### 1.7.4 Reset PIN

`POST /auth/pin/reset` 🔒

Reset the security PIN using the account password. Useful if the user has forgotten their PIN or it is locked.

**Request Body:**

| Field              | Type    | Required | Notes            |
| ------------------ | ------- | -------- | ---------------- |
| `password`         | string  | ✓        | Account password |
| `pin`              | integer | ✓        | 4 digits         |
| `pin_confirmation` | integer | ✓        | Must match `pin` |

**Response:**

```json
{
    "status": "success",
    "message": "PIN reset successfully.",
    "data": []
}
```

---

### 2. KYC Verification — `/verifications`

> 🔒 All routes require authentication.

#### 2.1 Get Verification Status

`GET /verifications`

Retrieve the current user's KYC verification status and timestamps. Does **not** return submitted document data for security reasons.

**Response:**

```json
{
    "status": "success",
    "message": "Verification status retrieved successfully",
    "data": {
        "status": "pending",
        "submitted_at": "2026-03-07 06:00:00",
        "updated_at": "2026-03-07 06:00:00"
    }
}
```

**Possible Status Values:**
- `pending` — Documents submitted, awaiting review
- `approved` — KYC verification approved
- `rejected` — KYC verification rejected

---

#### 2.2 Submit KYC Documents

`POST /verifications`

Submit KYC verification documents for review.

**Request Body:**

| Field                   | Type         | Required | Notes                                                       |
| ----------------------- | ------------ | -------- | ----------------------------------------------------------- |
| `id_type`               | string       | ✓        | `national_id`, `passport`, `driving_license`, `voters_card` |
| `id_number`             | string       | ✓        | Max 50 characters                                           |
| `id_card_front_img_url` | string (URL) | ✓        | URL to front of ID card image                               |
| `id_card_back_img_url`  | string (URL) | —        | URL to back of ID card image (optional)                     |
| `selfie_img_url`        | string (URL) | ✓        | URL to selfie image                                         |

> ⚠️ **Important:** A user may only submit once unless their status is `rejected`. Re-submission while `pending` or `approved` returns a `403 Forbidden` error.

**Response:**

```json
{
    "status": "success",
    "message": "Verification submitted successfully",
    "data": {
        "status": "pending",
        "submitted_at": "2026-03-08 12:00:00",
        "updated_at": "2026-03-08 12:00:00"
    }
}
```

---

### 3. MetaTrader Credentials — `/metatrader-credentials`

> 🔒 All routes require authentication.

#### 3.1 Store MetaTrader Credentials

`POST /metatrader-credentials`

Link an investor's MT4/MT5 broker account to the platform. This allows traders to manage the account.

**Request Body:**

| Field               | Type    | Required | Notes                                    |
| ------------------- | ------- | -------- | ---------------------------------------- |
| `mt_account_number` | string  | ✓        | MetaTrader account number, max 50 chars  |
| `mt_password`       | string  | ✓        | MetaTrader account password, max 50 chars|
| `mt_server`         | string  | ✓        | Broker server name, max 100 characters   |
| `platform_type`     | string  | ✓        | `mt4` or `mt5`                           |
| `initial_deposit`   | numeric | ✓        | Initial deposit amount, min `0`          |
| `risk_level`        | string  | ✓        | `conservative`, `moderate`               |
 `amount_paid`          | numeric | ✓        | Total amount paid (including fees)        |
| `payment_gateway_id`   | integer | ✓        | ID of the payment gateway used            |
| `payment_proof_url`    | URL     | ✓        | URL to payment


**Response:**

```json
{
    "status": "success",
    "message": "MetaTrader credentials saved successfully",
    "data": []
}
```

---

### 4. Trading Stats — `/trading-stats`

> 🔒 Requires authentication.

#### 4.1 Get Trading Stats

`GET /trading-stats` 🔒

Returns the latest cached trading metrics for the authenticated user's MetaTrader account. Simultaneously dispatches a background job to refresh the metrics from the fast backend, so subsequent requests will reflect fresher data.

**Response (stats available):**

```json
{
    "status": "success",
    "message": "Trading stats retrieved successfully",
    "data": {
        "balance": "25000.00",
        "equity": "25320.00",
        "profit": "3200.00",
        "deposits": "22000.00",
        "withdrawals": "0.00",
        "margin": "500.00",
        "free_margin": "24820.00",
        "trades": 42,
        "profit_factor": "1.85",
        "sharpe_ratio": "1.20",
        "won_trades_percent": "62.50",
        "lost_trades_percent": "37.50",
        "daily_growth": []
    }
}
```

**Response (no account linked):**

```json
{
    "status": "success",
    "message": "No MetaTrader account found",
    "data": []
}
```

**Response (no metrics yet):**

```json
{
    "status": "success",
    "message": "No trading stats available yet",
    "data": []
}
```

---

### 5. Client Portfolio — `/client-portfolio`

> 🔒 Requires authentication.

#### 5.1 Get Client Portfolio

`GET /client-portfolio` 🔒

Returns the top 3 clients ordered by account balance (descending). Each entry includes the client's name, risk level, balance, profit, and profit percentage calculated as `(profit / deposits) * 100`.

If no MetaAccount metrics exist yet, a dummy sample of 3 placeholder entries is returned so the UI always has data to display.

**Response:**

```json
{
    "status": "success",
    "message": "Client portfolio retrieved successfully",
    "data": [
        {
            "name": "Alex Johnson",
            "risk_level": "moderate",
            "balance": "25000.00",
            "profit": "3200.00",
            "profit_percent": 14.67
        },
        {
            "name": "Sarah Williams",
            "risk_level": "conservative",
            "balance": "18500.00",
            "profit": "1480.00",
            "profit_percent": 8.70
        },
        {
            "name": "James Carter",
            "risk_level": "moderate",
            "balance": "12000.00",
            "profit": "960.00",
            "profit_percent": 8.70
        }
    ]
}
```

**Notes:**
- Always returns at most 3 entries.
- `profit_percent` defaults to `0` when `deposits` is zero.
- When no real metrics exist the dummy response shape is identical, making frontend integration seamless.

---

### 6. Plans — `/plans`

#### 6.1 List All Active Plans

`GET /api/v1/plans`

Returns all active plans grouped by type. Pass an optional `type` query parameter to filter to a single group.

**Query Parameters:**

| Parameter | Type   | Required | Description                                      |
|-----------|--------|----------|--------------------------------------------------|
| `type`    | string | —        | Filter by type: `signals` or `trading_classes`   |

**Response:**

```json
{
  "status": "success",
  "message": "Plans retrieved successfully",
  "data": {
    "signals": [
      {
        "id": 1,
        "name": "Signals PRO",
        "slug": "signals-pro",
        "type": "signals",
        "level": 2,
        "price": "29.99",
        "duration_days": 30
      },
      {
        "id": 2,
        "name": "Signals VIP",
        "slug": "signals-vip",
        "type": "signals",
        "level": 3,
        "price": "199.99",
        "duration_days": 365
      }
    ],
    "trading_classes": [
      {
        "id": 3,
        "name": "Trading Classes PRO",
        "slug": "trading-pro",
        "type": "trading_classes",
        "level": 2,
        "price": "29.99",
        "duration_days": 30
      },
      {
        "id": 4,
        "name": "Trading Classes VIP",
        "slug": "trading-vip",
        "type": "trading_classes",
        "level": 3,
        "price": "199.99",
        "duration_days": 365
      }
    ]
  }
}
```

---

### 7. Rates — `/rates`

> ℹ️ Rate routes are **public** — no authentication required.

#### 7.1 List All Rates

`GET /api/v1/rates`

Returns all available exchange rates.

**Response:**

```json
{
  "status": "success",
  "message": "Rates retrieved successfully",
  "data": [
    {
      "id": 1,
      "key": "dollar",
      "value": 1650.00,
      "updated_at": "2026-04-24T22:16:55.000000Z"
    }
  ]
}
```

**Error Response (500):**

```json
{
  "status": "error",
  "message": "Error message",
  "data": null
}
```

#### 7.2 Get Specific Rate by Key

`GET /api/v1/rates/{key}`

Retrieve a specific rate by its key identifier.

**URL Parameters:**

| Parameter | Type   | Required | Description           |
|-----------|--------|----------|-----------------------|
| `key`     | string | ✓        | The rate key (e.g., `dollar`, `euro`) |

**Response:**

```json
{
  "status": "success",
  "message": "Rate retrieved successfully",
  "data": {
    "id": 1,
    "key": "dollar",
    "value": 1650.00,
    "updated_at": "2026-04-24T22:16:55.000000Z"
  }
}
```

**Error Response (404):**

```json
{
  "status": "error",
  "message": "Rate not found",
  "data": null
}
```

---

### 8. Trading Signals — `/signals`

> ℹ️ Signal routes are **public** — no authentication required.
> Content is filtered by subscription tier:
> - **No subscription / unauthenticated** — only signals marked `is_free = true` are returned.
> - **Active Signals subscription** — free signals + all signals assigned to the user's plan.

#### 4.1 Get All Signals

`GET /signals`

Retrieve a paginated list of published trading signals.

**Query Parameters:**

| Parameter    | Type   | Required | Description                                      |
| ------------ | ------ | -------- | ------------------------------------------------ |
| `status`     | string | —        | Filter by status: `active`, `tp`, `sl`, `closed` |
| `symbol`     | string | —        | Filter by symbol (e.g., `EURUSD`, `XAUUSD`)      |
| `action`     | string | —        | Filter by action: `buy`, `sell`, etc.            |
| `type`       | string | —        | Filter by type: `forex`, `crypto`, `commodities` |
| `per_page`   | number | —        | Results per page (default: 15)                   |
| `include_all`| bool   | —        | Include all statuses (default: active only)      |

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "symbol": "EURUSD",
            "action": "buy",
            "type": "forex",
            "entry_price": "1.09500",
            "stop_loss": "1.09200",
            "take_profit_1": "1.10000",
            "take_profit_2": "1.10500",
            "take_profit_3": null,
            "status": "active",
            "pips_result": null,
            "notes": "Strong bullish momentum on H4 timeframe",
            "is_published": true,
            "published_at": "2026-03-08T10:00:00+00:00",
            "created_at": "2026-03-08T10:00:00+00:00",
            "updated_at": "2026-03-08T10:00:00+00:00"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/v1/signals?page=1",
        "last": "http://localhost:8000/api/v1/signals?page=3",
        "prev": null,
        "next": "http://localhost:8000/api/v1/signals?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "per_page": 15,
        "to": 15,
        "total": 42
    }
}
```

---

#### 4.2 Get Active Signals Only

`GET /signals/active`

Retrieve only signals with `active` status.

**Query Parameters:**

| Parameter  | Type   | Required | Description                                 |
| ---------- | ------ | -------- | ------------------------------------------- |
| `symbol`   | string | —        | Filter by symbol (e.g., `EURUSD`, `XAUUSD`) |
| `type`     | string | —        | Filter by type: `forex`, `crypto`, etc.     |
| `per_page` | number | —        | Results per page (default: 15)              |

**Response:**

```json
{
    "data": [
        {
            "id": 5,
            "symbol": "XAUUSD",
            "action": "sell",
            "type": "commodities",
            "entry_price": "2050.00",
            "stop_loss": "2060.00",
            "take_profit_1": "2030.00",
            "take_profit_2": "2020.00",
            "take_profit_3": "2010.00",
            "status": "active",
            "pips_result": null,
            "notes": "Gold showing bearish divergence",
            "is_published": true,
            "published_at": "2026-03-08T12:00:00+00:00",
            "created_at": "2026-03-08T12:00:00+00:00",
            "updated_at": "2026-03-08T12:00:00+00:00"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

---

#### 4.3 Get Signal Statistics

`GET /signals/statistics`

Retrieve performance statistics for all published signals.

**Response:**

```json
{
    "status": "success",
    "message": "Signal statistics retrieved successfully",
    "data": {
        "total_signals": 42,
        "active_signals": 5,
        "tp_signals": 28,
        "sl_signals": 7,
        "total_pips": 1250.50,
        "average_pips": 35.73,
        "win_rate": 80.00
    }
}
```

**Statistics Explained:**
- `total_signals`: Total number of published signals
- `active_signals`: Currently active signals
- `tp_signals`: Signals that hit take profit
- `sl_signals`: Signals that hit stop loss
- `total_pips`: Sum of all pips results (positive and negative)
- `average_pips`: Average pips per closed signal
- `win_rate`: Percentage of signals that hit TP (TP / (TP + SL + Closed) × 100)

---

#### 4.4 Get Specific Signal

`GET /signals/{id}`

Retrieve details of a specific signal by ID.

**Response:**

```json
{
    "status": "success",
    "message": "Signal retrieved successfully",
    "data": {
        "id": 1,
        "symbol": "EURUSD",
        "action": "buy",
        "type": "forex",
        "entry_price": "1.09500",
        "stop_loss": "1.09200",
        "take_profit_1": "1.10000",
        "take_profit_2": "1.10500",
        "take_profit_3": null,
        "status": "tp",
        "pips_result": 50.00,
        "notes": "Perfect execution. Hit TP1 within 4 hours.",
        "is_published": true,
        "published_at": "2026-03-07T10:00:00+00:00",
        "created_at": "2026-03-07T10:00:00+00:00",
        "updated_at": "2026-03-07T14:00:00+00:00"
    }
}
```

**Signal Status Values:**
- `active`: Signal is currently active
- `tp`: Signal hit take profit
- `sl`: Signal hit stop loss
- `closed`: Signal manually closed
- `cancelled`: Signal cancelled

**Signal Action Values:**
- `buy`: Market buy order
- `sell`: Market sell order
- `buy_limit`: Buy limit order
- `sell_limit`: Sell limit order
- `buy_stop`: Buy stop order
- `sell_stop`: Sell stop order

**Signal Type Values:**
- `forex`: Foreign exchange pairs
- `crypto`: Cryptocurrency pairs
- `commodities`: Gold, Silver, Oil, etc.
- `indices`: Stock market indices
- `stocks`: Individual stocks

---

### 9. Pool Funding — `/pools`, `/pool-investments`, `/profit-distributions`

> 🔒 All routes require authentication.
> ℹ️ **No KYC Required**: Users do NOT need to complete KYC verification to invest in pools.

Pool Funding allows multiple investors to contribute to a collective investment pool where professional traders manage the funds. Profits are distributed proportionally based on each investor's contribution.

#### 5.1 Get Active Pools

`GET /pools` 🔒

Retrieve all active investment pools available for investment.

**Response:**

```json
{
    "status": "success",
    "message": "Active pools retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Conservative Growth Pool",
            "total_amount": "50000.00",
            "investor_count": 12,
            "minimum_investment": "100.00",
            "status": "active",
            "created_at": "2026-01-15 08:30:00"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

---

#### 5.2 Get Pool Details

`GET /pools/{id}` 🔒

Retrieve detailed information about a specific pool.

**Response:**

```json
{
    "status": "success",
    "message": "Pool details retrieved successfully",
    "data": {
        "id": 1,
        "name": "Conservative Growth Pool",
        "total_amount": "50000.00",
        "investor_count": 12,
        "minimum_investment": "100.00",
        "status": "active",
        "created_at": "2026-01-15 08:30:00"
    }
}
```

---

#### 5.3 Submit Pool Investment Application

`POST /pool-investments` 🔒

Submit an application to join an investment pool. Payment verification takes 24-48 hours.

**Request Body:**

| Field                  | Type    | Required | Notes                                    |
| ---------------------- | ------- | -------- | ---------------------------------------- |
| `pool_id`              | UUID    | ✓        | Must be a valid active pool              |
| `full_name`            | string  | ✓        | Investor's full legal name               |
| `phone_number`         | string  | ✓        | Contact number                           |
| `bank_name`            | string  | ✓        | Bank for profit disbursement             |
| `account_number`       | string  | ✓        | 10-digit bank account number             |
| `account_name`         | string  | ✓        | Name on bank account                     |
| `contribution`         | numeric | ✓        | Investment amount (minimum $1,000)       |
| `amount_paid`          | numeric | ✓        | Total amount paid (including fees)        |
| `payment_gateway_id`   | integer | ✓        | ID of the payment gateway used            |
| `payment_proof_url`    | URL     | ✓        | URL to payment screenshot                |
| `terms_accepted`       | boolean | ✓        | Must be `true`                           |
| `mt_account_number`    | string  |          | MetaTrader account number for pool trading |
| `mt_password`          | string  |          | Required when `mt_account_number` is set |
| `mt_server`            | string  |          | Required when `mt_account_number` is set (e.g. `Exness-MT5Real`) |
| `platform_type`        | string  |          | Required when `mt_account_number` is set. One of: `mt4`, `mt5` |
| `initial_deposit`      | numeric |          | Required when `mt_account_number` is set |
| `risk_level`           | string  |          | Required when `mt_account_number` is set. One of: `conservative`, `moderate` |

**Response:**

```json
{
    "status": "success",
    "message": "Investment application submitted successfully",
    "data": {
        "id": 15,
        "pool": {
            "id": 1,
            "name": "Conservative Growth Pool",
            "total_amount": "50000.00",
            "investor_count": 12,
            "minimum_investment": "100.00",
            "status": "active",
            "created_at": "2026-01-15 08:30:00"
        },
        "full_name": "John Doe",
        "phone_number": "+2348012345678",
        "contribution": "1000.00",
        "amount_paid": "1035.00",
        "share_percentage": "1.00",
        "payment_proof_url": "https://example.com/proof.jpg",
        "status": "pending",
        "terms_accepted": true,
        "verified_at": null,
        "submitted_at": "2026-03-08 14:20:00",
        "updated_at": "2026-03-08 14:20:00"
    }
}
```

> `pool_meta_trader_account` is only included in the response when `status` is `verified`. It is omitted for `pending` and `rejected` investments.

**Investment Status Values:**
- `pending` — Application submitted, awaiting admin verification
- `verified` — Payment verified by admin
- `active` — Investment is active and participating in pool trading
- `rejected` — Application rejected (includes rejection_reason)

---

#### 5.4 Get User's Pool Investments

`GET /pool-investments` 🔒

Retrieve all pool investments for the authenticated user.

**Response:**

```json
{
    "status": "success",
    "message": "Pool investments retrieved successfully",
    "data": [
        {
            "id": 15,
            "pool": {
                "id": 1,
                "name": "Conservative Growth Pool",
                "total_amount": "50000.00",
                "investor_count": 12,
                "minimum_investment": "100.00",
                "status": "active",
                "created_at": "2026-01-15 08:30:00"
            },
            "full_name": "John Doe",
            "phone_number": "+2348012345678",
            "contribution": "500.00",
            "share_percentage": "1.00",
            "status": "verified",
            "terms_accepted": true,
            "verified_at": "2026-03-10 10:00:00",
            "pool_meta_trader_account": {
                "mt_account_number": "123456",
                "mt_server": "Exness-MT5Real",
                "platform_type": "mt5",
                "risk_level": "moderate",
                "initial_deposit": "5000.00",
                "balance": "5250.00",
                "equity": "5300.00",
                "margin": "150.00"
            },
            "submitted_at": "2026-03-08 14:20:00",
            "updated_at": "2026-03-10 10:00:00"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

> `pool_meta_trader_account` is only present when `status` is `verified` and the pool has a linked MetaTrader credential. `balance`, `equity`, and `margin` are `null` if no metrics have been synced yet.

---

#### 5.5 Get Pool Investment Details

`GET /pool-investments/{id}` 🔒

Retrieve detailed information about a specific pool investment. Users can only view their own investments.

**Response:**

```json
{
    "status": "success",
    "message": "Pool investment details retrieved successfully",
    "data": {
        "id": 15,
        "pool": {...},
        "full_name": "John Doe",
        "phone_number": "+2348012345678",
        "contribution": "500.00",
        "share_percentage": "1.00",
        "status": "verified",
        "terms_accepted": true,
        "verified_at": "2026-03-10 10:00:00",
        "pool_meta_trader_account": {
            "mt_account_number": "123456",
            "mt_server": "Exness-MT5Real",
            "platform_type": "mt5",
            "risk_level": "moderate",
            "initial_deposit": "5000.00",
            "balance": "5250.00",
            "equity": "5300.00",
            "margin": "150.00"
        },
        "submitted_at": "2026-03-08 14:20:00",
        "updated_at": "2026-03-10 10:00:00"
    }
}
```

> `pool_meta_trader_account` is only present when `status` is `verified` and the pool has a linked MetaTrader credential. `balance`, `equity`, and `margin` are `null` if no metrics have been synced yet.

---

#### 5.6 Get User's Profit Distributions

`GET /profit-distributions` 🔒

Retrieve all profit distributions for the authenticated user.

**Response:**

```json
{
    "status": "success",
    "message": "Profit distributions retrieved successfully",
    "data": [
        {
            "id": 1,
            "distribution_date": "2026-02-28",
            "profit_amount": "1250.00",
            "pool_return": "50.00",
            "status": "processed",
            "processed_at": "2026-03-01 09:00:00"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

**Distribution Status Values:**
- `pending` — Profit calculated, awaiting transfer
- `processed` — Profit successfully transferred to bank account
- `failed` — Transfer failed (includes failure_reason)

---

#### 5.7 Get Profit Distribution Details

`GET /profit-distributions/{id}` 🔒

Retrieve detailed information about a specific profit distribution. Users can only view their own distributions.

**Response:**

```json
{
    "status": "success",
    "message": "Profit distribution details retrieved successfully",
    "data": {
        "id": 1,
        "distribution_date": "2026-02-28",
        "profit_amount": "1250.00",
        "pool_return": "50.00",
        "status": "processed",
        "processed_at": "2026-03-01 09:00:00"
    }
}
```

---

### 10. Trading Classes (Learning Hub) — `/trading-classes`

> 🔒 All routes require authentication.
> Content is filtered by subscription tier:
> - **No active Trading Classes subscription** — only classes marked `is_free = true` are returned.
> - **Active Trading Classes subscription** — free classes + all classes assigned to the user's plan.
> - Accessing a specific paid class without a subscription returns `403`.

Trading Classes (Learning Hub) allows administrators to post educational sessions, webinars, and workshops. Each class includes a schedule, a description, and a meeting link for platforms like Zoom or Telegram.

#### 6.1 Get All Published Classes

`GET /api/v1/trading-classes` 🔒

Retrieve a list of all trading classes that are marked as published, ordered by their scheduled date (newest first).

**Response:**

```json
{
  "status": "success",
  "message": "Trading classes retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Advanced Trading Strategies",
      "description": "Learn advanced techniques for swing trading and position sizing.",
      "scheduled_at": "2024-01-15 19:00:00",
      "formatted_date": "Jan 15, 2024",
      "formatted_time": "7:00 PM",
      "platform": "zoom",
      "platform_label": "Zoom",
      "meeting_link": "https://zoom.us/j/example-session-1",
      "created_at": "2024-03-10 23:30:00"
    }
  ]
}
```

#### 6.2 Get Specific Class Details

`GET /api/v1/trading-classes/{id}` 🔒

Retrieve detailed information about a specific trading class.

**Response:**

```json
{
  "status": "success",
  "message": "Trading class details retrieved successfully",
  "data": {
    "id": 1,
    "title": "Advanced Trading Strategies",
    "description": "Learn advanced techniques for swing trading and position sizing.",
    "scheduled_at": "2024-01-15 19:00:00",
    "formatted_date": "Jan 15, 2024",
    "formatted_time": "7:00 PM",
    "platform": "zoom",
    "platform_label": "Zoom",
    "meeting_link": "https://zoom.us/j/example-session-1",
    "created_at": "2024-03-10 23:30:00"
  }
}
```

**Error Responses:**

- 404: Class not found or is not published.

### 11. Payments & Subscriptions

#### 7.1 List Payment Gateways

`GET /api/v1/payment-gateways` 🔒

Returns all active payment gateways (bank accounts, wallets, etc.) the user can pay through.

**Response:**

```json
{
  "status": "success",
  "message": "Payment gateways retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Bank Transfer",
      "code": "bank_transfer",
      "wallet_address": null,
      "network": null,
      "bar_code_url": null,
      "bank_name": "Access Bank Nigeria",
      "account_name": "JMJ Investments Ltd",
      "account_number": "1234567890",
      "config": {}
    },
    {
      "id": 2,
      "name": "USDT TRC20",
      "code": "usdt_trc20",
      "wallet_address": "TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE",
      "network": "TRC20",
      "bar_code_url": "http://localhost:8000/storage/img/payment-gateway/qr-code.png",
      "bank_name": null,
      "account_name": null,
      "account_number": null,
      "config": {}
    }
  ]
}
```

**Gateway Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique payment gateway identifier |
| `name` | string | Human-readable gateway name (e.g., "Bank Transfer", "USDT TRC20") |
| `code` | string | Unique code identifier used in API requests (e.g., "bank_transfer") |
| `payment_type` | string | Type of payment: `crypto` or `bank` |
| `wallet_address` | string\|null | Cryptocurrency wallet address for crypto gateways |
| `network` | string\|null | Blockchain network (TRC20, ERC20, BEP20, BTC, SOL) |
| `bar_code_url` | string\|null | URL to QR code image for crypto payments |
| `bank_name` | string\|null | Bank name for bank transfer gateways |
| `account_name` | string\|null | Account holder name for bank transfers |
| `account_number` | string\|null | Bank account number |
| `config` | object | Additional configuration data |

---

#### 7.2 Subscribe (Single Entry Point)

`POST /api/v1/subscribe` 🔒

Creates a payment and records proof of payment in one atomic request. The amount is derived automatically from the selected plan — no client input needed. Subscription is activated only after an admin approves the payment in the admin panel.

A user may hold **two concurrent active subscriptions** — one for **Signals** and one for **Trading Classes** — since they are independent plan types. Subscribing to a second plan of the same type (e.g. Signals PRO while already on Signals VIP) is blocked with a `409`.

**Available Plans:**

| Slug | Name | Type |
|------|------|------|
| `signals-pro` | Signals PRO | signals |
| `signals-vip` | Signals VIP | signals |
| `trading-pro` | Trading Classes PRO | trading_classes |
| `trading-vip` | Trading Classes VIP | trading_classes |

**Request Body:**

| Field           | Type    | Required | Notes                                    |
|-----------------|---------|----------|------------------------------------------|
| `plan_id`       | integer | ✓        | Must be an active plan                   |
| `gateway_code`  | string  | ✓        | Must be an active gateway code           |
| `payment_proof` | string  | ✓        | URL to payment screenshot                |

**Response:**

```json
{
  "status": "success",
  "message": "Subscription request submitted. Awaiting admin approval.",
  "data": {
    "id": 1,
    "plan": { "id": 1, "name": "Signals PRO", "slug": "signals-pro", "type": "signals", "price": "29.99", "duration_days": 30 },
    "gateway": { "id": 1, "name": "Bank Transfer", "code": "bank_transfer" },
    "amount": "29.99",
    "status": "submitted",
    "reference": "SUB-01HZ...",
    "transaction_id": "TXN-01HZ...",
    "proofs": [{ "payment_proof_url": "https://example.com/proof.jpg" }],
    "created_at": "2026-04-10 12:00:00"
  }
}
```

**Error Responses:**

- 401: Unauthenticated
- 409: Already have an active subscription of the same type
- 422: `plan_id` is inactive or not found / `gateway_code` is inactive or not found / `payment_proof` is not a valid URL

---

#### 7.3 List Payments

`GET /api/v1/payments` 🔒

Returns a paginated list of the authenticated user's payments, newest first.

**Response:**

```json
{
  "status": "success",
  "message": "Payments retrieved successfully",
  "data": [ ... ]
}
```

---

#### 7.4 Get Payment

`GET /api/v1/payments/{id}` 🔒

Returns a single payment with plan, gateway, and proof details.

**Error Responses:**

- 403: Payment does not belong to the authenticated user
- 404: Payment not found

---

#### 7.5 Get Current Subscription

`GET /api/v1/subscriptions/current` 🔒

Returns the user's currently active subscription.

**Response:**

```json
{
  "status": "success",
  "message": "Active subscription retrieved successfully",
  "data": {
    "id": 1,
    "plan": { "id": 1, "name": "Signals PRO", "slug": "signals-pro", "type": "signals", "price": "29.99", "duration_days": 30 },
    "starts_at": "2026-04-10 12:00:00",
    "ends_at": "2026-05-10 12:00:00",
    "status": "active",
    "created_at": "2026-04-10 12:00:00"
  }
}
```

**Error Responses:**

- 404: No active subscription found

---

#### 7.6 Subscription History

`GET /api/v1/subscriptions` 🔒

Returns a paginated list of all the user's subscriptions, newest first. Each record includes the derived `status` field: `active`, `expired`, or `inactive`.

---

### 12. Notifications — `/notifications`

> 🔒 All routes require authentication.

#### 11.1 List Notifications

`GET /api/v1/notifications` 🔒

Returns a paginated list of the authenticated user's notifications, newest first.

**Response:**

```json
{
  "status": "success",
  "message": "Notifications retrieved.",
  "data": {
    "data": [
      {
        "id": "9d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a",
        "type": "App\\Notifications\\User\\NewSignalNotification",
        "notifiable_type": "App\\Models\\User",
        "notifiable_id": 1,
        "data": {
          "type": "new_signal",
          "title": "New Signal: EURUSD BUY",
          "message": "A new EURUSD BUY signal has been published. Entry: 1.09500",
          "signal_id": 42,
          "symbol": "EURUSD",
          "action": "buy",
          "entry_price": "1.09500"
        },
        "read_at": null,
        "created_at": "2026-04-16T10:00:00.000000Z",
        "updated_at": "2026-04-16T10:00:00.000000Z"
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/v1/notifications?page=1",
      "last": "http://localhost:8000/api/v1/notifications?page=2",
      "prev": null,
      "next": "http://localhost:8000/api/v1/notifications?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 2,
      "per_page": 20,
      "to": 20,
      "total": 35
    }
  }
}
```

**Notification `data` shapes by `type`:**

| `type` | Extra fields in `data` |
|--------|------------------------|
| `new_signal` | `signal_id`, `symbol`, `action`, `entry_price` |
| `signal_closed` | `signal_id`, `symbol`, `action`, `status`, `pips_result` |
| `subscription_activated` | `subscription_id`, `plan_name`, `ends_at` |
| `subscription_expiring` | `subscription_id`, `plan_name`, `ends_at`, `days_remaining` |
| `payment_rejected` | `payment_id`, `plan_name`, `reason` |
| `account_verified` | *(title + message only)* |
| `kyc_rejected` | `rejection_reason` |
| `new_trading_class` | `trading_class_id`, `title`, `scheduled_at`, `platform`, `meeting_link` |
| `trading_class_reminder` | `trading_class_id`, `title`, `scheduled_at`, `platform`, `meeting_link` |
| `pool_investment_approved` | `pool_investment_id`, `pool_name`, `contribution` |
| `pool_investment_rejected` | `pool_investment_id`, `pool_name`, `rejection_reason` |
| `profit_distributed` | `profit_distribution_id`, `amount`, `distribution_date` |
| `announcement` | `announcement_id`, `title`, `message` |


A notification is **unread** when `read_at` is `null`.

---

#### 11.2 Get Unread Count

`GET /api/v1/notifications/unread-count` 🔒

Returns the number of unread notifications for the authenticated user.

**Response:**

```json
{
  "status": "success",
  "message": "Unread count retrieved.",
  "data": {
    "unread_count": 5
  }
}
```

---

#### 11.3 Mark Notification as Read

`POST /api/v1/notifications/{id}/read` 🔒

Marks a single notification as read by its UUID.

**URL Parameters:**

| Parameter | Type   | Required | Description         |
|-----------|--------|----------|---------------------|
| `id`      | UUID   | ✓        | Notification UUID   |

**Response:**

```json
{
  "status": "success",
  "message": "Notification marked as read.",
  "data": []
}
```

**Error Responses:**

- 404: Notification not found or does not belong to the authenticated user

---

#### 11.4 Mark All Notifications as Read

`POST /api/v1/notifications/read-all` 🔒

Marks all unread notifications as read for the authenticated user.

**Response:**

```json
{
  "status": "success",
  "message": "All notifications marked as read.",
  "data": []
}
```

---

## Response Format

All API responses follow a consistent JSON structure.

### Success Response (2xx)

```json
{
    "status": "success",
    "message": "Operation successful",
    "data": {}
}
```

### Validation Error (422)

```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### Authentication Error (401)

```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

### Forbidden Error (403)

```json
{
    "status": "error",
    "message": "Forbidden"
}
```

### Not Found Error (404)

```json
{
    "status": "error",
    "message": "Resource not found"
}
```

### Server Error (500)

```json
{
    "status": "error",
    "message": "Internal server error"
}
```

---

## Testing the API

### Using cURL

**Register a new user:**

```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "country": "Nigeria"
  }'
```

**Login:**

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Get profile (authenticated):**

```bash
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Submit KYC verification:**

```bash
curl -X POST http://localhost:8000/api/v1/verifications \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "id_type": "national_id",
    "id_number": "12345678",
    "id_card_front_img_url": "https://example.com/front.jpg",
    "id_card_back_img_url": "https://example.com/back.jpg",
    "selfie_img_url": "https://example.com/selfie.jpg"
  }'
```

**Store MetaTrader credentials:**

```bash
curl -X POST http://localhost:8000/api/v1/metatrader-credentials \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "mt_account_number": "12345678",
    "mt_password": "mt_password",
    "mt_server": "Broker-Server",
    "platform_type": "mt5",
    "initial_deposit": 1000,
    "risk_level": "moderate"
  }'
```

**Get trading stats:**

```bash
curl -X GET http://localhost:8000/api/v1/trading-stats \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Get client portfolio (top 3 by balance):**

```bash
curl -X GET http://localhost:8000/api/v1/client-portfolio \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Get all signals:**

```bash
curl -X GET http://localhost:8000/api/v1/signals
```

**Get active signals only:**

```bash
curl -X GET http://localhost:8000/api/v1/signals/active
```

**Get signals with filters:**

```bash
curl -X GET "http://localhost:8000/api/v1/signals?symbol=EURUSD&status=active&per_page=10"
```

**Get signal statistics:**

```bash
curl -X GET http://localhost:8000/api/v1/signals/statistics
```

**Get specific signal:**

```bash
curl -X GET http://localhost:8000/api/v1/signals/1
```

**Get active pools:**

```bash
curl -X GET http://localhost:8000/api/v1/pools \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Submit pool investment:**

```bash
curl -X POST http://localhost:8000/api/v1/pool-investments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "pool_id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
    "full_name": "John Doe",
    "phone_number": "+234 123 456 7890",
    "bank_name": "GTBank",
    "account_number": "0123456789",
    "account_name": "John Doe",
    "contribution": 1000,
    "amount_paid": 1035,
    "payment_gateway_id": 1,
    "payment_proof_url": "https://example.com/proof.jpg",
    "terms_accepted": true
  }'
```

**Get my pool investments:**

```bash
curl -X GET http://localhost:8000/api/v1/pool-investments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Get my profit distributions:**

```bash
curl -X GET http://localhost:8000/api/v1/profit-distributions \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**List payment gateways:**

```bash
curl -X GET http://localhost:8000/api/v1/payment-gateways \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Subscribe to a plan:**

```bash
curl -X POST http://localhost:8000/api/v1/subscribe \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 1,
    "gateway_code": "bank_transfer",
    "payment_proof": "https://example.com/proof.jpg"
  }'
```

**Get current active subscription:**

```bash
curl -X GET http://localhost:8000/api/v1/subscriptions/current \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Get notifications:**

```bash
curl -X GET http://localhost:8000/api/v1/notifications \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Get unread notification count:**

```bash
curl -X GET http://localhost:8000/api/v1/notifications/unread-count \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Mark a notification as read:**

```bash
curl -X POST http://localhost:8000/api/v1/notifications/9d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a/read \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Mark all notifications as read:**

```bash
curl -X POST http://localhost:8000/api/v1/notifications/read-all \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## React Native Real-Time Guide (Laravel Echo + Reverb)

The JMJ API uses **Laravel Reverb** (WebSocket server) to push real-time notifications to connected clients. Every notification that gets sent to a user is also broadcast over their private channel so React Native clients can react instantly — without polling.

### How It Works

```
Admin sends announcement
        ↓
SendAnnouncementJob (queued)
        ↓
AnnouncementNotification → saved to database + broadcast via Reverb
        ↓
private-App.Models.User.{id}  ← your RN app listens here
        ↓
Your UI updates in real-time
```

The same pipeline fires for all notification types: signals, subscription events, KYC status changes, etc.

---

### 1. Install Dependencies

```bash
npm install laravel-echo pusher-js
# or
yarn add laravel-echo pusher-js
```

React Native does not expose `window` by default. Assign the global shim **before** importing Echo:

```js
// index.js or App.js — must be the very first thing
if (typeof window !== 'undefined') {
  window.global = window;
} else {
  global.window = global;
}
```

---

### 2. Create the Echo Instance

Create a dedicated `echo.js` utility, for example in `src/services/echo.js`:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Pusher must be on global before Echo is constructed
global.Pusher = Pusher;

/**
 * Build an authenticated Echo instance.
 * Call this after the user has logged in and you have their token.
 *
 * @param {string} token  - Sanctum Bearer token from login response
 * @returns {Echo}
 */
export function createEcho(token) {
  return new Echo({
    broadcaster: 'reverb',
    key: 'fh0s52qfgjl0iyhnxd0i',
    wsHost: 'ws.api.jmj.finance.com',
    wsPort: 443,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: 'https://api.jmj.finance.com/api/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    },
  });
}
```

> In production replace `wsHost`, `wsPort`, and `authEndpoint` with your live server values and set `forceTLS: true`.

---

### 3. Connect After Login

```js
import { createEcho } from '../services/echo';

// After a successful login API call:
const { access_token, user } = loginResponse.data;

// Store the token however you prefer (SecureStore, MMKV, etc.)
await SecureStore.setItemAsync('auth_token', access_token);

// Boot the Echo instance and store it somewhere accessible (context, zustand, etc.)
const echo = createEcho(access_token);
```

---

### 4. Subscribe to Notifications

Private channels require authentication. The server validates the token through `/api/broadcasting/auth` automatically — you only need to subscribe to the correct channel.

The channel name follows Laravel's convention: `App.Models.User.{userId}`.

```js
import { useEffect } from 'react';

export function useRealtimeNotifications(echo, userId, onNotification) {
  useEffect(() => {
    if (!echo || !userId) return;

    const channel = echo.private(`App.Models.User.${userId}`);

    // Laravel broadcasts notifications on this event name
    channel.notification((notification) => {
      onNotification(notification);
    });

    return () => {
      echo.leave(`App.Models.User.${userId}`);
    };
  }, [echo, userId]);
}
```

**Usage in a component:**

```js
useRealtimeNotifications(echo, currentUser.id, (notification) => {
  console.log('New notification:', notification);
  // notification.type tells you what kind it is
  // notification.title and notification.message are always present
});
```

---

### 5. Notification Payload Reference

Every broadcast payload contains at minimum:

```json
{
  "id": "uuid",
  "type": "App\\Notifications\\User\\AnnouncementNotification",
  "data": {
    "type": "announcement",
    "title": "System Maintenance",
    "message": "The platform will be down at 2am.",
    "announcement_id": 1
  }
}
```

Use `notification.data.type` (the short type string) to branch your UI logic:

| `data.type` | Extra fields | Suggested UI action |
|---|---|---|
| `announcement` | `announcement_id`, `title`, `message` | Show banner / push notification |
| `new_signal` | `signal_id`, `symbol`, `action`, `entry_price` | Badge on signals tab |
| `signal_closed` | `signal_id`, `symbol`, `status`, `pips_result` | Update signal card |
| `subscription_activated` | `subscription_id`, `plan_name`, `ends_at` | Unlock premium content |
| `subscription_expiring` | `subscription_id`, `plan_name`, `days_remaining` | Show renewal prompt |
| `payment_rejected` | `payment_id`, `plan_name`, `reason` | Show error alert |
| `account_verified` | *(title + message only)* | Show success toast |
| `kyc_rejected` | `rejection_reason` | Prompt user to resubmit |
| `new_trading_class` | `trading_class_id`, `title`, `scheduled_at`, `platform`, `meeting_link` | Add to calendar |
| `trading_class_reminder` | same as above | Push reminder notification |
| `pool_investment_approved` | `pool_investment_id`, `pool_name`, `contribution` | Show success toast |
| `pool_investment_rejected` | `pool_investment_id`, `pool_name`, `rejection_reason` | Show rejection reason |
| `profit_distributed` | `profit_distribution_id`, `amount`, `distribution_date` | Show earnings banner |

---

### 6. Full Example with React Context

```js
// src/context/EchoContext.js
import React, { createContext, useContext, useEffect, useRef, useState } from 'react';
import { createEcho } from '../services/echo';

const EchoContext = createContext(null);

export function EchoProvider({ token, userId, children }) {
  const echoRef = useRef(null);
  const [notifications, setNotifications] = useState([]);

  useEffect(() => {
    if (!token || !userId) return;

    echoRef.current = createEcho(token);

    echoRef.current
      .private(`App.Models.User.${userId}`)
      .notification((notification) => {
        setNotifications((prev) => [notification, ...prev]);
      });

    return () => {
      echoRef.current?.disconnect();
    };
  }, [token, userId]);

  return (
    <EchoContext.Provider value={{ echo: echoRef.current, notifications, setNotifications }}>
      {children}
    </EchoContext.Provider>
  );
}

export const useEcho = () => useContext(EchoContext);
```

```js
// App.js
import { EchoProvider } from './src/context/EchoContext';

export default function App() {
  const { token, user } = useAuthStore();

  return (
    <EchoProvider token={token} userId={user?.id}>
      <RootNavigator />
    </EchoProvider>
  );
}
```

```js
// Any screen
import { useEcho } from '../context/EchoContext';

export function NotificationBell() {
  const { notifications } = useEcho();
  const unread = notifications.filter((n) => !n.read_at).length;

  return <BellIcon badgeCount={unread} />;
}
```

---

### 7. Disconnect on Logout

Always disconnect Echo when the user logs out to avoid memory leaks and stale subscriptions:

```js
async function handleLogout() {
  await api.post('/auth/logout');
  echo.disconnect();         // close the WebSocket
  await SecureStore.deleteItemAsync('auth_token');
  navigation.replace('Login');
}
```

---

### 8. Combining Real-Time with REST Notifications

Real-time handles live events. For notifications the user missed while offline, fetch the REST endpoint on app start:

```js
// On app foreground / login
const { data } = await api.get('/notifications');
setNotifications(data.data.data);

// Then layer real-time on top — new events prepend to the list
echo.private(`App.Models.User.${userId}`).notification((n) => {
  setNotifications((prev) => [n, ...prev]);
});
```

Mark as read via REST — the real-time channel is receive-only:

```js
// Single notification
await api.post(`/notifications/${id}/read`);

// All at once
await api.post('/notifications/read-all');
```

---

## Setup Instructions

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. (Optional) Seed database: `php artisan db:seed`
7. Start the server: `php artisan serve`
8. Start Reverb: `php artisan reverb:start`
9. Start queue worker: `php artisan queue:work`

---

