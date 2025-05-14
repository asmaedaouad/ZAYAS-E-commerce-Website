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
