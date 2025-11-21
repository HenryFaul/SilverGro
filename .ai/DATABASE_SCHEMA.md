# Database Schema Reference

## Core Tables Overview

This document provides a reference for the main database tables and their relationships in the SilverGro application.

## Customer Management

### `customers`

Customer records with credit management and relationship tracking.

**Key Columns:**

- `id` - Primary key
- `first_name` - Customer first name
- `last_legal_name` - Legal/last name
- `nickname` - Display name
- `customer_parent_id` - FK to customer_parents (for grouped customers)
- `title` - Customer title (Mr, Mrs, etc.)
- `id_reg_no` - ID or registration number
- `is_active` - Boolean for active status
- `terms_of_payment_id` - FK to terms_of_payments
- `terms_of_payment_basis_id` - FK to terms_of_payment_bases
- `invoice_basis_id` - FK to invoice_bases
- `customer_rating_id` - FK to customer_ratings (credit rating)
- `is_vat_exempt` - Boolean for VAT exemption
- `is_vat_cert_received` - Boolean for VAT certificate status
- `credit_limit` - Soft credit limit (decimal)
- `credit_limit_hard` - Hard credit limit (decimal)
- `comment` - Notes/comments
- `days_overdue_allowed_id` - FK to debtor_standings
- `created_at`, `updated_at`, `deleted_at` (soft deletes)

**Relationships:**

- BelongsTo: CustomerParent, TermsOfPayment, CustomerRating, DebtorStanding
- HasMany: TransportTransactions, Contacts, Addresses
- MorphMany: ContactDetails (email, phone)

### `customer_parents`

Grouping of related customers (e.g., parent company with subsidiaries).

**Key Columns:**

- `id` - Primary key
- `name` - Parent company name
- Additional grouping fields

**Relationships:**

- HasMany: Customers

### `customer_ratings`

Credit rating system for customers.

**Key Columns:**

- `id` - Primary key
- `name` - Rating name/description
- `value` - Numeric rating value

### `debtor_standings`

Days overdue allowance configurations.

**Key Columns:**

- `id` - Primary key
- `days` - Number of days allowed
- `description` - Description of standing

## Supplier Management

### `suppliers`

Supplier information and management.

**Key Columns:**

- `id` - Primary key
- `name` - Supplier name
- `registration_number` - Business registration
- `is_active` - Boolean for active status
- Contact and address relationships similar to customers

**Relationships:**

- HasMany: PurchaseOrders, TransportTransactions
- MorphMany: ContactDetails, Addresses

## Transport Management

### `transport_transactions`

Core transport transaction records.

**Key Columns:**

- `id` - Primary key
- `customer_id` - FK to customers
- `supplier_id` - FK to suppliers
- `product_id` - FK to products
- `contract_type_id` - FK to contract_types (1=Direct, 2=PC, 3=SC, 4=MQ)
- `transport_date_earliest` - Earliest transport date
- `transport_date_latest` - Latest transport date
- `transport_date_actual` - Actual transport date
- `is_transaction_done` - Boolean for completion status
- `include_in_calculations` - Boolean for financial inclusion
- `delivery_notes` - Delivery instructions
- `loading_hours_from`, `loading_hours_to` - Loading time window
- `tonnes` - Quantity in tonnes
- `price_per_ton` - Price per tonne
- `total_price` - Calculated total
- `a_mq` - Market Quote approval number (bigInteger, nullable) - Sequential number assigned when MQ contract approved
- `a_pc` - Purchase Confirmation approval number (bigInteger, nullable) - Sequential number assigned when PC contract
  approved
- `a_sc` - Sales Confirmation approval number (bigInteger, nullable) - Sequential number assigned when SC contract
  approved
- `old_id` - Legacy system transaction ID for reference
- Various status and tracking fields

**Approval Number System:**
The approval numbers (a_mq, a_pc, a_sc) are automatically assigned sequential numbers based on contract type:

- **MQ Contracts (contract_type_id = 4)**: Get `a_mq` number via TransportApprovalController
- **PC Contracts (contract_type_id = 2)**: Get `a_pc` number via PcScApprovalController
- **SC Contracts (contract_type_id = 3)**: Get `a_sc` number via PcScApprovalController
- Each approval type has its own independent sequential numbering (e.g., MQ:123, PC:45, SC:67)
- These numbers are used for quick identification and searching across the system
- Display format: `(MQ:123)`, `(PC:45)`, `(SC:67)` in tables and views

**Relationships:**

- BelongsTo: Customer, Supplier, Product, Transporter, ContractType
- HasMany: TransportOrders, DealTickets, TransLinks
- HasOne: TransportApproval, TransportFinance

