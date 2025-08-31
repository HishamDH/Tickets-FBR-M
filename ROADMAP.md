# Project Documentation for Replication

This document provides a comprehensive technical analysis of the project, with the goal of enabling its replication using Laravel v10 and Filament v3.

## 1. Project Overview & Architecture

### Project Type, Purpose, and Core Functionality

The project is a multi-purpose, multi-tenant marketplace platform that connects merchants with customers. It allows merchants to create and manage various types of "offerings," such as events, conferences, restaurant reservations, and other experiences. Customers can browse these offerings, make reservations, and pay for them through the platform.

The platform supports three main user roles:

*   **Admin:** Manages the platform, including merchants, withdrawals, and support.
*   **Merchant:** Manages their own offerings, branches, reservations, and finances.
*   **Customer:** Browses offerings, makes reservations, and manages their own profile and tickets.

The system also appears to have a "sub-merchant" or "managed merchant" functionality, allowing for a hierarchical management structure.

### Overall System Architecture and Design Patterns Used

The application is built on the **Laravel framework (version 10)**, following a traditional **Model-View-Controller (MVC)** architecture. It heavily utilizes **Livewire** for building dynamic interfaces, which suggests a modern, interactive user experience without a separate frontend framework like Vue or React.

The use of `laravel/fortify` indicates a robust authentication system, while `spatie/laravel-permission` provides a flexible role-based access control (RBAC) system. The application is divided into several distinct sections (admin, merchant, customer), each with its own dashboard and authentication.

### Technology Stack Identification

*   **Backend:**
    *   PHP 8.1+
    *   Laravel 10
    *   Livewire 3
    *   Laravel Fortify (for authentication)
    *   Spatie Laravel Permission (for roles and permissions)
    *   MySQL (assumed from the use of Laravel)

*   **Frontend:**
    *   Vite (for asset bundling)
    *   Tailwind CSS
    *   TUI Calendar (for calendar functionality)
    *   Blade (as the templating engine)
    *   Livewire (for dynamic components)

*   **External Services:**
    *   `ticket-window.ottu.com` (for payment processing)

## 2. Database Structure & Relationships

The database schema is defined through a series of migrations. Below is a breakdown of the key tables and their relationships.

### `users`

This table stores information about all users, regardless of their role.

| Field Name        | Data Type           | Constraints                               | Description                                                                 |
| ----------------- | ------------------- | ----------------------------------------- | --------------------------------------------------------------------------- |
| `id`              | `bigint` (unsigned) | Primary Key                               | Unique identifier for the user.                                             |
| `f_name`          | `string`            |                                           | User's first name.                                                          |
| `l_name`          | `string`            |                                           | User's last name.                                                           |
| `email`           | `string`            | Unique                                    | User's email address.                                                       |
| `business_name`   | `string`            | Nullable                                  | The name of the merchant's business.                                        |
| `business_type`   | `enum`              | 'restaurant', 'events', 'show', 'other'   | The type of the merchant's business.                                        |
| `email_verified_at`| `timestamp`         | Nullable                                  | Timestamp of email verification.                                            |
| `password`        | `string`            |                                           | Hashed password for the user.                                               |
| `role`            | `enum`              | 'admin', 'merchant', 'user'               | The role of the user within the system.                                     |
| `phone`           | `string`            | Nullable                                  | User's phone number.                                                        |
| `additional_data` | `json`              | Nullable                                  | A flexible field for storing extra user data.                               |
| `is_accepted`     | `boolean`           | Default: `false`                          | A flag to indicate if a merchant's application has been accepted by an admin. |
| `remember_token`  | `string`            |                                           | Token for "remember me" functionality.                                      |
| `timestamps`      | `timestamp`         |                                           | `created_at` and `updated_at` timestamps.                                   |

### `offerings`

This table stores the various offerings created by merchants.

