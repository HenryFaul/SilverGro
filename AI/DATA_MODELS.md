# SilverGro - Data Model Reference

## Core Business Models

### TransportTransaction
**File:** `app/Models/TransportTransaction.php`
**Table:** `transport_transactions`
**Traits:** HasFactory, SoftDeletes, LogsActivity

The central entity linking all business operations.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint (starts 13000) | Primary key |
| old_id | string | Legacy system reference |
| old_deal_ticket | string | Legacy deal ticket reference |
| a_mq | string | Secondary reference identifier |
| sl_id | int | Split load ID |
| sl_global_id | int | Split load global ID |
| contract_type_id | FK | Contract type |
| contract_no | string | Contract number |
| supplier_id | FK | Supplier |
| customer_id | FK | Primary customer |
| customer_id_2 through _5 | FK | Additional customers |
| transporter_id | FK | Transporter |
| product_id | FK | Product |
| transport_date_earliest | date | Earliest transport date |
| transport_date_latest | date | Latest transport date |
| include_in_calculations | boolean | Include in financial aggregations |
| is_transaction_done | boolean | Transaction completed |
| is_split_load | boolean | Is a split load |
| is_split_load_primary | boolean | Primary in split load |
| is_split_load_member | boolean | Member of split load |
| *_notes | text | Various notes fields (10+) |

**Relationships:**
- belongsTo: ContractType, Supplier, Customer (x5), Transporter, Product
- hasOne: TransportJob, TransportLoad, TransportFinance, DealTicket, SalesOrder, PurchaseOrder, TransportOrder, TransportInvoice, TransportInvoiceDetails
- hasMany: TransportDriverVehicle, TransportStatus, AssignedUserComm, TransLink, TransLinkSplit

**Scopes:** `filter`, `index`, `week`, `month`

---

### TransportFinance
**File:** `app/Models/TransportFinance.php`
**Table:** `transport_finances`
**Traits:** HasFactory, SoftDeletes, LogsActivity

All financial calculations for a transaction.

| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Parent transaction |
| transport_load_id | FK | Related load |
| transport_rate_basis_id | FK | Rate calculation basis |
| cost_price_per_unit | decimal | Cost per unit |
| cost_price_per_ton | decimal | Calculated cost per ton |
| cost_price | decimal | Total cost price |
| selling_price / _2 / _3 / _4 / _5 | decimal | Selling price (per customer) |
| selling_price_per_ton | decimal | Selling price per ton |
| selling_price_per_unit | decimal | Selling price per unit |
| transport_rate_per_ton | decimal | Transport rate per ton |
| transport_rate | decimal | Base transport rate |
| transport_price | decimal | Transport price |
| load_insurance_per_ton | decimal | Insurance (selling_price_per_ton * 1.1) |
| comms_due_per_ton | decimal | Commission due per ton |
| weight_ton_incoming | decimal | Planned incoming weight |
| weight_ton_outgoing | decimal | Planned outgoing weight |
| is_transport_costs_inc_price | boolean | Transport costs included in price |
| transport_cost / _2 / _3 / _4 / _5 | decimal | Transport costs (per customer) |
| total_cost_price | decimal | Calculated total cost |
| additional_cost_1 / _2 / _3 | decimal | Additional costs |
| additional_cost_desc_1 / _2 / _3 | string | Additional cost descriptions |
| gross_profit | decimal | Calculated GP |
| gross_profit_percent | decimal | GP percentage |
| gross_profit_per_ton | decimal | GP per ton |
| total_supplier_comm | decimal | Total supplier commission |
| total_customer_comm | decimal | Total customer commission |
| total_comm | decimal | Total commission |
| adjusted_gp | decimal | GP adjustment amount |
| adjusted_gp_notes | text | GP adjustment notes |
| *_actual | decimal | Actual variants of above fields |

**Key Method:** `calculateFields()` - Performs all financial calculations

---

### TransportJob
**File:** `app/Models/TransportJob.php`
**Table:** `transport_jobs`

Job details and logistics configuration.

| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Parent transaction |
| transport_rate_basis_id | FK | Rate basis |
| customer_order_numbers_1-5 | string | Customer order refs |
| supplier_loading_numbers_1-5 | string | Supplier loading refs |
| is_multi_loads | boolean | Multiple loads flag |
| is_approved | boolean | Job approved |
| transport_date_earliest/latest | date | Date range |
| is_transport_costs_inc_price | boolean | Transport in price flag |
| loading_hour_option_id | FK | Loading time option |
| offloading_hour_option_id | FK | Offloading time option |
| load_insurance | decimal | Insurance amount |
| loading_instructions | text | Loading instructions |

