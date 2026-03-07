# Admin Panel Navigation Guide

## JMJ Trading Platform - Admin Interface

> **Version:** 1.0  
> **Last Updated:** March 7, 2026

---

## Overview

This guide outlines the admin panel navigation structure for managing the JMJ Trading Platform. The admin interface is built using Filament and provides comprehensive tools for managing users, verifications, trading accounts, and platform operations.

---

## Main Navigation Menu

### 1. Dashboard
**Route:** `/admin`

The main landing page providing an overview of platform metrics:

- Total registered users
- Pending KYC verifications
- Active MetaTrader accounts
- Recent trading activity
- Platform health status

---

### 2. User Management
**Route:** `/admin/users`

Manage all platform users (investors and admins).

**Features:**
- View all registered users
- Filter by role (Investor, Admin)
- Filter by KYC status (Pending, Approved, Rejected)
- Search by name, email, or phone
- Edit user profiles
- Assign/modify user roles
- View user activity logs
- Suspend/activate accounts

**Actions:**
- Create new admin users
- Reset user passwords
- View linked MetaTrader accounts per user
- Access user verification documents (admin-only)

---

### 3. KYC Verification
**Route:** `/admin/verifications`

Review and manage investor identity verification submissions.

**Features:**
- List all verification requests
- Filter by status (Pending, Approved, Rejected)
- View submitted documents:
  - ID Type (Passport, Driver's License, National ID)
  - ID Number
  - Uploaded document files
  - Proof of address
- Approve or reject submissions
- Add rejection reasons/notes
- Track verification history

**Workflow:**
1. Review submitted documents
2. Verify authenticity
3. Approve → User can connect MetaTrader
4. Reject → User receives notification with reason

**Security Note:** Verification documents are only accessible through admin panel, never via client API.

---

### 4. MetaTrader Accounts
**Route:** `/admin/meta-trader-credentials`

Manage all connected MetaTrader 4/5 accounts.

**Features:**
- View all linked MT accounts
- Filter by platform type (MT4, MT5)
- Filter by risk level
- Search by account number or server
- View account details:
  - Account number
  - Server
  - Platform type (MT4/MT5)
  - Initial deposit
  - Risk level
  - Associated user
- Access credentials for trade execution (encrypted)
- View account snapshots and performance

**Actions:**
- View real-time account balance
- Access trading credentials (admin-only)
- Deactivate compromised accounts
- Update risk levels

**Security Note:** MT credentials are encrypted and only accessible to authorized admins for trade execution purposes.

---

### 5. Trading Signals
**Route:** `/admin/signals`

Manage trading signals and execution across managed accounts.

**Features:**
- Create new trading signals
- View all signals (Active, Executed, Cancelled)
- Filter by:
  - Signal type (Market Order, Pending Order, Close Position)
  - Status (Active, Executed, Cancelled, Failed)
  - Symbol (EURUSD, XAUUSD, etc.)
  - Action (Buy, Sell, Close)
- Edit pending signals
- Cancel active signals
- View execution results

**Signal Details:**
- Symbol and lot size
- Entry price, Stop Loss, Take Profit
- Execution timestamp
- Target accounts
- Profit/Loss results

---

### 6. Account Snapshots
**Route:** `/admin/account-snapshots`

Monitor real-time and historical account performance data.

**Features:**
- View snapshots for all MT accounts
- Filter by date range
- View metrics:
  - Balance
  - Equity
  - Margin
  - Free margin
  - Margin level
  - Leverage
  - Currency
- Export snapshot data
- Compare performance across accounts

**Use Cases:**
- Monitor account health
- Track equity changes
- Identify margin issues
- Generate performance reports

---

### 7. Audit Logs
**Route:** `/admin/audit-logs`

Track all administrative actions for security and compliance.

**Logged Events:**
- Admin login/logout
- KYC approval/rejection
- MT credential access
- Trade signal creation/execution
- User account modifications
- System configuration changes

**Features:**
- Filter by admin user
- Filter by action type
- Search by date range
- Export logs for compliance

---

### 8. Settings
**Route:** `/admin/settings`

Platform configuration and management.

**Sections:**

#### 8.1 Platform Settings
- Trading hours configuration
- Default risk levels
- Supported currency pairs
- Platform maintenance mode

#### 8.2 Integration Settings
- MetaTrader API configuration
- Email service settings (SendGrid/SES)
- Redis cache configuration
- Queue management

#### 8.3 Security Settings
- Password policies
- Session timeout
- Two-factor authentication
- API rate limiting

#### 8.4 Notification Templates
- KYC approval/rejection emails
- Trade execution alerts
- Account activity notifications

---

## User Roles & Permissions

### Admin Role
Full access to all features:
- User management
- KYC verification
- MT credential access
- Trading signal execution
- System settings
- Audit logs

### Super Admin Role
All admin permissions plus:
- Create/manage admin users
- Modify system configurations
- Access sensitive security settings
- Export compliance reports

---

## Quick Actions

Available from any page in the admin panel:

- **Search Users:** Quick search bar for finding users by name/email
- **Pending Verifications Badge:** Shows count of pending KYC reviews
- **Active Signals Badge:** Shows count of active trading signals
- **Profile Menu:** Access admin profile, settings, and logout

---

## Mobile Responsiveness

The admin panel is fully responsive and accessible on:
- Desktop (recommended for trade execution)
- Tablet (suitable for monitoring)
- Mobile (view-only recommended)

---

## Support & Documentation

For technical issues or questions:
- **Email:** admin-support@jmjtrading.com
- **Documentation:** `/docs`
- **API Reference:** `/docs/api`

---

## Security Best Practices

1. Always log out after completing admin tasks
2. Never share admin credentials
3. Use strong, unique passwords
4. Enable two-factor authentication
5. Review audit logs regularly
6. Only access MT credentials when necessary for trade execution
7. Verify user identity before approving KYC
