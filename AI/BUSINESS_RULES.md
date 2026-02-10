# SilverGro - Business Rules & Domain Logic

## 1. Transport Transaction Lifecycle

A Transport Transaction is the central business entity. It represents a trade deal for moving product from supplier to customer via a transporter.

### 1.1 Transaction Creation
- A transaction links: **Supplier** (1), **Customer(s)** (up to 5), **Transporter** (1), **Product** (1)
- Each transaction is assigned a **Contract Type** (e.g., Purchase Contract, Sales Contract)
- Transaction IDs start at 13000 (configured in migration)
- The `a_mq` field is a secondary reference identifier
- A transaction can reference an `old_id` and `old_deal_ticket` for legacy data migration
- `include_in_calculations` flag controls whether the transaction is counted in financial aggregations
- `is_transaction_done` marks completion

### 1.2 Split Load Handling
- Transactions support split loads via `is_split_load`, `is_split_load_primary`, `is_split_load_member`
- A split load links one primary transaction to member transactions
- `sl_id` and `sl_global_id` track split load grouping
- Split loads calculate outgoing units as: `no_units_outgoing_2 + _3 + _4 + _5`

### 1.3 Multi-Customer Transactions
- A single transaction can have up to 5 customers (`customer_id` through `customer_id_5`)
- Each customer can have separate selling prices (`selling_price` through `selling_price_5`)
- Each customer can have separate transport costs (`transport_cost` through `transport_cost_5`)

### 1.4 Notes System
The transaction has extensive notes fields for different stakeholders:
- `suppliers_notes`, `traders_notes`, `transport_notes`, `pricing_notes`
- `process_notes`, `document_notes`, `transaction_notes`
- `traders_notes_supplier`, `traders_notes_customer`, `traders_notes_transport`
- `delivery_notes`, `product_notes`, `customer_notes`

## 2. Financial Calculations

Financial calculations are performed by `TransportFinance::calculateFields()`. This is the core financial engine.

### 2.1 Price Calculations
```
selling_price = no_units_outgoing * selling_price_per_unit
cost_price = no_units_incoming * cost_price_per_unit
```

### 2.2 Weight Calculations
```
weight_ton_incoming = no_units_incoming * (billing_units_incoming.kgs / 1000)
weight_ton_outgoing = no_units_outgoing_total * (billing_units_outgoing.kgs / 1000)
```

### 2.3 Per-Ton Calculations
```
cost_price_per_ton = cost_price / weight_ton_incoming
selling_price_per_ton = selling_price / weight_ton_outgoing
load_insurance_per_ton = selling_price_per_ton * 1.1
```

### 2.4 Transport Cost Calculation
Transport cost depends on the **Transport Rate Basis**:
- **Per Load (ID=3):** `transport_cost = transport_rate` (flat rate)
- **Per Ton (incoming weight > 0):** `transport_cost = transport_rate * weight_ton_incoming`
- **Per Ton (fallback):** `transport_cost = transport_rate * weight_ton_outgoing`

### 2.5 Total Cost Price
Depends on `is_transport_costs_inc_price` flag on TransportJob:
- **If transport costs included in price:**
  `total_cost_price = cost_price + additional_cost_1 + additional_cost_2 + additional_cost_3`
- **If transport costs NOT included:**
  `total_cost_price = cost_price + additional_cost_1 + additional_cost_2 + additional_cost_3 + transport_cost`

### 2.6 Gross Profit
```
gross_profit = selling_price - (total_cost_price + adjusted_gp)
gross_profit_percent = (gross_profit / selling_price) * 100
gross_profit_per_ton = gross_profit / weight_ton_outgoing
```

### 2.7 Actual vs Planned
The system maintains dual sets of calculations:
- **Planned:** Based on `no_units_incoming`/`no_units_outgoing` from TransportLoad
- **Actual:** Based on `weighbridge_upload_weight`/`weighbridge_offload_weight` from TransportDriverVehicle records

Actual values are aggregated from ALL driver/vehicle assignments on the transaction.

### 2.8 Commission Calculation
```
total_supplier_comm = gross_profit / 2
total_customer_comm = gross_profit / 2
```
Commissions are split evenly among all assigned commission users (AssignedUserComm records):
```
per_user_supplier_comm = total_supplier_comm / number_of_comm_users
per_user_customer_comm = total_customer_comm / number_of_comm_users
```

## 3. Approval Workflow (Deal Tickets)

### 3.1 Trade Rules Engine
Deal tickets go through an approval process based on configurable trade rules:

1. **Value-Based Rules (TradeRule):** Rules with `min_trade_value` and `max_trade_value` ranges
   - The system finds the rule where `selling_price` falls within the range
   - If no matching rule found, falls back to TradeRule ID 1 (default)
   - Each rule has associated roles (TradeRuleRole) that must approve

2. **Opportunity-Based Rules (TradeRuleOpp):** Additional conditional rules:
   - **COD Rule (ID=1):** Triggered when supplier's `terms_of_payment_id = 1` (Cash on Delivery)
   - **Suspended Customer Rule (ID=2):** Triggered when customer's `is_active != 1`

