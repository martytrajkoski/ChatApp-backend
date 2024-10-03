<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

# Laravel Application Setup with Laragon

This guide will walk you through the steps to set up and run a Laravel application using Laragon. We will ensure that the database connection matches Laragon’s MySQL configuration, install the necessary dependencies, and run the necessary commands for queuing and broadcasting.

## Prerequisites

Make sure you have the following installed:

- **[Laragon](https://laragon.org/)**: A modern development environment for PHP, MySQL, and other tools.

## 1. Clone the repository

1. Navigate to the `www` folder in your Laragon installation. This is typically located at `C:\laragon\www`.
2. Open a terminal (Command Prompt or Git Bash) and run the following command to clone your project:

```bash
git clone https://github.com/martytrajkoski/ChatApp-backend.git
```

## 2. Set up .env file
In Laravel, the .env file contains configuration values for your application. You need to update the database settings to match Laragon’s MySQL configuration.

1. Navigate to the project directory:
```bash
cd ChatApp-backend
```
2. Open the .env file located in the root of your project.
3. Update the following database fields to match your Laragon MySQL settings:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chatApp-database
DB_USERNAME=root
DB_PASSWORD=
```
By default, Laragon's MySQL username is root and the password is empty.

To create a new database in Laragon:
- Open Laragon.
- Click on Menu → Database → MySQL.
- Open phpMyAdmin or connect via your preferred MySQL client and create a new database (your_database_name as defined in the .env file).

## 3. Install dependencies
Now that the database is configured, install the necessary Laravel dependencies using Composer:

```bash
composer install
```

## 4. Run migrations and seeders
Once the dependencies are installed and the database is set up, you can run Laravel's migrations and seeders:

```bash
php artisan migrate --seed
```
This will create the necessary tables in the database and seed any test data if specified.

## 5. Set up Laravel Passport
Run the following command to generate new personal access client IDs:
```bash
php artisan passport:client --personal
```
Replace the PASSPORT_PERSONAL_ACCESS_CLIENT_ID and PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET in your .env file with the values provided after running the previous command.

## 6. Start Laragon services
1. Open Laragon.
2. Make sure Apache and MySQL services are running. Click on the Start All button if they are not already running.

## 7. Start the queue worker
To reduce latency in processing background tasks, run the queue worker with:
```bash
php artisan queue:work
```
This command will start listening for jobs on the queue and process them.

## 8. Start the broadcasting server
For real-time broadcasting features in Laravel, you can start the broadcasting server (using Reverb) with:

```bash
php artisan reverb:start
```
This will enable real-time communication for events like chat messages, notifications, etc.

## 9. Access the application
Now you can access your Laravel application by navigating to the following URL in your browser:

```bash
http://http://chatapp-backend.test/
```

## Additional Notes
- Queue Worker: The queue:work command should always be running in the background to process queued jobs. You can use a process manager like Supervisor to manage this in production.
- Broadcasting: Ensure your .env is properly set up for broadcasting with Reverb. Typically, this involves configuring the broadcasting settings and making sure the relevant credentials are set.

```bash
BROADCAST_DRIVER=reverb
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```
-Caching: For performance, consider running the following commands after setting up:
```bash
php artisan config:cache
php artisan route:cache
```
