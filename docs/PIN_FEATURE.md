# 4-Digit PIN Feature — Technical Implementation Plan

## Overview

A secondary 4-digit PIN layer sits on top of the existing Sanctum token auth. After a user authenticates with email/password and receives a bearer token, the mobile app gates all navigation behind a local PIN entry screen. The PIN is also re-required for sensitive mutations (withdrawals, investment applications). This document covers every layer from database to API contract.

---

## 1. Database Schema

### 1.1 Migration — `add_pin_columns_to_users_table`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('pin')->nullable()->after('password');          // bcrypt hash
    $table->timestamp('pin_set_at')->nullable()->after('pin');
    $table->unsignedTinyInteger('pin_attempts')->default(0)->after('pin_set_at');
    $table->timestamp('pin_locked_until')->nullable()->after('pin_attempts');
});
```

| Column | Type | Purpose |
|---|---|---|
| `pin` | `string\|null` | bcrypt hash of the 4-digit PIN; null = not yet set |
| `pin_set_at` | `timestamp\|null` | When PIN was first configured |
| `pin_attempts` | `tinyint` | Consecutive failed attempts since last success |
| `pin_locked_until` | `timestamp\|null` | Lockout expiry; null = not locked |

### 1.2 Model Changes — `User`

```php
protected $fillable = [
    // ... existing fields
    'pin',
    'pin_set_at',
    'pin_attempts',
    'pin_locked_until',
];

protected $hidden = [
    'password',
    'remember_token',
    'pin',                // never leak the hash
];

protected function casts(): array
{
    return [
        // ... existing casts
        'pin_set_at'       => 'datetime',
        'pin_locked_until' => 'datetime',
        'pin_attempts'     => 'integer',
    ];
}

public function isPinSet(): bool
{
    return $this->pin !== null;
}

public function isPinLocked(): bool
{
    return $this->pin_locked_until !== null
        && $this->pin_locked_until->isFuture();
}

public function verifyPin(string $raw): bool
{
    return Hash::check($raw, $this->pin);
}
```

---

## 2. Endpoints

All endpoints are under `/api/v1/auth/pin` and require `Authorization: Bearer <token>`.

| Method | Path | Purpose | Auth |
|---|---|---|---|
| `POST` | `/api/v1/auth/pin/setup` | Set PIN for the first time | 🔒 |
| `POST` | `/api/v1/auth/pin/verify` | Verify PIN (app unlock) | 🔒 |
| `POST` | `/api/v1/auth/pin/change` | Change existing PIN | 🔒 |
| `POST` | `/api/v1/auth/pin/reset` | Reset PIN via OTP | 🔒 |

### 2.1 `POST /api/v1/auth/pin/setup`

**Request:**
```json
{ "pin": "1234", "pin_confirmation": "1234" }
```

**Validation:**
- `pin` — required, string, regex `/^\d{4}$/`, confirmed
- User must not already have a PIN (`pin_set_at === null`)

**Logic:**
```php
$user->update([
    'pin'        => Hash::make($request->pin),
    'pin_set_at' => now(),
]);
```

**Response `200`:**
```json
{ "success": true, "message": "PIN set successfully." }
```

**Error `422`:** PIN already set — user must use `/change`.

---

### 2.2 `POST /api/v1/auth/pin/verify`

Used by the app on launch or after backgrounding. Does **not** issue a new token; it simply confirms the PIN is correct so the app can unlock its local UI state.

**Request:**
```json
{ "pin": "1234" }
```

**Validation:**
- `pin` — required, string, regex `/^\d{4}$/`
- User must have a PIN set

**Logic (see Section 4 — Rate Limiting):**
```php
if ($user->isPinLocked()) {
    return response()->json([
        'success' => false,
        'message' => 'Too many attempts. Try again in ' . $user->pin_locked_until->diffForHumans() . '.',
    ], 429);
}

if (! $user->verifyPin($request->pin)) {
    $attempts = $user->pin_attempts + 1;
    $update   = ['pin_attempts' => $attempts];

    if ($attempts >= 5) {
        $update['pin_locked_until'] = now()->addMinutes(30);
        $update['pin_attempts']     = 0;
    }

    $user->update($update);

    return response()->json(['success' => false, 'message' => 'Incorrect PIN.'], 401);
}

