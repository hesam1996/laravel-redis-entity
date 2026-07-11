<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Demo & Testing

This repository includes a fully functional Laravel application to demonstrate how the package works in a real-world scenario. 

* **Seed Data:** I have generated **3,000 dummy product records** using Laravel Factories so you can immediately test the Read-Through caching and Cache Stampede protection under simulated load. Feel free to modify or add more data as you see fit.

### Prerequisites & Configuration

To run the demo successfully, please ensure the following:

1. **Redis Server:** You must have Redis running on your system. 
   > 💡 *Note: During development, I used a Docker container to run Redis quickly.*
2. **PHP Redis Client:** The package requires a PHP client to communicate with Redis. For this demo, `predis/predis` has already been installed via Composer.
3. **Environment Setup:** Make sure to correctly configure your `.env` file with your database and Redis credentials before running the application:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```
