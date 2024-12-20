# Camera Rental Website

A PHP-based website for camera rentals with admin panel and user management.

## Prerequisites

- XAMPP (or similar local server environment)
- PHP 7.4 or higher
- MySQL
- Git

## Installation

1. Clone the repository:
```bash
git clone [your-repository-url]
```

2. Place the files in your XAMPP's htdocs folder:
```bash
C:/xampp/htdocs/camera-rental-website/
```

3. Import the database:
- Start XAMPP and ensure MySQL service is running
- Open phpMyAdmin (http://localhost/phpmyadmin)
- Create a new database named 'camera_rental'
- Import the database.sql file from the project's root directory

4. Configure the database connection:
- Copy config.example.php to config.php
- Update the database credentials in config.php

## Running the Website

1. Start XAMPP:
   - Start Apache service
   - Start MySQL service

2. Access the website:
   - Main website: http://localhost/camera-rental-website
   - Admin panel: http://localhost/camera-rental-website/admin

## Default Admin Login
- Username: admin
- Password: admin123

## Features
- User Registration and Login
- Camera Listing and Search
- Rental Management
- Admin Dashboard
- User Management
- Camera Management

## File Structure
```
camera-rental-website/
├── admin/             # Admin panel files
├── assets/            # CSS, JS, and images
├── includes/          # PHP includes
├── uploads/           # Uploaded images
└── database.sql       # Database structure
```

## Contributing
1. Create a new branch for your feature
2. Make your changes
3. Submit a pull request

## Support
Contact the team for support:
[Your contact information]