| Field Name        | Data Type           | Constraints                               | Description                                                                 |
| ----------------- | ------------------- | ----------------------------------------- | --------------------------------------------------------------------------- |
| `id`              | `bigint` (unsigned) | Primary Key                               | Unique identifier for the offering.                                         |
| `name`            | `string`            | Nullable                                  | The name of the offering.                                                   |
| `location`        | `string`            | Nullable                                  | The location of the offering.                                               |
| `description`     | `string`            | Nullable                                  | A description of the offering.                                              |
| `image`           | `string`            | Nullable                                  | URL or path to an image for the offering.                                   |
| `price`           | `decimal`           | Nullable                                  | The price of the offering.                                                  |
| `start_time`      | `dateTime`          | Nullable                                  | The start time of the event or offering.                                    |
| `end_time`        | `dateTime`          | Nullable                                  | The end time of the event or offering.                                      |
| `status`          | `enum`              | 'active', 'inactive'                      | The status of the offering.                                                 |
| `type`            | `enum`              | 'events', 'conference', 'restaurant', 'experiences' | The type of the offering.                                                   |
| `category`        | `string`            | Nullable                                  | The category of the offering.                                               |
| `additional_data` | `json`              | Nullable                                  | A flexible field for storing extra offering data.                           |
| `translations`    | `json`              | Nullable                                  | A field for storing translations of the offering's content.                 |
| `has_chairs`      | `boolean`           | Default: `false`                          | A flag to indicate if the offering has chairs.                              |
| `chairs_count`    | `integer`           | Nullable                                  | The number of chairs available.                                             |
| `user_id`         | `bigint` (unsigned) | Foreign Key (`users.id`)                  | The ID of the merchant who created the offering.                            |
| `features`        | `json`              | Nullable                                  | A field for storing additional features of the offering.                    |
| `timestamps`      | `timestamp`         |                                           | `created_at` and `updated_at` timestamps.                                   |

### `branches`

This table stores the branches of a merchant.

| Field Name   | Data Type           | Constraints              | Description                               |
| ------------ | ------------------- | ------------------------ | ----------------------------------------- |
| `id`         | `bigint` (unsigned) | Primary Key              | Unique identifier for the branch.         |
| `name`       | `string`            |                          | The name of the branch.                   |
| `location`   | `string`            | Nullable                 | The location of the branch.               |
| `user_id`    | `bigint` (unsigned) | Foreign Key (`users.id`) | The ID of the merchant who owns the branch. |
| `timestamps` | `timestamp`         |                          | `created_at` and `updated_at` timestamps. |

### `paid_reservations`

This table stores information about paid reservations made by customers.

| Field Name        | Data Type           | Constraints              | Description                                                                 |
| ----------------- | ------------------- | ------------------------ | --------------------------------------------------------------------------- |
| `id`              | `bigint` (unsigned) | Primary Key              | Unique identifier for the reservation.                                      |
| `item_id`         | `bigint` (unsigned) |                          | The ID of the item being reserved (polymorphic).                            |
| `item_type`       | `string`            |                          | The type of the item being reserved (e.g., `App\Models\Offering`).          |
| `user_id`         | `bigint` (unsigned) | Foreign Key (`users.id`) | The ID of the customer who made the reservation.                            |
| `quantity`        | `double`            | Default: `1`             | The quantity of items reserved.                                             |
| `price`           | `double`            |                          | The price of the reservation.                                               |
| `discount`        | `double`            | Default: `0.0`           | The discount applied to the reservation.                                    |
| `code`            | `string`            | Unique                   | A unique code for the reservation.                                          |
| `additional_data` | `json`              | Nullable                 | A flexible field for storing extra reservation data.                        |
| `timestamps`      | `timestamp`         |                          | `created_at` and `updated_at` timestamps.                                   |

## 3. Backend Components (Laravel)

This section details the backend components of the application, including models, controllers, and middleware.

### Models

#### `User`

The `User` model represents all users in the system and uses the `spatie/laravel-permission` package for role management.

