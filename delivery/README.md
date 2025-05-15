# ZAYAS Delivery System

This is the delivery management system for ZAYAS e-commerce website. It allows delivery personnel to manage their assigned deliveries.

## Features

- Delivery personnel login
- Dashboard with delivery statistics
- View assigned deliveries
- Update delivery status
- View delivery details
- Profile management

## Setup

1. Make sure the main ZAYAS e-commerce system is set up and running
2. Update the database schema by running the SQL commands in `setup.sql`
3. Create delivery personnel accounts by setting `is_delivery = 1` in the users table
4. Assign deliveries to personnel using the admin interface

## Usage

1. Delivery personnel can log in at `/delivery/login.php`
2. Default credentials for testing:
   - Email: delivery@zayas.com
   - Password: 123456
3. After login, they will be redirected to the dashboard
4. They can view and manage their assigned deliveries
5. They can update delivery status as needed

## Delivery Status Flow

The typical flow of a delivery is:

```
Assigned → In-Transit → Delivered
```

Alternative flows include:

```
Assigned → In-Transit → Delivered → Returned
```

Note: Delivery personnel cannot cancel orders. Cancellations can only be done by administrators.

The system uses the following statuses:

1. **Pending** - Initial status, not yet assigned to delivery personnel (managed by admin)
2. **Assigned** - Admin has assigned the order to a delivery person, but the delivery person hasn't accepted it yet
3. **In-Transit** - Delivery person has accepted the order and is actively delivering it
4. **Delivered** - Successfully delivered to customer
5. **Returned** - Delivery was returned after delivery attempt

## Integration with Admin System

- Admin assigns deliveries to delivery personnel
- Delivery personnel update delivery status
- Admin can view all deliveries and their status
- Status changes are synchronized between delivery and admin systems
