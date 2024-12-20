-- Add admin column to users table
ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE;

-- Update existing admin users or create new admin
INSERT INTO users (username, email, password, is_admin) 
VALUES ('admin', 'admin@ayesrental.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
ON DUPLICATE KEY UPDATE is_admin = 1;

-- Drop the separate admin_users table if it exists
DROP TABLE IF EXISTS admin_users;