### `transport_orders`

Transport job orders.

**Key Columns:**

- `id` - Primary key
- `transport_transaction_id` - FK to transport_transactions
- `transporter_id` - FK to transporters
- `driver_id` - FK to regular_drivers (nullable)
- `vehicle_id` - FK to regular_vehicles (nullable)
- `vehicle_type_id` - FK to vehicle_types
- `transport_status_id` - FK to transport_statuses
- `transport_date_earliest`, `transport_date_latest`, `transport_date_actual`
- `no_of_loads` - Number of loads required
- `tonnage_per_load` - Tonnes per load
- `rate_per_ton` - Transport rate
- `total_cost` - Calculated cost

**Relationships:**

- BelongsTo: TransportTransaction, Transporter, RegularDriver, RegularVehicle
- HasMany: TransportLoads, TransportInvoices

### `transport_loads`

Individual load tracking within transport orders.

**Key Columns:**

- `id` - Primary key
- `transport_order_id` - FK to transport_orders
- `load_number` - Load sequence number
- `driver_id` - Assigned driver
- `vehicle_id` - Assigned vehicle
- `load_date` - Date of load
- `tonnage` - Weight transported
- `status` - Load status
- `notes` - Load notes

### `transport_invoices`

Transport invoicing.

**Key Columns:**

- `id` - Primary key
- `transport_order_id` - FK to transport_orders
- `invoice_number` - Invoice reference
- `invoice_date` - Date issued
- `amount` - Invoice amount
- `status_id` - FK to invoice_statuses
- `paid_date` - Payment date

### `contract_types`

Defines the types of contracts available in the system.

**Key Columns:**

- `id` - Primary key
- `name` - Contract type name

**Standard Values:**

- 1: Direct Sale (no approval workflow)
- 2: PC (Purchase Confirmation) - Requires PC approval
- 3: SC (Sales Confirmation) - Requires SC approval
- 4: MQ (Market Quote) - Requires full MQ approval workflow with trade rules

**Relationships:**

- HasMany: TransportTransactions

### `transport_approvals`

Approval workflow for MQ transport transactions (contract_type_id = 4).

**Key Columns:**

- `id` - Primary key
- `transport_transaction_id` - FK to transport_transactions
- `transport_job_id` - FK to transport_jobs
- `deal_ticket_id` - FK to deal_tickets
- `poly_approval_id` - Polymorphic ID for approval rules
- `poly_approval_type` - Polymorphic type (TradeRule or TradeRuleOpp)
- `role_name` - User role that approved
- `approved_by_id` - FK to users
- `is_approved` - Boolean for approval status
- `created_at`, `updated_at` - Timestamps

**Note:** This table is specifically for MQ contracts. PC and SC contracts use a simpler approval process that directly
updates the `a_pc` or `a_sc` field on the transport_transactions table.

### `transport_finances`

Financial tracking for transport.

**Key Columns:**

- `id` - Primary key
- `transport_transaction_id` - FK to transport_transactions
- `cost` - Total cost
- `revenue` - Total revenue
- `margin` - Calculated margin
- `commission` - Commission amount

### `transporters`

Transport service providers.

**Key Columns:**

- `id` - Primary key
- `name` - Transporter name
- `registration_number` - Business registration
- `is_active` - Boolean
- Contact details

**Relationships:**

- HasMany: TransportOrders, RegularDrivers, RegularVehicles

### `regular_drivers`

Regular/contracted drivers.

**Key Columns:**

- `id` - Primary key
- `transporter_id` - FK to transporters
- `first_name`, `last_name` - Driver name
- `id_number` - ID number
- `license_number` - Driver license
- `phone` - Contact number
- `is_active` - Boolean

### `regular_vehicles`

Regular/contracted vehicles.

**Key Columns:**

- `id` - Primary key
- `transporter_id` - FK to transporters
- `vehicle_type_id` - FK to vehicle_types
- `registration` - Vehicle registration
- `make`, `model` - Vehicle details
- `capacity` - Load capacity
- `is_active` - Boolean

### `vehicle_types`

Vehicle type classifications.

**Key Columns:**

- `id` - Primary key
- `name` - Type name (e.g., "18 Ton", "34 Ton")
- `capacity` - Standard capacity
- `description` - Type description

## Product Management

### `products`

Product catalog.

**Key Columns:**

- `id` - Primary key
- `name` - Product name
- `product_source_id` - FK to product_sources
- `packaging_id` - FK to packagings
- `is_active` - Boolean
- `code` - Product code
- `description` - Product description

