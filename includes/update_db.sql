-- Add admin column to users table
ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE;

-- Set default admin user
UPDATE users SET is_admin = TRUE WHERE username = 'admin';

-- If admin doesn't exist, create one
INSERT IGNORE INTO users (username, email, password, is_admin) 
VALUES ('admin', 'admin@ayesrental.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);
