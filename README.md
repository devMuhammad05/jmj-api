# JMJ API Documentation

This repository contains the API for the JMJ application. The API follows RESTful principles and returns JSON responses.

## Base URL

All API requests should be made to:
`http://localhost:8000/api/v1` (or your configured environment URL)

## Authentication

Authentication is handled via [Laravel Sanctum](https://laravel.com/docs/sanctum). For protected routes, include the Bearer token in the `Authorization` header.

Header example:
`Authorization: Bearer <your-token>`

---

## API Endpoints

### 0. General

#### **API Health Check**

Check if the API service is active.

- **URL**: `/`
- **Method**: `GET`
- **Response**: `"API is active"`

### 1. Authentication

#### **Register User**

Create a new user account.

- **URL**: `/auth/register`
- **Method**: `POST`
- **Body Parameters**:
    - `full_name` (string, required): Full name of the user.
    - `email` (string, required): Valid email address.
    - `country` (string, optional): Country of the user.
    - `password` (string, required): Minimum 8 characters.
    - `password_confirmation` (string, required): Must match `password`.

#### **Login User**

Authenticate a user and get an access token.

- **URL**: `/auth/login`
- **Method**: `POST`
- **Body Parameters**:
    - `email` (string, required): Registered email.
    - `password` (string, required): User password.

#### **Logout User**

Revoke the current access token.

- **URL**: `/auth/logout`
- **Method**: `POST`
- **Authentication**: Required (Sanctum)

#### **Get Profile**

Retrieve current authenticated user details.

- **URL**: `/auth/me`
- **Method**: `GET`
- **Authentication**: Required (Sanctum)

---

### 2. Client Management

#### **List Clients**

Get a paginated list of all clients.

- **URL**: `/clients`
- **Method**: `GET`
- **Query Parameters**:
    - `page` (int, optional): Page number for pagination.

#### **Store Client**

Create a new client with verification and MetaTrader details.

- **URL**: `/clients`
- **Method**: `POST`
- **Body Parameters**:
    - **Client Details**:
        - `full_name` (string, required)
        - `email` (string, required, unique)
        - `phone` (string, required)
    - **Verification Details**:
        - `id_type` (string, required): One of `national_id`, `passport`, `driving_license`, `voters_card`.
        - `id_number` (string, required)
        - `id_card_front_img_url` (string, required)
        - `id_card_back_img_url` (string, optional)
        - `selfie_img_url` (string, required)
    - **MetaTrader Details**:
        - `mt_account_number` (string, required)
        - `mt_password` (string, required)
        - `mt_server` (string, required)
        - `initial_deposit` (numeric, required)
        - `risk_level` (string, required): One of `conservative`, `moderate`, `aggressive`.

---

## Response Formats

### Success Response

```json
{
    "status": "success",
    "message": "Operation successful",
    "data": { ... }
}
```

### Resource Collection Response (Paginated)

```json
{
    "status": "success",
    "message": "Clients listed successfully",
    "data": [ ... ],
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "...",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

### Error Response

```json
{
    "status": "error",
    "message": "Error description"
}
```
