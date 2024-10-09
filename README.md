# Blog Platform API

This is a RESTful API for a blog platform built using Laravel 10. The API supports user management with role-based access control and CRUD operations for blog posts, Comments and Users.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)

## Features

- User registration and login using JWT authentication
- Role-based permissions (Admin and Author)
- CRUD operations for blog posts, comments and users (by admin only)
- Filtering and searching blog posts
- Commenting on blog posts
- Caching for improved performance
- Unit test for posts

## Requirements

- PHP >= 8.0
- Laravel 10
- SQLite (or another database of your choice)
- Composer
- Node.js and npm (for front-end development, if applicable)

## Installation
1. Clone the repository:

   git clone https://github.com/AhmedKamal71/Laravel-Blog-Platform.git

2. Navigate to the project directory:

    cd blog-platform

3. Install dependencies:

    composer install

4. Copy the example environment file:

    cp .env.example .env

5. Generate an application key:

    php artisan key:generate

6. Create database sqlite file:

    touch database/database.sqlite

7. Set up your database in the .env file For SQLite:

    DB_CONNECTION=sqlite
    DB_DATABASE=/absolute/path/to/database.sqlite

8. Run migrations to create database table:

    php artisan migrate

9. start the local development server:

    php artisan serve

## Usage
- The API will be accessible at http://localhost:8000.

# API Endpoints
- User Authentication
    POST /api/register  - Register a new user
    POST /api/login     - Log in an existing user

- Blog Posts
    GET /api/posts           - List all blog posts (with filtering)
    GET /api/posts/{id}      - Show a single blog post
    POST /api/posts          - Create a new blog post
    PUT /api/posts/{id}      - Update a blog post
    DELETE /api/posts/{id}   - Delete a blog post

- Comments
    POST /api/posts/{id}/comments   - Add a comment to a post
    GET /api/comments               - List all comments
    PUT /api/comments/{id}          - Update Comment
    DELETE /api/posts/{id}          - Delete Comment

- Users (For admin role)
    GET /api/users           - List all users
    GET /api/users/{id}      - Show a single user
    DELETE /api/users/{id}   - Delete a user

# Testing
- To run tests, use the following command:
    php artisan test

# Notes
1. To register a new author in postman you can use this data:
{
    "name": "Ahmed Kamal",
    "email": "ahmed@gmail.com",
    "password": "password123",
    "password_confirmation": "password123",
}

2. To register a new admin in postman you can use this data :
{
    "name": "Admin",
    "email": "admin@gmail.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
}

3. Then login using this new email, the response will be a token

4. To create a new post you will need to send this token with the header in the request:
    Authorization   Bearer => the token <=

5. Any request to add comment or create new post you will need the token
6. To use search and filteration you need to send the word you want to search for as a parameter in the request:
   =>   GET http://localhost:8000/api/posts?category=News

 











