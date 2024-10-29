
Mock Fintech Deployment Guide
Prerequisites
PHP (>= 8.2), Composer
Database: MySQL or preferred database
Web Server: Apache
Environment: .env file with appropriate configurations
D
eployment Steps
1. Clone the Repository


git clone <repository-url>
cd <project-directory>

2. Install Dependencies
Backend: Install PHP dependencies
composer install


3. Run Migrations and Seeders

php artisan migrate --seed

4. Set File Permissions

# Adjust storage and cache directories
chmod -R 775 storage bootstrap/cache

5. Start the Application
For local testing:
php artisan serve

