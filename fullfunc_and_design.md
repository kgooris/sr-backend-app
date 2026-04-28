# Full Functionality and Design Specification for sr-backend-app

This document details the complete functional requirements, architectural decisions, data models, and user experience (UX) rules derived from the existing codebase. It serves as the definitive guide for rebuilding the application while preserving all existing functionality and relationships.

## 1. Architectural Foundation
*   **Framework:** The application must be built on a Symfony-like architecture, leveraging Dependency Injection for service management.
*   **Data Layer:** Must use Doctrine ORM for relational database management and entity persistence.
*   **Bundles:** The system must be organized into distinct bundles: `SMS`, `AppBundle`, and `FrontBundle`.

## 2. Data Model Requirements (Database Schema)
The following core entities must be defined in the database structure, with relationships explicitly defined:
*   **Users**: Manages user accounts (`User`, `UserOld`) and profile management.
*   **Products/Items**: Must manage core item types: `DrankEenheid` (Unit), `DrankSoort` (Type), `DrankStand` (Stock), and `FestivalDag` (Event Date).
*   **Grouping**: A structure to group items (`Group`) must exist, linking products to specific collections.
*   **Order Management**: Must support tracking of orders: `Order`, `OrderDrank`, and related stock information.
*   **Notification System**: Entities for asynchronous communication flow: `Inbox` and `Outbox`.
*   **SMS Tracking**: Specific entities to track SMS-related data (`Phones`, `Sentitems`, `Multipartinbox`, `OutboxMultipart`).

## 3. Backend Functionality (API & Business Logic)
The backend must implement the following core functionalities via controllers and services:
*   **User Management Endpoints:** Full CRUD operations for users, including registration and profile management.
*   **Order Processing Endpoints:** API endpoints to handle the creation, viewing, editing, and status updates for all order types (e.g., `DrankBestelling`, `MainStock`).
*   **Group Management Endpoints:** Functionality to manage item groupings and their associated details.
*   **Product/Item Management Endpoints:** CRUD operations for managing product details (`DrankEenheid`, `DrankSoort`, etc.).
*   **Notification Flow Logic:** Logic must exist to handle the creation, storage, and retrieval of messages in the Inbox and Outbox.
*   **Role Hierarchy:** A mechanism (`RoleHierarchyHelper`) must be implemented to define and enforce user roles and their hierarchies.

## 4. Notification System (SMS Bundle) Requirements
The SMS system requires dedicated management:
*   **Inbox/Outbox Management:** Ability to manage message queues, including handling multipart messages (`Multipartinbox`, `OutboxMultipart`).
*   **Filtering:** Logic must exist to filter messages based on context (e.g., `InboxFilterType`, `OutboxFilterType`).
*   **Admin Interface:** An administrative interface for managing SMS entities is required, including views for Inboxes, Outboxes, and user/group management.

## 5. Presentation Layer (Frontend Views) Requirements
The frontend must provide a complete and functional user experience:
*   **Authentication Flow:** Full support for registration, email confirmation, password change, and login flows.
*   **Dashboard:** An administrative dashboard view (`dashboard.html.twig`) is required for oversight.
*   **User Profile Management:** Views for viewing and editing personal profile information.
*   **Order Interface:** Detailed views for managing orders, including listing, editing (for specific order types like `DrankBestelling` or `MainStock`), and order history/reports (`HoofdStockOrders`, `drankstandOrders`).
*   **Data Reporting:** Views must be present to display aggregated data based on product groupings and stock status.
*   **Internationalization (i18n):** The frontend must fully support multiple languages, requiring the use of localization files (`messages.fr.xlf`) and corresponding language-specific JavaScript/CSS files for dynamic text and date formatting.

## 6. Configuration & Dependencies Requirements
*   **Configuration:** All application settings, routing rules, and service definitions must be managed via configuration files (`config.yml`, `routing.yml`, `services.xml`).
*   **Dependencies:** The project must rely on the Symfony framework and Doctrine ORM for structure.