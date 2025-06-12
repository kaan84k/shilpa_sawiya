# Shilpa Sawiya - Educational Material Donation Platform

A web-based platform for educational material donation and requests where users can act as both donors and requesters.

## Setup Instructions

1. Install WAMP Server:
   - Download and install WAMP from https://www.wampserver.com/
   - Start WAMP services

2. Create Database:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named 'shilpa_sawiya'
   - Import the database schema from `database.sql`

3. Configure Database:
   - Open `config/database.php`
   - Update database credentials if needed (default: root/blank password)

4. Place Files:
   - Copy all project files to `C:\wamp\www\shilpa-sawiya`
   - Ensure your web server's document root points to the `public/` directory.
   - Run `composer install` and `composer dump-autoload` to set up autoloading.

5. Access the Application:
   - Open browser and go to http://localhost/shilpa-sawiya

## Features

- User Registration and Login
- Donation Posting and Management
- Request Creation and Management
- Custom Request System
- User Dashboard
- Notification System
- Responsive Design
## Project Structure
- `public/` - web server document root containing all entry PHP files and assets
- `src/` - application code organized into `Models`, `Controllers`, and `Views`
- `config/` - configuration scripts
- `tests/` - place for automated tests


## Technology Stack

- Frontend: HTML5, CSS3, JavaScript, Bootstrap 5
- Backend: PHP
- Database: MySQL
- Server: WAMP

## Security Features

- Password hashing
- SQL injection prevention
- Session management
- Input validation

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
