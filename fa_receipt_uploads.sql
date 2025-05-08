CREATE TABLE fa_receipt_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_hash VARCHAR(64) UNIQUE NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    vendor_name VARCHAR(255) NOT NULL,
    receipt_date DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed TINYINT(1) DEFAULT 0
);
