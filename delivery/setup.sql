-- Add delivery personnel user (password: 123456)
INSERT INTO users (first_name, last_name, email, password, phone, is_delivery)
VALUES ('Delivery', 'Person', 'delivery@zayas.com', '$2y$10$8SOl8Krb3.WsALaIzGZYYOZOvCMxG0UbqV5PClUGCk0Kn8JCmgjhm', '123-456-7890', 1);

-- Add personnel_id column to delivery table if it doesn't exist
ALTER TABLE delivery ADD COLUMN IF NOT EXISTS personnel_id INT;
ALTER TABLE delivery ADD FOREIGN KEY IF NOT EXISTS (personnel_id) REFERENCES users(id) ON DELETE SET NULL;

-- Update some existing orders to be assigned to the delivery personnel
-- First, get the ID of the delivery personnel
SET @delivery_id = (SELECT id FROM users WHERE email = 'delivery@zayas.com' AND is_delivery = 1);

-- Update some deliveries to be assigned to this personnel
UPDATE delivery SET personnel_id = @delivery_id, delivery_status = 'assigned' WHERE id IN (SELECT id FROM (SELECT id FROM delivery ORDER BY id LIMIT 3) as temp);

-- Update corresponding orders
UPDATE orders o
JOIN delivery d ON o.id = d.order_id
SET o.status = 'assigned'
WHERE d.personnel_id = @delivery_id AND d.delivery_status = 'assigned';

-- Set one delivery to in_transit
UPDATE delivery SET delivery_status = 'in_transit' WHERE personnel_id = @delivery_id LIMIT 1;

-- Update corresponding order
UPDATE orders o
JOIN delivery d ON o.id = d.order_id
SET o.status = 'in_transit'
WHERE d.personnel_id = @delivery_id AND d.delivery_status = 'in_transit';