*   **Fillable Fields:** `f_name`, `l_name`, `email`, `business_name`, `business_type`, `email_verified_at`, `password`, `role`, `phone`, `additional_data`, `status`, `status_updated_at`, `rejection_reason`, `acceptance_note`
*   **Relationships:**
    *   `carts()`: `hasMany(Cart::class)`
    *   `branches()`: `hasMany(Branch::class)`
    *   `offers()`: `hasMany(Offering::class)`
    *   `roles()`: `belongsToMany(Role::class)`
    *   `wallet()`: `hasOne(MerchantWallet::class)`
    *   `reviews()`: `hasManyThrough(Customer_Ratings::class, Offering::class)`
*   **Custom Methods:**
    *   `isMerchant()`: Checks if the user has the 'merchant' role.

#### `Offering`

The `Offering` model represents the services or products offered by merchants.

*   **Fillable Fields:** `name`, `location`, `description`, `image`, `price`, `start_time`, `end_time`, `status`, `type`, `category`, `additional_data`, `translations`, `has_chairs`, `chairs_count`, `user_id`, `features`
*   **Casts:**
    *   `additional_data`: `array`
    *   `translations`: `array`
    *   `features`: `array`
    *   `start_time`: `datetime`
    *   `end_time`: `datetime`
    *   `has_chairs`: `boolean`
*   **Relationships:**
    *   `user()`: `belongsTo(User::class)`
    *   `Reservations()`: `hasMany(PaidReservation::class)`
    *   `Reviwes()`: `hasMany(Customer_Ratings::class)` (Note: typo in the original code, should be `Reviews`)

### Controllers

#### `AuthController`

This controller is responsible for handling user authentication for all roles.

*   **`store(Request $request)`:** Handles the registration of new users. It validates the input data and creates a new user with the appropriate role (`merchant` or `user`).
*   **`login(Request $request)`:** Handles the login process for all three roles (`admin`, `merchant`, `customer`) using different authentication guards.
*   **`logout(Request $request)`:** Logs out the currently authenticated merchant.
*   **`userLogout(Request $request)`:** Logs out the currently authenticated customer.
*   **`adminLogout(Request $request)`:** Logs out the currently authenticated admin.
*   **`dashboard()`:** Redirects authenticated users to their respective dashboards based on their role.
*   **Update Methods:** Contains several methods for updating user information, such as `update`, `update_settings`, `update_PS`, `update_password`, and `update_work`.

### Authentication and Authorization Systems

The application uses a combination of Laravel's built-in authentication features, Laravel Fortify, and the `spatie/laravel-permission` package.

*   **Authentication Guards:** The application uses three different authentication guards: `admin`, `merchant`, and `customer`. This allows for separate authentication for each user role.
*   **Role-Based Access Control (RBAC):** The `spatie/laravel-permission` package is used to manage roles and permissions. The `User` model uses the `HasRoles` trait, and the routes are protected by middleware that checks the user's role.
*   **Fortify:** Laravel Fortify is used for backend authentication logic, including registration, login, and password reset.
*   **Custom Middleware:** The application uses custom middleware, such as `RoleMiddleware` and `EnsureUserIsVerified`, to protect routes and enforce access control.

## 4. Frontend Components (Livewire)

The frontend of the application is built using **Livewire**, a full-stack framework for Laravel that allows developers to build dynamic interfaces with Blade and PHP. While the request is to replicate the project using Filament v3, it's important to understand the existing Livewire components to recreate the functionality.

### Livewire Component Analysis

The application is heavily component-based, with Livewire components handling most of the user interactions within the dashboards.

#### `Information.php` (Merchant Dashboard)

This component is part of the offer creation process and is responsible for managing the basic information of an offering.

*   **Functionality:** It handles the creation and updating of an offering's name, location, description, type, and category.
*   **Features:**
    *   **Real-time Validation:** It uses Livewire's `updated` hook to validate fields as the user types.
    *   **Automatic Saving:** Validated fields are immediately saved to the database, providing a seamless experience for the user.
    *   **Dynamic Fields:** The component dynamically adds or removes fields based on the selected offering type (e.g., adding a "location" question for mobile services).
*   **Replication in Filament:** This functionality can be replicated in Filament using a `Wizard` or a multi-step form, with conditional fields based on the selected offering type.

