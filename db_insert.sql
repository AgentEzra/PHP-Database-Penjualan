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

-- insert sample users | this is the fourth
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@coffeeshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
('ezra', 'ezioradjangoding@gmail.com', '$2y$10$Vfarl9Wj713SHOdYZQgHxemRCmL3IYQQrAnh12LdHLV8pgduHyPji', 'member')



-- Reminder
-- do not paste all the code, just copy each portion - ezra