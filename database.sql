-- Database: sistem_pemesanan_barang
-- Created for PHP Order Management System

CREATE DATABASE IF NOT EXISTS sistem_pemesanan_barang;
USE sistem_pemesanan_barang;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    avatar VARCHAR(255) DEFAULT 'default.png',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-folder',
    color VARCHAR(7) DEFAULT '#667eea',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: products
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(12,2) NOT NULL,
    stock INT DEFAULT 0,
    min_stock INT DEFAULT 10,
    unit VARCHAR(20) DEFAULT 'pcs',
    image VARCHAR(255),
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: customers
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    company VARCHAR(200),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    type ENUM('personal', 'company') DEFAULT 'personal',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: orders
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    order_date DATE NOT NULL,
    delivery_date DATE,
    total_amount DECIMAL(12,2) DEFAULT 0,
    discount DECIMAL(12,2) DEFAULT 0,
    tax DECIMAL(12,2) DEFAULT 0,
    grand_total DECIMAL(12,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    payment_method ENUM('cash', 'transfer', 'credit') DEFAULT 'cash',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default data
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@sistem.com', 'admin'),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager Gudang', 'manager@sistem.com', 'manager'),
('staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Penjualan', 'staff@sistem.com', 'staff');

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Elektronik', 'Produk elektronik dan gadget'),
('Pakaian', 'Pakaian dan fashion item'),
('Makanan', 'Makanan dan minuman'),
('Alat Tulis', 'Alat tulis kantor'),
('Perabotan', 'Perabotan rumah tangga');

-- Insert sample products
INSERT INTO products (category_id, sku, name, description, price, stock) VALUES
(1, 'ELEC-001', 'Laptop ASUS Vivobook', 'Laptop 14 inch, 8GB RAM, 512GB SSD', 8500000, 15),
(1, 'ELEC-002', 'Mouse Wireless', 'Mouse wireless dengan receiver USB', 250000, 50),
(2, 'CLOTH-001', 'Kaos Polo Pria', 'Kaos polo katun premium', 150000, 100),
(3, 'FOOD-001', 'Kopi Arabica 250gr', 'Kopi arabica kemasan 250gr', 75000, 200),
(4, 'STN-001', 'Buku Tulis A4', 'Buku tulis 58 lembar', 15000, 300);

-- Insert sample customers
INSERT INTO customers (customer_code, name, email, phone) VALUES
('CUST-001', 'Budi Santoso', 'budi@email.com', '081234567890'),
('CUST-002', 'Siti Nurhaliza', 'siti@email.com', '081987654321'),
('CUST-003', 'PT. Abadi Sentosa', 'info@abadisentosa.com', '02112345678');

-- Insert sample orders
INSERT INTO orders (order_number, customer_id, order_date, grand_total, status) VALUES
('ORD-2024001', 1, '2024-01-15', 8500000, 'completed'),
('ORD-2024002', 2, '2024-01-16', 250000, 'processing'),
('ORD-2024003', 3, '2024-01-17', 450000, 'pending');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES
(1, 1, 1, 8500000),
(2, 2, 1, 250000),
(3, 3, 3, 150000);