### General Frontend Structure

*   **Admin Panel:** The admin panel is located at `/admin/dashboard` and is composed of a series of Livewire components for managing merchants, withdrawals, and other platform settings.
*   **Merchant Panel:** The merchant panel is located at `/merchant/dashboard` and is also built with Livewire components. It provides a comprehensive set of tools for merchants to manage their offerings, reservations, and finances.
*   **Customer Panel:** The customer panel is located at `/customer/dashboard` and allows customers to manage their profile, view their tickets, and interact with the support system.

## 5. User Interface & User Experience

The user interface is divided into three main sections, each with its own navigation flow and access levels.

### Page Structure and Navigation Flow

*   **Public Pages:**
    *   Home (`/`)
    *   Pricing (`/pricing`)
    *   Login (`/login`)
    *   Register (`/register`)
    *   Merchant Template (`/{id}`)
    *   Offering View (`/{id}/{offering}`)
*   **Admin Dashboard (`/admin/dashboard`):**
    *   Overview
    *   Reports
    *   Setup
    *   Merchants
    *   Withdraws
    *   Support
    *   Public Reservations
    *   Employees
*   **Merchant Dashboard (`/merchant/dashboard`):**
    *   Overview
    *   Reservations
    *   Withdraw
    *   POS
    *   Statistics
    *   Work In
    *   Checking
    *   Customer Reviews
    *   Message Center
    *   Branch Management
    *   Offer Management
    *   Team Management
    *   Page Setup
    *   Policies Settings
    *   Support
    *   Activity Log
    *   Profile Setup
*   **Customer Dashboard (`/customer/dashboard`):**
    *   Overview
    *   Tickets
    *   Pay History
    *   Profile
    *   Support
    *   Settings
    *   Rewards
    *   Experiences

### User Roles and Access Levels

*   **Admin:** Has full access to the admin dashboard and can manage all aspects of the platform.
*   **Merchant:** Has access to the merchant dashboard and can manage their own offerings, branches, and finances. They can also have sub-users with specific permissions.
*   **Customer:** Has access to the customer dashboard and can manage their own profile, reservations, and tickets.

### Forms, Validation Rules, and User Interactions

*   **Forms:** The application uses standard HTML forms, enhanced with Livewire for dynamic interactions.
*   **Validation:** Validation is handled on the backend using Laravel's validation rules, and on the frontend in real-time using Livewire.
*   **User Interactions:** The use of Livewire suggests a highly interactive user experience, with features like real-time feedback, automatic saving, and dynamic content updates without full page reloads.

## 6. Integration Points & External Services

### Third-Party API Integrations

The primary third-party integration is with **`ticket-window.ottu.com`** for payment processing.

### Payment Gateways or External Services

*   **Ottu:** The application uses Ottu as its payment gateway. The integration is handled through a direct API call, as seen in the `routes/web.php` file.

### Email Systems and Notification Channels

*   **Laravel Mail:** The application likely uses Laravel's built-in mail functionality for sending emails, such as registration confirmations and notifications. The specific mail driver would be configured in the `.env` file.

### File Storage Solutions

*   **Local Storage:** The application uses the local filesystem for storing uploaded files, such as profile pictures and banners. This is configured in the `config/filesystems.php` file.

## 7. Configuration & Environment

### Required Environment Variables

To replicate the project, the following environment variables would need to be configured in the `.env` file:

*   `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
*   `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
*   `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
*   `OTTU_API_KEY`: The API key for the Ottu payment gateway.
*   `OTTU_MERCHANT_ID`: The merchant ID for the Ottu payment gateway.

### Package Dependencies and Versions

*   **PHP:** `^8.1`
*   **Laravel Framework:** `^10.10`
*   **Livewire:** `^3.6`
*   **Laravel Fortify:** `^1.27`
*   **Spatie Laravel Permission:** `^6.19`
*   **Guzzle:** `^7.9`
*   **Vite:** `^5.0.0`
*   **Tailwind CSS:** `^3.4.1`
*   **TUI Calendar:** `^1.15.3`
