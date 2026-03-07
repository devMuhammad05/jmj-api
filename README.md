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

## Endpoints

### Health Check

| Method | URL | Auth |
| ------ | --- | ---- |
| `GET`  | `/` | No   |

Returns `"API is active"`.

---

### 1. Authentication — `/auth`

#### Register

`POST /auth/register`

| Field                   | Type   | Required | Notes                 |
| ----------------------- | ------ | -------- | --------------------- |
| `full_name`             | string | ✓        |                       |
| `email`                 | string | ✓        | Must be unique        |
| `password`              | string | ✓        | Min 8 characters      |
| `password_confirmation` | string | ✓        | Must match `password` |
| `country`               | string | —        | Optional              |

**Response:** Returns `user` object + `access_token`.

---

#### Login

`POST /auth/login`

| Field      | Type   | Required |
| ---------- | ------ | -------- |
| `email`    | string | ✓        |
| `password` | string | ✓        |

**Response:** Returns `user` object + `access_token`.

---

#### Logout

`POST /auth/logout` 🔒

Revokes the current access token.

---

#### Get Profile

`GET /auth/me` 🔒

Returns the authenticated user's profile.

---

#### Update Profile

`PUT /auth/profile` 🔒

| Field          | Type   | Required | Notes          |
| -------------- | ------ | -------- | -------------- |
| `full_name`    | string | —        |                |
| `email`        | string | —        | Must be unique |
| `phone_number` | string | —        |                |
| `country`      | string | —        |                |

---

### 2. KYC Verification — `/verifications`

> 🔒 All routes require authentication.

#### Get Verification Status

`GET /verifications`

Returns the current user's KYC verification status and timestamps. Does **not** return submitted document data.

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

Possible `status` values: `pending`, `approved`, `rejected`.

---

#### Submit KYC Documents

`POST /verifications`

| Field                   | Type         | Required | Notes                                                       |
| ----------------------- | ------------ | -------- | ----------------------------------------------------------- |
| `id_type`               | string       | ✓        | `national_id`, `passport`, `driving_license`, `voters_card` |
| `id_number`             | string       | ✓        | Max 50 characters                                           |
| `id_card_front_img_url` | string (URL) | ✓        |                                                             |
| `id_card_back_img_url`  | string (URL) | —        | Optional                                                    |
| `selfie_img_url`        | string (URL) | ✓        |                                                             |

> ⚠️ A user may only submit once unless their status is `rejected`. Re-submission while `pending` or `approved` returns a `403 Forbidden`.

---

### 3. MetaTrader Credentials — `/metatrader-credentials`

> 🔒 All routes require authentication.

#### Store MetaTrader Credentials

`POST /metatrader-credentials`

Links an investor's MT4/MT5 broker account to the platform.

| Field               | Type    | Required | Notes                                    |
| ------------------- | ------- | -------- | ---------------------------------------- |
| `mt_account_number` | string  | ✓        | Max 50 characters                        |
| `mt_password`       | string  | ✓        | Max 50 characters                        |
| `mt_server`         | string  | ✓        | Broker server name, max 100 characters   |
| `platform_type`     | string  | ✓        | `mt4` or `mt5`                           |
| `initial_deposit`   | numeric | ✓        | Min `0`                                  |
| `risk_level`        | string  | ✓        | `conservative`, `moderate`, `aggressive` |

> ⚠️ Credential data is **never** returned in the response per platform security rules.

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

### Success

```json
{
    "status": "success",
    "message": "Operation successful",
    "data": {}
}
```

### Validation Error `422`

```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### Auth Error `401`

```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

### Not Found `404`

```json
{
    "status": "error",
    "message": "Resource not found"
}
```

### Forbidden `403`

```json
{
    "status": "error",
    "message": "Forbidden"
}
```

---

## Project Structure

```
app/
├── Enums/              # RiskLevel, IdType, MetaTraderPlatformType, VerificationStatus, ...
├── Http/
│   ├── Controllers/Api/V1/   # Versioned API controllers
│   └── Requests/Api/V1/      # Form request validation
├── Models/             # Eloquent models
└── Traits/
    └── ApiResponse.php # Shared JSON response helpers

meta_cloud/             # Python MetaAPI Cloud microservice
routes/
└── api.php             # All API route definitions
```
