# Activity Log Implementation

## Overview
Comprehensive activity logging system using Spatie Laravel Activity Log package to track all administrative actions and model changes for security and compliance.

---

## Package Information

**Package**: `spatie/laravel-activitylog`  
**Documentation**: https://spatie.be/docs/laravel-activitylog

---

## Logged Models

### 1. User Model
**Log Name**: `user`

**Tracked Fields**:
- `full_name`
- `email`
- `role`
- `country`

**Events Logged**:
- User created
- User updated (only dirty fields)
- User deleted

---

### 2. Verification Model
**Log Name**: `verification`

**Tracked Fields**:
- `status`
- `rejection_reason`
- `user_id`

**Events Logged**:
- Verification created
- Verification updated (status changes, rejection reasons)
- Verification deleted

**Use Cases**:
- Track KYC approval/rejection
- Monitor verification status changes
- Audit compliance requirements

---

### 3. Signal Model
**Log Name**: `signal`

**Tracked Fields**:
- `symbol`
- `action`
- `status`
- `entry_price`
- `stop_loss`
- `take_profit_1`
- `pips_result`

**Events Logged**:
- Signal created
- Signal updated (status changes, price modifications)
- Signal deleted

**Use Cases**:
- Track signal creation and modifications
- Monitor signal performance updates
- Audit trading decisions

---

## Activity Log Resource

### Location
`/admin/activity-logs`

### Features

#### Table Columns:
1. **Log Type** - Badge showing the model type (Verification, Signal, User)
2. **Event** - Description of what happened (created, updated, deleted)
3. **Model** - The type of model affected
4. **ID** - The ID of the affected model
5. **User** - Who performed the action (or "System")
6. **Changes** - Summary of what changed (with tooltip for full details)
7. **Date** - When the action occurred

#### Filters:
- **Log Type**: Filter by Verification, Signal, or User
- **Event**: Filter by Created, Updated, or Deleted

#### Sorting:
- Default: Most recent first
- All columns sortable

#### Pagination:
- Options: 10, 25, 50, 100 records per page

---

## Color Coding

**Log Type Badges**:
- Verification: Yellow (Warning)
- Signal: Blue (Info)
- User: Green (Success)
- Other: Gray

---

## Automatic Logging

### Configuration
Activity logging is automatic for all tracked models. No manual logging required for CRUD operations.

### What Gets Logged:
- **Only Dirty Fields**: Only changed fields are logged
- **No Empty Logs**: Logs are not created if nothing changed
- **Causer**: The authenticated user who made the change
- **Properties**: Old and new values for changed fields

### Example Log Entry:
```json
{
  "log_name": "verification",
  "description": "Verification updated",
  "subject_type": "App\\Models\\Verification",
  "subject_id": 1,
  "causer_type": "App\\Models\\User",
  "causer_id": 1,
  "properties": {
    "attributes": {
      "status": "approved"
    },
    "old": {
      "status": "pending"
    }
  },
  "created_at": "2026-03-08 04:30:00"
}
```

---

## Manual Logging

For custom events not tied to model changes:

```php
use Spatie\Activitylog\Models\Activity;

// Log a custom activity
activity()
    ->causedBy(auth()->user())
    ->performedOn($model)
    ->withProperties(['key' => 'value'])
    ->log('Custom event description');

// Log without a model
activity()
    ->causedBy(auth()->user())
    ->log('Admin logged in');
```

---

## Adding Activity Logging to New Models

### Step 1: Add Trait and Use Statement
```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class YourModel extends Model
{
    use LogsActivity;
    
    // ... other code
}
```

### Step 2: Configure Logging Options
```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['field1', 'field2', 'field3'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => "Model {$eventName}")
        ->useLogName('model_name');
}
```

---

## Configuration Options

### Log Options Methods:

**`logOnly(array $attributes)`**
- Specify which attributes to log
- Only these fields will be tracked

**`logOnlyDirty()`**
- Only log when attributes actually change
- Prevents duplicate logs

**`dontSubmitEmptyLogs()`**
- Don't create log if nothing changed
- Keeps database clean

