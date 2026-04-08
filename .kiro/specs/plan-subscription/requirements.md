# Requirements Document

## Introduction

This feature allows authenticated users to subscribe to a plan by initiating a payment, uploading proof of payment, and having an admin approve the payment — at which point an active subscription is created. The system must track subscription status, enforce access based on active subscriptions, and allow users to view their current and past subscriptions.

The payment flow mirrors the existing pattern: user initiates payment → uploads proof → admin approves → subscription is activated. Subscription enforcement is applied via middleware or policy checks on protected resources.

## Glossary

- **Subscription_System**: The Laravel API subsystem responsible for managing plan subscriptions.
- **User**: An authenticated API consumer identified via Sanctum token with a verified email.
- **Plan**: A purchasable tier with a defined price and duration (stored in the `plans` table).
- **Subscription**: A record linking a User to a Plan with `starts_at`, `ends_at`, and `is_active` fields.
- **Payment**: A record of a financial transaction initiated by a User for a Plan, with a lifecycle status.
- **Payment_Proof**: A file uploaded by the User as evidence of a completed bank transfer or payment.
- **Admin**: A privileged user who reviews and approves or rejects payments via the Filament admin panel.
- **Active_Subscription**: A Subscription where `is_active = true` and `ends_at` is in the future.
- **Expired_Subscription**: A Subscription where `ends_at` is in the past, regardless of `is_active`.
- **PaymentStatus**: An enum with values: `pending`, `submitted`, `under_review`, `approved`, `rejected`, `failed`.
- **SubscriptionStatus**: A derived state — `active`, `expired`, or `inactive` — computed from Subscription fields.

---

## Requirements

### Requirement 1: Initiate a Subscription Payment

**User Story:** As a User, I want to initiate a payment for a plan, so that I can begin the subscription process.

#### Acceptance Criteria

1. WHEN a User submits a valid plan ID, gateway code, amount, and reference(should be created from the backend not frontend), THE Subscription_System SHALL create a Payment record with status `pending` and return it in the response.
2. WHEN a User submits a plan ID that does not exist or is inactive, THE Subscription_System SHALL return a 422 validation error.
3. WHEN a User submits a gateway code that does not exist or is inactive, THE Subscription_System SHALL return a 422 validation error.
4. WHEN a User submits an amount that does not match the plan's price, THE Subscription_System SHALL return a 422 validation error.
5. THE Subscription_System SHALL associate the Payment with the authenticated User and the selected Plan.

---

### Requirement 2: Upload Payment Proof

**User Story:** As a User, I want to upload proof of payment, so that an admin can verify my transaction.

#### Acceptance Criteria

1. WHEN a User uploads a valid image or PDF file for an existing Payment in `pending` status, THE Subscription_System SHALL store the file and create a Payment_Proof record linked to the Payment.
2. WHEN a Payment_Proof is successfully stored, THE Subscription_System SHALL update the Payment status to `submitted`.
3. IF a User attempts to upload proof for a Payment that does not belong to them, THEN THE Subscription_System SHALL return a 403 Forbidden response.
4. IF a User uploads a file that is not an image (jpeg, png, webp) or PDF, THEN THE Subscription_System SHALL return a 422 validation error.
5. IF a User uploads a file exceeding 5 MB, THEN THE Subscription_System SHALL return a 422 validation error.

---

### Requirement 3: Admin Approves or Rejects Payment

**User Story:** As an Admin, I want to approve or reject a submitted payment, so that I can control when subscriptions are activated.

#### Acceptance Criteria

1. WHEN an Admin approves a Payment with status `submitted` or `under_review`, THE Subscription_System SHALL update the Payment status to `approved`.
2. WHEN a Payment is approved, THE Subscription_System SHALL deactivate any existing Active_Subscription for the User, then create a new Subscription with `starts_at = now()`, `ends_at = now() + plan.duration_days`, and `is_active = true`.
3. WHEN an Admin rejects a Payment, THE Subscription_System SHALL update the Payment status to `rejected` and SHALL NOT create a Subscription.
4. WHEN a Payment is approved and a new Subscription is created, THE Subscription_System SHALL link the Subscription to the Payment via a `payment_id` foreign key.
5. THE Subscription_System SHALL perform the approval and subscription creation atomically within a database transaction.

---

### Requirement 4: View Current Subscription

**User Story:** As a User, I want to view my current subscription, so that I know which plan I am on and when it expires.

#### Acceptance Criteria

1. WHEN a User requests their active subscription, THE Subscription_System SHALL return the Subscription record including plan details, `starts_at`, `ends_at`, and derived `status`.
2. WHILE a User has no Active_Subscription, THE Subscription_System SHALL return a 404 response with a descriptive message.
3. THE Subscription_System SHALL include a derived `status` field in the response: `active` when `is_active = true` and `ends_at` is in the future, `expired` when `ends_at` is in the past.

---

### Requirement 5: View Subscription History

**User Story:** As a User, I want to view my subscription history, so that I can track my past and current plans.

#### Acceptance Criteria

1. WHEN a User requests their subscription history, THE Subscription_System SHALL return a paginated list of all Subscriptions belonging to the User, ordered by `starts_at` descending.
2. THE Subscription_System SHALL include plan details and derived `status` for each Subscription in the list.

---

### Requirement 6: Subscription Expiry Enforcement

**User Story:** As a system operator, I want expired subscriptions to be automatically marked inactive, so that access control remains accurate.

#### Acceptance Criteria

1. WHEN a scheduled job runs, THE Subscription_System SHALL set `is_active = false` on all Subscriptions where `ends_at` is in the past and `is_active = true`.
2. THE Subscription_System SHALL run the expiry job at least once every 24 hours.

---

### Requirement 7: Access Control via Subscription Status

**User Story:** As a system operator, I want to restrict certain API resources to users with an active subscription, so that only paying users can access premium features.

#### Acceptance Criteria

1. WHEN a User without an Active_Subscription attempts to access a subscription-gated endpoint, THE Subscription_System SHALL return a 403 Forbidden response with a descriptive message.
2. WHILE a User has an Active_Subscription, THE Subscription_System SHALL allow access to subscription-gated endpoints.
3. THE Subscription_System SHALL expose a reusable middleware named `subscribed` that enforces the Active_Subscription check.

---

### Requirement 8: View Payment History

**User Story:** As a User, I want to view my payment history, so that I can track all payments I have made.

#### Acceptance Criteria

1. WHEN a User requests their payment list, THE Subscription_System SHALL return a paginated list of Payments belonging to the User, ordered by creation date descending.
2. THE Subscription_System SHALL include plan details, gateway details, proof files, and current status for each Payment in the list.