// Success — reset counters
$user->update(['pin_attempts' => 0, 'pin_locked_until' => null]);
```

**Response `200`:**
```json
{ "success": true, "message": "PIN verified." }
```

**Response `429`:** Locked out.
**Response `401`:** Wrong PIN.

---

### 2.3 `POST /api/v1/auth/pin/change`

**Request:**
```json
{ "current_pin": "1234", "pin": "5678", "pin_confirmation": "5678" }
```

**Validation:**
- `current_pin` — required, `\d{4}`, must pass `verifyPin()`
- `pin` — required, `\d{4}`, confirmed, different from `current_pin`

**Logic:** Same lockout check as verify, then rehash and update.

---

### 2.4 `POST /api/v1/auth/pin/reset`

PIN reset is gated behind OTP verification using the existing `Verification` / OTP flow already in the project.

**Flow:**
1. Client calls existing OTP send endpoint (e.g. `/api/v1/auth/otp/send`)
2. User receives OTP via SMS/email
3. Client calls `/api/v1/auth/pin/reset` with the OTP and new PIN

**Request:**
```json
{ "otp": "839201", "pin": "9999", "pin_confirmation": "9999" }
```

**Logic:**
```php
// Validate OTP via existing OTP service, then:
$user->update([
    'pin'              => Hash::make($request->pin),
    'pin_set_at'       => now(),
    'pin_attempts'     => 0,
    'pin_locked_until' => null,
]);
```

---

## 3. Form Requests

```
app/Http/Requests/
├── Auth/
│   ├── SetupPinRequest.php
│   ├── VerifyPinRequest.php
│   ├── ChangePinRequest.php
│   └── ResetPinRequest.php
```

Each `SetupPinRequest` / `VerifyPinRequest` includes the rule:

```php
'pin' => ['required', 'string', 'regex:/^\d{4}$/'],
```

`SetupPinRequest` adds `'pin_confirmation'` confirmed rule and custom rule asserting PIN is not yet set:

```php
Rule::prohibitedIf(fn () => $this->user()->isPinSet()),
```

---

## 4. Rate Limiting & Lockout

| Threshold | Action |
|---|---|
| 5 consecutive wrong PINs | Lock account PIN for **30 minutes** |
| Reset on successful verify | Clear `pin_attempts` + `pin_locked_until` |
| Reset on PIN change/reset | Clear both columns |

Additionally, apply Laravel's route-level throttle on top:

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::post('/auth/pin/verify', VerifyPinController::class);
});
```

This prevents brute-force at the HTTP level before even hitting the DB lockout logic.

---

## 5. Middleware — `RequirePin`

For sensitive routes (pool investment submission, withdrawal, etc.) the client must include the PIN in the request body. A dedicated middleware re-verifies before processing:

```
app/Http/Middleware/RequirePin.php
```

```php
public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();

    if (! $user->isPinSet()) {
        return response()->json(['message' => 'PIN not configured.'], 403);
    }

    if ($user->isPinLocked()) {
        return response()->json(['message' => 'PIN locked. Try later.'], 429);
    }

    if (! $user->verifyPin((string) $request->input('pin'))) {
        // increment attempts (same logic as verify endpoint)
        return response()->json(['message' => 'Invalid PIN.'], 401);
    }

    return $next($request);
}
```

Register in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'pin' => \App\Http\Middleware\RequirePin::class,
    ]);
})
```

Usage on sensitive routes:

```php
Route::middleware(['auth:sanctum', 'pin'])->group(function () {
    Route::post('/pools/{pool}/invest', SubmitInvestmentController::class);
});
```

The `pin` field is consumed by the middleware and should **not** be passed to the controller; strip it in the middleware after verification:

```php
$request->request->remove('pin');
return $next($request);
```

---

## 6. Login Response — PIN Status Flag

Extend the login response to tell the client whether PIN setup is required:

```json
{
  "success": true,
  "data": {
    "token": "...",
    "user": { "id": "...", "full_name": "..." },
    "pin_configured": false
  }
}
```

`pin_configured` = `$user->isPinSet()` — the app uses this to redirect to PIN setup vs PIN entry screen on first launch.

---

## 7. App-Side Flow

```
Login (email + password)
        │
        ▼
  Receive token
        │
        ├── pin_configured = false ──► PIN Setup Screen ──► POST /pin/setup
        │
        └── pin_configured = true
                   │
                   ▼
           PIN Entry Screen ──► POST /pin/verify
                   │
                   ├── 200 OK ──► Unlock App
                   ├── 401    ──► Show remaining attempts
                   └── 429    ──► Show lockout timer (countdown)
```

After backgrounding > N minutes, the app re-presents the PIN entry screen and calls `/pin/verify` again without re-issuing a token — the existing token is still valid.

---

## 8. Security Checklist

- [x] PIN stored as `bcrypt` hash — never plaintext
- [x] PIN never returned in any API response
- [x] DB lockout after 5 failures (30 min)
- [x] HTTP throttle `10 req/min` on PIN endpoints
- [x] PIN stripped from request before reaching controller
- [x] `RequirePin` middleware for sensitive mutations
- [x] PIN reset requires OTP (out-of-band verification)
- [x] Common/sequential PINs (`0000`, `1234`, `1111`) — optionally blocked via custom validation rule
- [x] `pin` column in `$hidden` on User model

### Optional: Block Weak PINs

```php
// app/Rules/StrongPin.php
public function validate(string $attribute, mixed $value, Closure $fail): void
{
    $weak = ['0000','1111','2222','3333','4444','5555','6666','7777','8888','9999','1234','4321','0123'];

    if (in_array($value, $weak, true)) {
        $fail('This PIN is too common. Choose a less predictable PIN.');
    }
}
```

---

## 9. Files to Create

```
database/migrations/
└── xxxx_add_pin_columns_to_users_table.php

app/Http/
├── Controllers/Api/V1/Auth/
│   ├── SetupPinController.php
│   ├── VerifyPinController.php
│   ├── ChangePinController.php
│   └── ResetPinController.php
├── Requests/Auth/
│   ├── SetupPinRequest.php
│   ├── VerifyPinRequest.php
│   ├── ChangePinRequest.php
│   └── ResetPinRequest.php
├── Middleware/
│   └── RequirePin.php
└── Rules/
    └── StrongPin.php (optional)
```

Routes added to `routes/api.php` under `v1/auth/pin` prefix, inside `auth:sanctum` middleware group.