# Order Status System

This document explains the order status system used in the ZAYAS Simple e-commerce application.

## Order Statuses

The system uses the following order statuses:

1. **Pending** - Order has been placed but not yet assigned to a delivery person. This is the initial status of all orders.
2. **Assigned** - Order has been assigned to a delivery person but not yet in transit.
3. **In-Transit** - Order is on the way to the customer.
4. **Delivered** - Order has been successfully delivered to the customer.
5. **Cancelled** - Order was cancelled before it arrived at the customer.
6. **Returned** - Order was returned after it arrived at the customer.

## Status Flow

The typical flow of an order is:

```
Pending → Assigned → In-Transit → Delivered
```

Alternative flows include:

```
Pending → Cancelled
Pending → Assigned → Cancelled
Pending → Assigned → In-Transit → Cancelled
Pending → Assigned → In-Transit → Delivered → Returned
```

## Implementation Details

- The status is stored in the `orders` table in the `status` column.
- The delivery status is stored in the `delivery` table in the `delivery_status` column.
- Both columns use ENUM data types with the values: 'pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned'.

## Updating the Database

To update an existing database to use the new status system, run the SQL script `update_order_status.sql`.

```sql
-- Update orders table to use the new status values
ALTER TABLE orders 
MODIFY COLUMN status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned') DEFAULT 'pending';

-- Update delivery table to use the new status values
ALTER TABLE delivery
MODIFY COLUMN delivery_status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned') DEFAULT 'pending';

-- Update any existing 'processing' status to 'assigned'
UPDATE orders SET status = 'assigned' WHERE status = 'processing';

-- Update any existing 'shipped' status to 'in_transit'
UPDATE orders SET status = 'in_transit' WHERE status = 'shipped';
```
