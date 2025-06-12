@echo off
echo Setting up Shilpa Sawiya database...

:: Check if MySQL is running
net start | findstr /C:"MySQL" > nul
if errorlevel 1 (
    echo MySQL service is not running. Please start MySQL service in XAMPP Control Panel first.
    pause
    exit /b 1
)

:: Create database and import schema
mysql -u root -e "CREATE DATABASE IF NOT EXISTS shilpa_sawiya;"
echo Database created successfully.

mysql -u root shilpa_sawiya < database.sql
echo Database schema imported successfully.

echo Setup completed successfully!
echo You can now access the application at: http://localhost/shilpa-sawiya
echo.
pause
