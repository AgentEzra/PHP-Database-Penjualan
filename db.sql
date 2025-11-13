-- Table 1: Products (food)
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_makanan VARCHAR(50) NOT NULL,
    harga INT NOT NULL
);

-- Table 2: Customers (if needed for future expansion)
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_customer VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telepon VARCHAR(20)
);

-- Table 3: Orders (transaksi penjualan)
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    total_harga INT NOT NULL,
    waktu_terjual TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Table 4: Order Items (detail pesanan)
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Table: Users for authentication
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);