---

### TransportLoad
**File:** `app/Models/TransportLoad.php`
**Table:** `transport_loads`

Product quantities and delivery details.

| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Parent transaction |
| product_source_id | FK | Product source |
| packaging_incoming_id | FK | Incoming packaging type |
| packaging_outgoing_id | FK | Outgoing packaging type |
| no_units_incoming | decimal | Planned incoming units |
| no_units_outgoing / _2 / _3 / _4 / _5 | decimal | Planned outgoing units |
| no_units_outgoing_total | decimal | Calculated total outgoing |
| billing_units_incoming_id | FK | Incoming billing unit |
| billing_units_outgoing_id | FK | Outgoing billing unit |
| weighbridge | boolean | Weighbridge required |
| delivery_note | text | Delivery notes |
| route_distance | decimal | Route distance |
| collection_address_1-5 | FK | Collection addresses |
| delivery_address_1-5 | FK | Delivery addresses |

---

### TransportDriverVehicle
**File:** `app/Models/TransportDriverVehicle.php`
**Table:** `transport_driver_vehicles`

Driver and vehicle assignment with status tracking.

| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Parent transaction |
| transport_job_id | FK | Related job |
| regular_driver_id | FK | Assigned driver |
| regular_vehicle_id | FK | Assigned vehicle |
| weighbridge_upload_weight | decimal | Actual weight at loading |
| weighbridge_offload_weight | decimal | Actual weight at delivery |
| weighbridge_variance | boolean | Weight variance flag |
| is_cancelled / cancellation_date | boolean/date | Cancellation status |
| is_loading / loading_date | boolean/date | Loading status |
| is_onroad / onroad_date | boolean/date | On-road status |
| is_delivered / delivered_date | boolean/date | Delivery status |
| is_scheduled / scheduled_date | boolean/date | Schedule status |
| is_paid / paid_date | boolean/date | Payment status |
| is_overdue | boolean | Overdue flag |
| notes | text | Notes |
| trailer_reg_1 / _2 | string | Trailer registration numbers |

---

### DealTicket
**File:** `app/Models/DealTicket.php`
**Table:** `deal_tickets`
**Implements:** HasMedia

| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Parent transaction |
| old_id | string | Legacy reference |
| trade_value | decimal | Current trade value |
| type | string | Deal ticket type |
| comment | text | Comments |
| is_active | boolean | Active status |
| is_approved | boolean | Approval status |
| is_printed | boolean | Print status |
| stamp_printed | timestamp | Print timestamp |
| report_path | string | Generated report path |

**Key Methods:** `calculateRules()`, `getAppliedRules()`

---

### Customer
**File:** `app/Models/Customer.php`
**Table:** `customers`

| Field | Type | Description |
|-------|------|-------------|
| first_name | string | First name |
| last_legal_name | string | Legal/last name |
| nickname | string | Nickname |
| title | string | Title |
| id_reg_no | string | ID/Registration number |
| is_active | boolean | Active status |
| customer_parent_id | FK | Parent customer |
| terms_of_payment_basis_id | FK | Payment terms basis |
| terms_of_payment_id | FK | Payment terms |
| invoice_basis_id | FK | Invoice basis |
| customer_rating_id | FK | Credit rating |
| is_vat_exempt | boolean | VAT exempt |
| is_vat_cert_received | boolean | VAT cert on file |
| credit_limit | decimal | Soft credit limit |
| credit_limit_hard | decimal | Hard credit limit |
| comment | text | Comments |
| days_overdue_allowed_id | FK | Max overdue days |

**Computed:** `trades_count` (active non-done transactions)

---

### CustomerParent
**File:** `app/Models/CustomerParent.php`
**Table:** `customer_parents`

Same fields as Customer (minus customer_parent_id). Parent in hierarchy.

---

### Supplier
**File:** `app/Models/Supplier.php`
**Table:** `suppliers`

| Field | Type | Description |
|-------|------|-------------|
| first_name, last_legal_name, nickname, title | string | Identity |
| job_description | string | Job description |
| id_reg_no | string | ID/Registration |
| is_active | boolean | Active status |
| terms_of_payment_id | FK | Payment terms |
| account_number | string | Account number |
| comment | text | Comments |

---

### Transporter
**File:** `app/Models/Transporter.php`
**Table:** `transporters`

Same structure as Supplier, plus `is_git` (boolean) flag.

---

### Product
**File:** `app/Models/Product.php`
**Table:** `products`