**Relationships:**

- BelongsTo: ProductSource, Packaging
- HasMany: TransportTransactions, SalesOrders, PurchaseOrders

### `product_sources`

Product origin/source classifications.

**Key Columns:**

- `id` - Primary key
- `name` - Source name
- `description` - Source description

### `packagings`

Packaging type definitions.

**Key Columns:**

- `id` - Primary key
- `name` - Packaging name (e.g., "Bulk", "Bag 50kg")
- `unit_weight` - Standard weight
- `description` - Description

## Transaction & Contract Management

### `transaction_summaries`

Summary/aggregate transaction data.

**Key Columns:**

- `id` - Primary key
- Transaction summary fields
- Calculated totals and aggregates

### `sales_orders`

Customer sales orders.

**Key Columns:**

- `id` - Primary key
- `customer_id` - FK to customers
- `product_id` - FK to products
- `order_number` - Order reference
- `order_date` - Date placed
- `quantity` - Ordered quantity
- `price_per_unit` - Unit price
- `total_amount` - Total value
- `status_id` - FK to status_types

### `purchase_orders`

Supplier purchase orders.

**Key Columns:**

- `id` - Primary key
- `supplier_id` - FK to suppliers
- `product_id` - FK to products
- `order_number` - Order reference
- `order_date` - Date placed
- `quantity` - Ordered quantity
- `price_per_unit` - Unit price
- `total_amount` - Total value
- `status_id` - FK to status_types

### `contract_summaries`

Contract tracking and management.

**Key Columns:**

- `id` - Primary key
- `contract_type_id` - FK to contract_types
- `contract_number` - Contract reference
- `customer_id` - FK to customers (nullable)
- `supplier_id` - FK to suppliers (nullable)
- `start_date`, `end_date` - Contract period
- `total_value` - Contract value
- `status` - Contract status

### `pc_summaries`

PC (Purchase Contract) summaries.

**Key Columns:**

- `id` - Primary key
- PC-specific summary fields
- Contract references

### `deal_tickets`

Deal documentation and tickets.

**Key Columns:**

- `id` - Primary key
- `transport_transaction_id` - FK to transport_transactions
- `ticket_number` - Ticket reference
- `issue_date` - Date issued
- `weight` - Ticket weight
- `notes` - Ticket notes
- `status` - Ticket status

### `trans_links`

Links between related transactions.

**Key Columns:**

- `id` - Primary key
- `trans_link_type_id` - FK to trans_link_types
- `source_transaction_id` - Source transaction
- `target_transaction_id` - Target transaction
- `link_date` - Date linked
- `notes` - Link notes

### `trans_link_splits`

Split allocations for linked transactions.

**Key Columns:**

- `id` - Primary key
- `trans_link_id` - FK to trans_links
- `percentage` - Split percentage
- `amount` - Split amount

## Staff & User Management

### `users`

System user accounts (Jetstream).

**Key Columns:**

- `id` - Primary key
- `name` - User name
- `email` - Email (unique)
- `email_verified_at` - Verification timestamp
- `password` - Hashed password
- `two_factor_secret`, `two_factor_recovery_codes` - 2FA
- `profile_photo_path` - Profile image
- `current_team_id` - Team association
- `remember_token` - Session token

**Relationships:**

- BelongsToMany: Roles, Permissions (Spatie)
- HasMany: Various activity logs

### `staff`

Internal staff member records.

**Key Columns:**

- `id` - Primary key
- `user_id` - FK to users (nullable)
- `first_name`, `last_name` - Staff name
- `email` - Email address
- `phone` - Contact number
- `position` - Job position
- `is_active` - Boolean
- `commission_rate` - Commission percentage

**Relationships:**

- BelongsTo: User
- HasMany: AssignedUserComm

### `assigned_user_comms`

Commission assignments to staff for transactions.

**Key Columns:**

- `id` - Primary key
- `staff_id` - FK to staff
- `transport_transaction_id` - FK to transport_transactions
- `commission_percentage` - Commission %
- `commission_amount` - Calculated commission
- `notes` - Assignment notes

## Supporting Tables

### `addresses`

Address records (polymorphic).

**Key Columns:**

- `id` - Primary key
- `addressable_type`, `addressable_id` - Polymorphic relation
- `address_type_id` - FK to address_types
- `line_1`, `line_2`, `line_3` - Address lines
- `city`, `province`, `postal_code`, `country`
- `is_primary` - Primary address flag

### `contacts`

Contact person records.

**Key Columns:**

