-- Insert sample products | this is the first one to execute
INSERT INTO products (nama_kopi, harga) VALUES 
('Espresso', 25000),
('Cappuccino', 30000),
('Latte', 32000),
('Americano', 28000);

-- Insert sample customer | this is the second
INSERT INTO customers (nama_customer, email, telepon) VALUES 
('Walk-in Customer', 'walkin@example.com', '-');

-- Insert sample order | this is the third
INSERT INTO orders (customer_id, total_harga) VALUES 
(1, 85000);

-- do not paste all the code, just copy each portion - ezra