### 3.2 Approval Process
- Each required role must have a corresponding `TransportApproval` record
- A deal ticket is `is_approved = true` only when ALL required approvals exist
- If any approval is missing, the deal ticket is set to `is_approved = false, is_active = false`
- The `trade_value` on the deal ticket is updated to the current `selling_price` on each check
- Approved deal tickets trigger a `DealTicketApproved` notification

### 3.3 Deal Ticket Documents
- Deal tickets implement `HasMedia` (Spatie Media Library) for file attachments
- PDF reports can be generated, viewed, and downloaded
- `report_path` and `report_path_old` track generated document locations

## 4. Order Management

### 4.1 Sales Order
- One per transaction, tracks customer-side order confirmation
- Workflow: Create -> Activate -> Send -> Receive
- Generates confirmation PDFs
- Supports split confirmation PDFs per client (`viewConfirmationPDFSplit`)

### 4.2 Purchase Order
- One per transaction, tracks supplier-side order confirmation
- Workflow: Create -> Activate -> Send -> Receive
- Generates confirmation PDFs

### 4.3 Transport Order
- One per transaction, tracks transporter-side order confirmation
- Workflow: Create -> Activate -> Send -> Receive
- Generates confirmation PDFs

### 4.4 Confirmation Tracking
Each order type tracks:
- `confirmed_by_type_id` - Method of confirmation
- `confirmed_by_id` - Who confirmed
- `is_active`, `is_printed` - Status flags
- Send/receive confirmation flags (e.g., `is_sa_conf_sent`, `is_sa_conf_received`)

## 5. Customer Management

### 5.1 Customer Hierarchy
- **CustomerParent** -> has many -> **Customer** (parent-child relationship)
- Both levels share the same financial fields (terms, rating, VAT, credit limits)
- Customers inherit settings from their parent unless overridden

### 5.2 Credit Management
- `credit_limit` - Soft credit limit
- `credit_limit_hard` - Hard credit limit (cannot be exceeded)
- `days_overdue_allowed_id` - Maximum days overdue allowed
- `customer_rating_id` - Customer creditworthiness rating

### 5.3 VAT Handling
- `is_vat_exempt` - Whether customer is VAT exempt
- `is_vat_cert_received` - Whether VAT exemption certificate is on file

### 5.4 Active Trade Count
- Customer model has a computed `trades_count` attribute
- Counts active transactions where `is_transaction_done = false` AND `include_in_calculations = true`

## 6. Debtor Standing
- Tracks outstanding balances per customer
- Calculates overdue amounts
- Accessible via dedicated Debtor Standing page
- Can be filtered by customer name, active status, and has-balance flag

## 7. Contact System (Polymorphic)
- Contacts are polymorphic (`poly_contact`) - can belong to Customer, Supplier, Transporter, CustomerParent
- Addresses are polymorphic (`poly_address`) - same entities
- Staff assignments are polymorphic (`staff_assigned`)
- Contact details split into EmailContactDetail and NumberContactDetail (also polymorphic)

## 8. Driver/Vehicle Management

### 8.1 Assignment
- Multiple drivers/vehicles can be assigned to a transaction
- Each assignment tracks: weighbridge weights, status flags, schedule dates, notes, trailer info

### 8.2 Status Tracking
Driver/vehicle assignments track progression:
- Cancellation status + date
- Loading status + date
- On-road status + date
- Delivery status + date
- Schedule status + date
- Payment status + date
- Overdue flag

### 8.3 Weighbridge
- `weighbridge_upload_weight` - Weight at loading
- `weighbridge_offload_weight` - Weight at delivery
- `weighbridge_variance` - Weight difference flag
- These actual weights feed into the financial "actual" calculations

## 9. Transport Status
- Transactions can have multiple status entries
- Each status links to a `StatusEntity` and `StatusType`
- Status entries are tracked with timestamps and soft deletes

## 10. Transport Linking

### 10.1 Contract Links (TransLink)
- Links transport transactions together (e.g., Purchase Contract to Sales Contract)
- `trans_link_type_id` defines the relationship type
- Used in PC Overview and SC Overview pages

### 10.2 Split Links (TransLinkSplit)
- Manages split load relationships between transactions
- Separate store and delete operations from regular links

## 11. Data Import
- Bulk import from Excel for: Customers, Suppliers, Transporters, Products, Old Transactions
- Uses `Maatwebsite\Excel` with `ToCollection` and `WithHeadingRow` interfaces
- Imports process entire rows including contacts, addresses, and financial settings

## 12. Reporting
- **Dashboard:** Monthly KPIs (planned tons, actual weights, costs, selling prices, GP, GP%)
- **Custom Reports:** Configurable report definitions with model/attribute selection
- **Excel Export:** Transaction data export with PhpSpreadsheet
- **PDF Reports:** Deal tickets, Sales Orders, Purchase Orders, Transport Orders

## 13. Activity Logging
- TransportTransaction and TransportFinance models use Spatie ActivityLog
- All field changes are logged for audit trail
- Critical financial fields are tracked for compliance

## 14. Permissions & Roles
- Uses Spatie Laravel Permission package
- Role-based access control with granular permissions
- Examples: `create_customer`, `update_customer`, `view-only`
- Permissions checked both server-side and client-side (via Inertia shared props)
- Role modification available in admin section
