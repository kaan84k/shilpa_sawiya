# Shilpa Sawiya - Educational Material Donation Platform

A web-based platform for educational material donation and requests where users can act as both donors and requesters.

## Setup Instructions

1. Clone the repository and install PHP (>=7.4) and MySQL on your system.
   The project can run on Linux, macOS or Windows.

2. Create the database:
   - Create a MySQL database named `shilpa_sawiya`.
   - Import the schema from `database.sql` (or the migration files).

3. Environment configuration:
   - Copy `.env.example` to `.env` and update the database credentials.
   - The application reads these values at runtime.

4. Serve the application:
   - Point your web server's document root to the `public/` directory **or**
     run `php -S localhost:8000 -t public` for quick testing.

5. Access the application by navigating to `http://localhost:8000` in your
   browser (or your configured virtual host).

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
- `tests/` - contains a simple test suite. Run `tests/run_tests.sh`
  to execute the tests.
- `.env.example` - sample environment configuration file


## Technology Stack

- Frontend: HTML5, CSS3, JavaScript, Bootstrap 5
- Backend: PHP
- Database: MySQL
- Server: WAMP or any PHP web server

## API Endpoints

The project exposes a few JSON endpoints for administration:

- `admin/content/DonationController.php` – manage donations (`action=list|get|update|delete`).
- `admin/content/RequestController.php` – manage requests (`action=list|get|update|delete`).

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

## Future Improvements

The current codebase is intentionally lightweight. Adopting a small framework or
a routing library would help keep the controllers simple and maintainable.

## License

This project is licensed under the MIT License - see the LICENSE file for details.
