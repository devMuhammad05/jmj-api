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
| POST   | `/auth/register`              | No   | Register new user                  |
| POST   | `/auth/login`                 | No   | Login user                         |
| POST   | `/auth/logout`                | 🔒   | Logout user                        |
| GET    | `/auth/me`                    | 🔒   | Get authenticated user profile     |
| PUT    | `/auth/profile`               | 🔒   | Update user profile                |
| GET    | `/verifications`              | 🔒   | Get KYC verification status        |
| POST   | `/verifications`              | 🔒   | Submit KYC documents               |
| POST   | `/metatrader-credentials`     | 🔒   | Store MetaTrader credentials       |

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
        "access_token": "1|abc123..."
    }
}
```

---

#### 1.2 Login

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
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "email": "john@example.com",
            "country": "Nigeria",
            "created_at": "2026-03-08T10:00:00.000000Z",
            "updated_at": "2026-03-08T10:00:00.000000Z"
        },
        "access_token": "2|xyz789..."
    }
}
```

---

#### 1.3 Logout

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

#### 1.4 Get Profile

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

#### 1.5 Update Profile

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
| `risk_level`        | string  | ✓        | `conservative`, `moderate`, `aggressive` |

> ⚠️ **Security:** Credential data is **never** returned in the response per platform security rules. Credentials are encrypted and stored securely.

**Response:**

```json
{
    "status": "success",
    "message": "MetaTrader credentials saved successfully",
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

---

## Project Structure

```
app/
├── Actions/            # Business logic actions
│   ├── CreateClientAction.php
│   ├── CreateMetaTraderCredentialAction.php
│   └── CreateVerificationAction.php
├── DTOs/               # Data Transfer Objects
│   ├── ClientData.php
│   ├── MetaTraderData.php
│   └── VerificationData.php
├── Enums/              # Enumerations
│   ├── IdType.php
│   ├── MetaTraderPlatformType.php
│   ├── RiskLevel.php
│   ├── Role.php
│   ├── SignalAction.php
│   ├── SignalStatus.php
│   ├── SignalType.php
│   └── VerificationStatus.php
├── Http/
│   ├── Controllers/Api/V1/   # Versioned API controllers
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   ├── MetaTraderCredentialController.php
│   │   └── VerificationController.php
│   ├── Requests/Api/V1/      # Form request validation
│   │   ├── Auth/
│   │   │   ├── LoginUserRequest.php
│   │   │   ├── RegisterUserRequest.php
│   │   │   └── UpdateProfileRequest.php
│   │   ├── StoreMetaTraderCredentialRequest.php
│   │   └── StoreVerificationRequest.php
│   └── Resources/V1/         # API resources
│       ├── ClientResource.php
│       └── VerificationResource.php
├── Models/             # Eloquent models
│   ├── AccountSnapshot.php
│   ├── Client.php
│   ├── MetaTraderCredential.php
│   ├── Signal.php
│   ├── User.php
│   └── Verification.php
└── Traits/
    └── ApiResponse.php # Shared JSON response helpers

routes/
└── api.php             # All API route definitions
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

---

## License

This project is proprietary software for JMJ Trading Platform.