**`setDescriptionForEvent(Closure $callback)`**
- Customize the event description
- Use closure for dynamic descriptions

**`useLogName(string $logName)`**
- Set the log category name
- Used for filtering and organization

**`logAll()`**
- Log all model attributes
- Use with caution (may log sensitive data)

**`dontLogIfAttributesChangedOnly(array $attributes)`**
- Don't log if only these attributes changed
- Useful for timestamps

---

## Database Schema

### activity_log Table:
```sql
- id (bigint)
- log_name (string) - Category/type of log
- description (text) - Event description
- subject_type (string) - Model class name
- subject_id (bigint) - Model ID
- causer_type (string) - User class name
- causer_id (bigint) - User ID
- properties (json) - Old and new values
- event (string) - created, updated, deleted
- batch_uuid (uuid) - Group related activities
- created_at (timestamp)
- updated_at (timestamp)
```

---

## Querying Activity Logs

### Get All Activities:
```php
$activities = Activity::all();
```

### Get Activities for a Model:
```php
$activities = Activity::forSubject($model)->get();
```

### Get Activities by a User:
```php
$activities = Activity::causedBy($user)->get();
```

### Get Activities by Log Name:
```php
$activities = Activity::inLog('verification')->get();
```

### Get Recent Activities:
```php
$activities = Activity::latest()->take(10)->get();
```

---

## Security Considerations

### Sensitive Data:
- **Passwords**: Never logged (not in logOnly arrays)
- **API Keys**: Not logged
- **Personal Data**: Only log what's necessary for audit

### Access Control:
- Activity logs only accessible to admin users
- No delete functionality (read-only)
- No edit functionality

### Data Retention:
Consider implementing automatic cleanup:
```php
// In a scheduled command
Activity::where('created_at', '<', now()->subMonths(6))->delete();
```

---

## Performance Considerations

### Indexes:
The migration includes indexes on:
- `(log_name, created_at)`
- `(subject_type, subject_id)`
- `(causer_type, causer_id)`

### Optimization Tips:
1. Only log necessary fields
2. Use `logOnlyDirty()` to reduce log volume
3. Implement data retention policy
4. Consider archiving old logs

---

## Compliance & Audit

### GDPR Compliance:
- Activity logs may contain personal data
- Include in data export requests
- Delete logs when user requests data deletion

### Audit Trail:
- Immutable record of all changes
- Tracks who, what, when
- Useful for compliance audits
- Can be exported for external review

---

## Future Enhancements

### Planned Features:
1. **Export Functionality**: Export logs to CSV/PDF
2. **Advanced Filtering**: Date range, multiple users
3. **Dashboard Widget**: Recent activity widget
4. **Email Notifications**: Alert on critical changes
5. **Detailed View**: Full log entry details page
6. **Restore Functionality**: Revert changes from logs
7. **Batch Operations**: View grouped activities

---

## Troubleshooting

### Logs Not Being Created:
1. Check if trait is added to model
2. Verify `getActivitylogOptions()` is implemented
3. Ensure fields are in `logOnly` array
4. Check if `logOnlyDirty()` is preventing logs

### Missing Causer:
- Logs created outside authenticated context show no causer
- System operations won't have a causer
- Use `causedBy()` for manual logging

### Performance Issues:
1. Add database indexes
2. Implement data retention
3. Reduce logged fields
4. Use `logOnlyDirty()`

---

## Testing

### Manual Testing:
1. Create a user → Check activity log
2. Update verification status → Check log
3. Create/update signal → Check log
4. Filter by log type
5. Search by user
6. Verify changes are tracked correctly

### Automated Testing:
```php
public function test_verification_status_change_is_logged()
{
    $verification = Verification::factory()->create(['status' => 'pending']);
    
    $verification->update(['status' => 'approved']);
    
    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Verification::class,
        'subject_id' => $verification->id,
        'description' => 'Verification updated',
    ]);
}
```

---

## Notes

- Activity logs are read-only in the admin panel
- All CRUD operations are automatically logged
- Custom events can be logged manually
- Logs include IP address and user agent
- Changes are stored as JSON in properties column
- Old and new values are preserved
