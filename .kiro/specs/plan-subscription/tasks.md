# Implementation Plan: Plan Subscription

## Overview

This implementation plan covers the complete plan subscription feature, including payment initiation, proof upload, admin approval workflow, subscription management, access control middleware, scheduled expiry, and Filament admin interface. The implementation follows Laravel best practices with Action classes, FormRequests, API Resources, and follows the existing codebase patterns.

## Tasks

- [x] 1. Database schema and models
  - [x] 1.1 Create migration to add payment_id to subscriptions table
    - Add nullable foreign key `payment_id` to `subscriptions` table
    - Add foreign key constraint with `nullOnDelete()`
    - _Requirements: 3.4_

  - [x] 1.2 Update Subscription model with payment relationship and status accessor
    - Add `payment_id` to `$fillable` array
    - Add `payment()` belongsTo relationship
    - Implement `getStatusAttribute()` accessor for derived status (active/expired/inactive)
    - _Requirements: 4.3, 5.2_

  - [ ]* 1.3 Write unit tests for Subscription status accessor
    - Test `active` status when `is_active=true` and `ends_at` is future
    - Test `expired` status when `ends_at` is past
    - Test `inactive` status when `is_active=false`
    - _Requirements: 4.3_

- [x] 2. Create enums and DTOs
  - [x] 2.1 Create SubscriptionStatus enum
    - Define cases: `Active`, `Expired`, `Inactive`
    - Use string backing type
    - _Requirements: 4.3, 5.2_

  - [x] 2.2 Create InitiatePaymentData DTO
    - Define readonly properties: `plan_id`, `gateway_code`, `amount`
    - Implement `fromRequest()` static factory method
    - _Requirements: 1.1_

- [x] 3. Implement validation rules and requests
  - [x] 3.1 Create MatchesPlanPrice custom validation rule
    - Implement `validate()` method to check amount matches plan price
    - Resolve plan by ID and compare prices
    - _Requirements: 1.4_

  - [ ]* 3.2 Write unit tests for MatchesPlanPrice rule
    - Test passes when amount equals plan price
    - Test fails when amount differs from plan price
    - Test handles non-existent plan gracefully
    - _Requirements: 1.4_

  - [x] 3.3 Update StorePaymentRequest validation rules
    - Add plan_id validation with `exists:plans,id` and `where('is_active', true)`
    - Add gateway_code validation with `exists:payment_gateways,code` and `where('is_active', true)`
    - Add amount validation with `MatchesPlanPrice` rule
    - Remove `reference` from validation (backend-generated)
    - _Requirements: 1.2, 1.3, 1.4_

  - [x] 3.4 Create UploadPaymentProofRequest
    - Validate `proof` file: required, file, mimes:jpeg,png,webp,pdf, max:5120 (5MB)
    - _Requirements: 2.4, 2.5_

- [x] 4. Implement payment controller endpoints
  - [x] 4.1 Update PaymentController store() method
    - Generate reference server-side: `'SUB-' . strtoupper((string) Str::ulid())`
    - Create payment with status `pending`
    - Associate with authenticated user and plan
    - Return PaymentResource
    - _Requirements: 1.1, 1.5_

  - [x] 4.2 Add PaymentController index() method
    - Return paginated list of user's payments ordered by created_at desc
    - Eager load plan, gateway, and proofs relationships
    - Return PaymentResource collection
    - _Requirements: 8.1, 8.2_

  - [x] 4.3 Add PaymentController show() method
    - Authorize user can view payment (policy check)
    - Return single payment with relationships
    - Return PaymentResource
    - _Requirements: 8.2_

  - [x] 4.4 Add PaymentController uploadProof() method
    - Authorize user owns payment (Gate::authorize)
    - Validate payment status is `pending`
    - Store file using Storage facade
    - Create PaymentProof record
    - Update payment status to `submitted`
    - Return PaymentResource
    - _Requirements: 2.1, 2.2, 2.3_

  - [ ]* 4.5 Write feature tests for payment endpoints
    - Test POST /payments creates payment with pending status and generated reference
    - Test POST /payments validates plan is active
    - Test POST /payments validates amount matches plan price
    - Test POST /payments validates gateway is active
    - Test POST /payments/{id}/proof uploads file and updates status
    - Test POST /payments/{id}/proof returns 403 for non-owned payment
    - Test POST /payments/{id}/proof returns 422 for invalid file type
    - Test POST /payments/{id}/proof returns 422 for oversized file
    - Test POST /payments/{id}/proof returns 422 for non-pending payment
    - Test GET /payments returns paginated user payments
    - Test GET /payments/{id} returns single payment
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 2.4, 2.5, 8.1, 8.2_

