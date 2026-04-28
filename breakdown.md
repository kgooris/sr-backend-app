# System Functionality Breakdown for sr-backend-app

This document summarizes the core functionality, database structure, and system relationships discovered in the `sr-backend-app` codebase.

## 1. Core Domain & Data Models (Entities)
The system is built around several interconnected entities managed by Doctrine ORM, suggesting a relational database structure:

*   **Users**: Manages user accounts (`User`, `UserOld`) and profile management.
*   **Products/Items**: Entities like `DrankEenheid`, `DrankSoort`, `DrankStand`, and `FestivalDag` manage the core product catalog or item details.
*   **Grouping & Orders**: `Group` entities organize items, and `Order` entities (`Order`, `OrderDrank`) track specific drink orders and their associated stock/details.
*   **Notifications**: Entities like `Inbox` and `Outbox` manage the asynchronous flow of messages or notifications.

## 2. Backend Logic (Controllers & Services)
The backend logic is managed through a Symfony-like structure:

*   **API Endpoints:** Numerous controllers handle CRUD operations for all major entities, including administrative functions (`AdminController`, `UserController`) and specific business operations related to orders, groups, and user profiles.
*   **Data Access:** Repositories (e.g., `OrderRepository`, `UserRepository`) abstract the database interactions.
*   **Business Logic:** Services (e.g., `RoleHierarchyHelper.php`) handle complex logic, such as role hierarchy management.

## 3. Notification System (SMS Bundle)
The SMS bundle (`src/SMS`) is responsible for managing communication flow:

*   It includes an `Inbox` and `Outbox` mechanism to store messages or notifications.
*   Data fixtures (`LoadInboxData.php`, `LoadOutboxData.php`) suggest processes exist to load this data, likely related to synchronization or processing.

## 4. Presentation Layer (Frontend Views)
The frontend is built using Twig templates and heavily relies on localization files:

*   **Structure:** Templates define views for administrative dashboards, user profiles, registration flows, order management (listing, editing, confirmation), and data reporting.
*   **Localization:** Extensive use of language files (`messages.fr.xlf`) and numerous JavaScript/CSS localization files indicates support for multiple languages (e.g., Dutch, English, French).

## 5. Configuration & Dependencies
The system is configured via YAML and XML files:

*   **Configuration Files**: `config.yml`, `routing.yml`, `services.xml` define application settings, routing rules, and service dependencies.
*   **Dependencies**: The use of Doctrine and Symfony components indicates a structured, dependency-injected architecture.

## Conclusion
The application functions as a comprehensive backend system for managing user data, product/item catalog, and order processing, with an added layer for asynchronous communication via SMS notifications. The structure is highly relational and organized into distinct bundles (User, SMS, Front).