-- Add admin column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT FALSE;

-- Create default admin user if not exists (password: admin123)
INSERT INTO users (username, email, password, is_admin) 
VALUES ('admin', 'admin@ayesrental.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE)
ON DUPLICATE KEY UPDATE is_admin = TRUE;

-- Add status column to cameras if not exists
ALTER TABLE cameras ADD COLUMN IF NOT EXISTS status 
    ENUM('Available', 'Rented', 'Maintenance') DEFAULT 'Available';