| Field | Type | Description |
|-------|------|-------------|
| old_id | string | Legacy reference |
| name | string | Product name |
| comment | text | Comments |
| is_active | boolean | Active status |

---

## Order Models

### SalesOrder / PurchaseOrder / TransportOrder
All three follow the same pattern:

| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Parent transaction |
| confirmed_by_type_id | FK | Confirmation method |
| confirmed_by_id | int | Confirmer ID |
| type | string | Order type |
| comment | text | Comments |
| is_active | boolean | Active status |
| is_printed | boolean | Print status |
| is_*_sent | boolean | Sent status |
| is_*_received | boolean | Received status |

---

## Approval Models

### TradeRule
| Field | Type | Description |
|-------|------|-------------|
| name | string | Rule name |
| min_trade_value | decimal | Minimum trade value |
| max_trade_value | decimal | Maximum trade value |
| is_active | boolean | Active status |

### TradeRuleOpp
Opportunity-based approval rules (COD, Suspended Customer).

### TradeRuleRole (Polymorphic)
Roles required for approval, linked to either TradeRule or TradeRuleOpp.

### TransportApproval
| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Transaction |
| deal_ticket_id | FK | Deal ticket |
| poly_approval_type/id | morph | Rule reference |
| transport_job_id | FK | Job reference |
| approved_by_id | FK | Approving user |
| role_name | string | Role that approved |
| is_approved | boolean | Approval status |

---

## Lookup/Reference Models

| Model | Table | Key Fields |
|-------|-------|------------|
| ContractType | contract_types | name, comment, is_active |
| BillingUnits | billing_units | name, kgs (conversion factor) |
| Packaging | packagings | name, comment, is_active |
| ProductSource | product_sources | name |
| TransportRateBasis | transport_rate_bases | name (Per Ton, Per Load, etc.) |
| VehicleType | vehicle_types | name |
| LoadingHourOption | loading_hour_options | name/value |
| ConfirmationTypes | confirmation_types | name |
| TermsOfPayment | terms_of_payments | value, comment, days |
| TermsOfPaymentBasis | terms_of_payment_bases | name |
| InvoiceBasis | invoice_bases | name |
| CustomerRating | customer_ratings | name/value |
| StatusType | status_types | name |
| StatusEntity | status_entities | name |
| InvoiceStatus | invoice_statuses | name |
| ContactType | contact_types | name |
| AddressType | address_types | name |
| TransLinkType | trans_link_types | name |

---

## Contact/Address Models (Polymorphic)

### Contact
| Field | Type | Description |
|-------|------|-------------|
| first_name, last_legal_name, nickname | string | Identity |
| title, job_description | string | Role |
| id_reg_no | string | ID/Registration |
| is_active | boolean | Active |
| branch, department | string | Organization |
| comment | text | Comments |
| poly_contact_type/id | morph | Owner entity |

### Address
| Field | Type | Description |
|-------|------|-------------|
| line_1, line_2, line_3 | string | Address lines |
| country, code | string | Country/postal |
| is_primary | boolean | Primary address |
| longitude, latitude | decimal | GPS coordinates |
| directions | text | Directions |
| address_type_id | FK | Address type |
| poly_address_type/id | morph | Owner entity |

### EmailContactDetail / NumberContactDetail
Polymorphic contact details for email addresses and phone numbers.

---

## Staff & Commission Models

### Staff
| Field | Type | Description |
|-------|------|-------------|
| first_name, last_legal_name | string | Name |
| user_id | FK | Linked user account |
| nickname, title, job_description | string | Details |
| id_reg_no | string | ID/Registration |
| is_active | boolean | Active |

### AssignedUserComm
| Field | Type | Description |
|-------|------|-------------|
| transport_trans_id | FK | Transaction |
| transport_finance_id | FK | Finance record |
| assigned_user_supplier_id | FK -> Staff | Supplier-side user |
| assigned_user_customer_id | FK -> Staff | Customer-side user |
| supplier_comm | decimal | Supplier commission amount |
| customer_comm | decimal | Customer commission amount |
| notes | text | Notes |

---

## Reporting Models

| Model | Purpose |
|-------|---------|
| DebtorStanding | Customer outstanding balance calculations |
| CustomReport | Custom report definitions |
| CustomReportModel | Report model/attribute configurations |
| TransactionSummary | Transaction summary/aggregation |
| TransactionSpreadSheet | Spreadsheet export data |
| PcSummary | Purchase contract summary |
| ContractSummary | Contract summary |
| OldTransaction | Legacy transaction data |
| DocumentStore | Document file storage |