- `id` - Primary key
- `contactable_type`, `contactable_id` - Polymorphic
- `contact_type_id` - FK to contact_types
- `first_name`, `last_name` - Contact name
- `position` - Job position
- `is_primary` - Primary contact flag

### `email_contact_details` / `number_contact_details`

Contact detail records (polymorphic).

**Key Columns:**

- `id` - Primary key
- `emailable_type`, `emailable_id` / `callable_type`, `callable_id`
- `email` / `number` - Contact value
- `is_primary` - Primary flag
- `label` - Description (work, mobile, etc.)

### `terms_of_payments`

Payment term definitions.

**Key Columns:**

- `id` - Primary key
- `name` - Term name (e.g., "Net 30")
- `days` - Number of days
- `terms_of_payment_basis_id` - FK to bases
- `description` - Term description

### `trade_rules`

Trading rule definitions.

**Key Columns:**

- `id` - Primary key
- `name` - Rule name
- `trade_rule_role_id` - FK to trade_rule_roles
- `conditions` - Rule conditions (JSON)
- `is_active` - Boolean

### `status_types` / `status_entities`

Status tracking system.

**Key Columns:**

- `id` - Primary key
- `name` - Status name
- `entity_type` - Entity being tracked
- `order` - Display order
- `color` - UI color code

### `document_stores`

Document storage and management.

**Key Columns:**

- `id` - Primary key
- `documentable_type`, `documentable_id` - Polymorphic
- `file_path` - Storage path
- `file_name` - Original filename
- `file_type` - MIME type
- `file_size` - File size in bytes
- `uploaded_by` - FK to users

### `custom_reports` / `custom_report_models`

Custom report definitions.

**Key Columns:**

- `id` - Primary key
- `name` - Report name
- `description` - Report description
- `model_class` - Associated model
- `query_parameters` - Query config (JSON)
- `columns` - Selected columns (JSON)

## Lookup Tables

Common lookup/reference tables:

- `address_types` - Address type classifications
- `contact_types` - Contact type classifications
- `billing_units` - Billing unit types
- `confirmation_types` - Confirmation classifications
- `contract_types` - Contract classifications
- `invoice_bases` - Invoice basis options
- `invoice_statuses` - Invoice status options
- `loading_hour_options` - Loading hour presets
- `transport_rate_bases` - Rate calculation bases
- `transport_statuses` - Transport status options
- `trans_link_types` - Transaction link types
- `trade_rule_roles` - Trade rule role types
- `trade_rule_opps` - Trade rule opportunities

## Activity Logging

### `activity_log`

Audit trail (Spatie Activity Log).

**Key Columns:**

- `id` - Primary key
- `log_name` - Log category
- `description` - Action description
- `subject_type`, `subject_id` - Subject entity
- `causer_type`, `causer_id` - User who caused action
- `properties` - Action details (JSON)
- `batch_uuid` - Batch identifier
- `created_at` - Timestamp

## Notifications

### `notifications`

System notifications.

**Key Columns:**

- `id` - UUID primary key
- `type` - Notification type class
- `notifiable_type`, `notifiable_id` - Recipient
- `data` - Notification data (JSON)
- `read_at` - Read timestamp
- `created_at`, `updated_at`

## Indexes & Performance

**Common Indexes:**

- Foreign keys should be indexed
- `is_active` flags commonly indexed
- Date fields for range queries
- Email fields for lookups
- Status fields for filtering
- Polymorphic type+id pairs

**Soft Deletes:**

- Customers, Suppliers, Staff, and most core entities use soft deletes
- Remember to include `->withTrashed()` or `->onlyTrashed()` when needed

## Relationships Summary

**One-to-Many (Most Common):**

- Customer → TransportTransactions
- Supplier → PurchaseOrders
- TransportTransaction → TransportOrders
- TransportOrder → TransportLoads
- Transporter → RegularDrivers, RegularVehicles

**Many-to-Many:**

- Users ↔ Roles (via Spatie Permission)
- Users ↔ Permissions (via Spatie Permission)

**Polymorphic:**

- Addresses (customers, suppliers, transporters)
- ContactDetails (customers, suppliers, contacts, staff)
- DocumentStore (various entities)
- Media (Spatie Media Library)

**One-to-One:**

- TransportTransaction ↔ TransportApproval
- TransportTransaction ↔ TransportFinance

## Notes

- All monetary values stored as decimal/float
- Dates stored in database timezone (configure in Laravel)
- Most tables have timestamps (created_at, updated_at)
- Activity logging enabled on key models
- Foreign key constraints enforced where applicable
- Use migrations to understand exact schema (check `database/migrations/`)

