-- Insert sample food products
INSERT INTO products (nama_makanan, harga) VALUES 
('Ichiraku Ramen', 18000),
('Pasta Carbonara', 16000),
('Samyang Noodle', 12000),
('Mie Ayam', 12000);

-- Insert sample customer
INSERT INTO customers (nama_customer, email, telepon) VALUES 
('Walk-in Customer', 'walkin@example.com', '-');

-- Insert sample users
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@foodshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('ezra', 'ezioradjangoding@gmail.com', '$2y$10$Vfarl9Wj713SHOdYZQgHxemRCmL3IYQQrAnh12LdHLV8pgduHyPji', 'user');