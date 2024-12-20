-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password should be hashed in production)
INSERT INTO admin_users (username, password, email) 
VALUES ('admin', 'admin123', 'admin@ayesrental.ph');

-- Rentals Table
CREATE TABLE IF NOT EXISTS rentals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    camera_id INT NOT NULL,
    rental_date DATE NOT NULL,
    return_date DATE NOT NULL,
    status ENUM('Pending', 'Active', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (camera_id) REFERENCES cameras(id)
);

-- Add status column to cameras table if not exists
ALTER TABLE cameras ADD COLUMN IF NOT EXISTS 
    status ENUM('Available', 'Rented', 'Maintenance') DEFAULT 'Available';