- [x] 5. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implement subscription approval action
  - [x] 6.1 Create ApprovePaymentAction
    - Wrap logic in DB::transaction()
    - Update payment status to `approved`
    - Deactivate existing active subscriptions for user
    - Create new subscription with starts_at=now(), ends_at=now()+duration_days, is_active=true, payment_id
    - Return created Subscription
    - _Requirements: 3.1, 3.2, 3.4, 3.5_

  - [ ]* 6.2 Write unit tests for ApprovePaymentAction
    - Test payment status updated to approved
    - Test existing active subscription is deactivated
    - Test new subscription created with correct dates and payment_id
    - Test transaction atomicity (rollback on failure)
    - Test rejection does not create subscription
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 7. Implement subscription controller endpoints
  - [x] 7.1 Create SubscriptionController with current() method
    - Query user's active subscription (is_active=true, ends_at > now)
    - Return 404 with message "You do not have an active subscription." if none found
    - Eager load plan relationship
    - Return SubscriptionResource
    - _Requirements: 4.1, 4.2_

  - [x] 7.2 Add SubscriptionController index() method
    - Return paginated list of user's subscriptions ordered by starts_at desc
    - Eager load plan relationship
    - Return SubscriptionResource collection
    - _Requirements: 5.1, 5.2_

  - [x] 7.3 Create SubscriptionResource
    - Include id, plan (PlanResource), starts_at, ends_at, status (derived), created_at
    - Format dates as ISO 8601 strings
    - _Requirements: 4.1, 4.3, 5.2_

  - [ ]* 7.4 Write feature tests for subscription endpoints
    - Test GET /subscriptions/current returns active subscription with derived status
    - Test GET /subscriptions/current returns 404 when no active subscription
    - Test GET /subscriptions returns paginated list ordered by starts_at desc
    - Test GET /subscriptions includes plan details and derived status
    - _Requirements: 4.1, 4.2, 4.3, 5.1, 5.2_

- [x] 8. Implement subscription access control middleware
  - [x] 8.1 Create EnsureActiveSubscription middleware
    - Check if user has active subscription (is_active=true, ends_at > now)
    - Return 403 with message "An active subscription is required to access this resource." if no active subscription
    - Pass request to next middleware if active subscription exists
    - _Requirements: 7.1, 7.2_

  - [x] 8.2 Register middleware as 'subscribed' alias
    - Add middleware alias in bootstrap/app.php or Kernel.php
    - _Requirements: 7.3_

  - [ ]* 8.3 Write unit tests for EnsureActiveSubscription middleware
    - Test returns 403 for user without active subscription
    - Test passes through for user with active subscription
    - Test returns 403 for unauthenticated user
    - _Requirements: 7.1, 7.2_

  - [ ]* 8.4 Write integration test for subscription-gated endpoint
    - Create test route with 'subscribed' middleware
    - Test returns 403 without active subscription
    - Test returns 200 with active subscription
    - _Requirements: 7.1, 7.2_

- [x] 9. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 10. Implement scheduled subscription expiry
  - [x] 10.1 Create ExpireSubscriptionsCommand
    - Set signature: `subscriptions:expire`
    - Query subscriptions where is_active=true and ends_at < now()
    - Update is_active to false
    - Log count of expired subscriptions
    - _Requirements: 6.1_

  - [x] 10.2 Schedule command to run daily
    - Add schedule entry in routes/console.php: `Schedule::command('subscriptions:expire')->daily()`
    - _Requirements: 6.2_

  - [ ]* 10.3 Write unit tests for ExpireSubscriptionsCommand
    - Test only subscriptions with ends_at < now() and is_active=true are updated
    - Test subscriptions with ends_at in future are not updated
    - Test already inactive subscriptions are not updated
    - _Requirements: 6.1_

- [x] 11. Implement Filament admin resource for payments
  - [x] 11.1 Create PaymentResource for Filament
    - Set navigation group to "Subscriptions"
    - Set navigation icon
    - Define model as Payment
    - _Requirements: 3.1, 3.3_

  - [x] 11.2 Create PaymentForm schema
    - Display user, plan, gateway, amount, reference (read-only)
    - Add status select field with PaymentStatus enum options
    - Display payment proofs (file preview/download)
    - Add approve/reject action buttons
    - _Requirements: 3.1, 3.3_

  - [x] 11.3 Create PaymentsTable schema
    - Show columns: reference, user name, plan name, amount, status, created_at
    - Add filters: status, date range
    - Add bulk actions if needed
    - _Requirements: 3.1_

  - [x] 11.4 Create ListPayments page
    - Standard Filament list page
    - _Requirements: 3.1_

  - [x] 11.5 Create EditPayment page
    - Add approve action that calls ApprovePaymentAction
    - Add reject action that updates status to rejected
    - Show success/error notifications
    - _Requirements: 3.1, 3.3_

- [x] 12. Add API routes
  - [x] 12.1 Register payment routes in routes/api.php
    - GET /api/v1/payments (auth:sanctum, verified.email)
    - POST /api/v1/payments (auth:sanctum, verified.email)
    - GET /api/v1/payments/{payment} (auth:sanctum, verified.email)
    - POST /api/v1/payments/{payment}/proof (auth:sanctum, verified.email)
    - _Requirements: 1.1, 2.1, 8.1, 8.2_

  - [x] 12.2 Register subscription routes in routes/api.php
    - GET /api/v1/subscriptions/current (auth:sanctum, verified.email)
    - GET /api/v1/subscriptions (auth:sanctum, verified.email)
    - _Requirements: 4.1, 5.1_

- [x] 13. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- The design uses PHP/Laravel, so all code will be in PHP
- Reference generation is backend-only to prevent tampering
- Approval logic is atomic via database transaction
- Subscription status is derived at read time, not stored
- The `subscribed` middleware enforces active subscription checks
- Scheduled command runs daily to mark expired subscriptions inactive
