# Contact Management API

This is a simple API for managing contacts, built with the Laravel framework.

## Features

-   User registration and authentication (Login/Logout)
-   CRUD (Create, Read, Update, Delete) operations for contacts
-   Search for contacts by name, email, or phone
-   API documentation using OpenAPI (Swagger)

## API Endpoints

A full description of the API endpoints can be found in the OpenAPI specification file located at `storage/api-docs/api-docs.json`.

Here is a summary of the available endpoints:

**User Authentication**

-   `POST /api/users/register` - Register a new user
-   `POST /api/users/login` - Log in a user
-   `POST /api/users/logout` - Log out the current user (Authentication required)
-   `GET /api/users` - Get current user details (Authentication required)
-   `PATCH /api/users` - Update current user details (Authentication required)

**Contacts**

-   `POST /api/contact` - Create a new contact (Authentication required)
-   `GET /api/contacts` - Get a list of all contacts for the current user (Authentication required)
-   `GET /api/contact/{id}` - Get a specific contact by its ID (Authentication required)
-   `PATCH /api/contact/{id}` - Update a specific contact (Authentication required)
-   `DELETE /api/contact/{id}` - Delete a specific contact (Authentication required)
-   `GET /api/contacts/search` - Search for contacts (Authentication required)

## How to Run Locally

Follow these steps to set up and run the project on your local machine.

### Prerequisites

-   PHP (>= 8.1)
-   Composer
-   Node.js & NPM

### 1. Clone the Repository

```bash
git clone https://github.com/frdiskandr/Laravel-Restfull-Api.git
cd Laravel-Restfull-api
```

### 2. Install Dependencies

Install the PHP and JavaScript dependencies.

```bash
composer install
npm install
```

### 3. Set Up Environment

Create your environment file and generate the application key.

```bash
copy .env.example .env
php artisan key:generate
```

This project is configured to use SQLite by default. The database file `database/database.sqlite` is already included. If you want to use another database, update the `DB_*` variables in your `.env` file.

### 4. Run Database Migrations

Run the database migrations and seeders to create the necessary tables and populate them with initial data.

```bash
php artisan migrate --seed
```

### 5. Start the Development Server

Now you can start the Laravel development server.

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`.
