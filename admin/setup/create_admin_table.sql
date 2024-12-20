CREATE TABLE IF NOT EXISTS admins (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin user (email: admin@example.com, password: admin123)
INSERT INTO admins (email, password) 
SELECT 'admin@example.com', '$2y$10$8KzO8Pzz6QY6ElX6QX9YUuqx5hkxiJ5pZH5n5lJ5J5J5J5J5J5'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE email = 'admin@example.com');
