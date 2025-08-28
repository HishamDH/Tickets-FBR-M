# [Product Roadmap: Shubak Tickets Customer Frontend]

## 1. Vision & Tech Stack
*   **Problem:** Customers need an easy-to-use, Arabic-language web interface to discover, book, and purchase tickets for services and events offered by providers on the platform.
*   **Proposed Solution:** Build an interactive frontend using Laravel Blade, enhanced with Livewire and Alpine.js to replicate the seamless user experience defined in the reference UI design.
*   **Tech Stack:** Laravel v10, Filament v3, PHP v8.1, Livewire, Alpine.js, Tailwind CSS.
*   **Applied Constraints & Preferences:** Strict adherence to the specified tech stack (Laravel/Filament), avoiding complex rendering solutions, and ensuring a precise replication of the reference UI.

## 2. Core Requirements (from Fact-Finding)
*   **Service Discovery:** Users must be able to easily browse, search, and filter services.
*   **Availability Display:** A calendar showing real-time booking availability.
*   **Simplified Booking Process:** Clear steps to select a service, date, and process payment.
*   **Secure Payment Gateway:** Integration with trusted payment gateways.
*   **Automated Confirmations:** Sending confirmation and notification emails/SMS.
*   **User Dashboard:** A personal area for users to manage and view their bookings.

## 3. Prioritized Functional Modules (Designed to meet the above requirements)
| Priority | Functional Module | Rationale (from Research & Tech Analysis) | Description (Includes grouped features) |
|:---:|:---|:---|:---|
| 1 | **Base Layout & Homepage** | Establish the main visual structure and apply Jakob's Law for a familiar UI. Showcase featured services to engage users immediately. | Create the main Blade layout files (app, guest), including a global Header and Footer. Build the static Homepage to display sections like "Most Popular Services" and "Latest Offers". |
| 2 | **Service Listing Page** | Fulfills the "Service Discovery" requirement. Allows users to browse all available options in one place. | Develop a page that displays all services as cards. Add basic filters by category (e.g., venues, catering) and a search bar. |
| 3 | **Service Detail Page** | Provide comprehensive information to help the user make a booking decision. Displaying availability is a core feature. | Display all service details: images, description, features, price. Most importantly, include an **Interactive Calendar** to show available dates and prices. |
| 4 | **Booking & Checkout Flow** | The core of the platform. Streamlining the booking and payment process is crucial for maximizing conversions. | Create a multi-step Livewire component: 1. Select package, date, and quantity. 2. Enter customer information. 3. Process payment via the payment gateway. |
| 5 | **Customer Dashboard** | Enable user "self-service" to manage their bookings, which increases trust and satisfaction. | Create a protected page for registered users to view a list of past and upcoming bookings, check their status, and have the ability to view details or cancel. |
