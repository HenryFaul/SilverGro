# SilverGro - Routes Reference

All routes are defined in `routes/web.php` and protected by authentication middleware.

## Public Routes

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/` | - | Welcome/landing page |

## Dashboard & Core

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/dashboard` | DashboardController@index | Main dashboard with KPIs |
| GET | `/test` | TestController | Development test page |
| GET | `/no_permission` | - | 403 permission denied page |

## Transport Transactions (Core)

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/transport_transaction` | TransportTransactionController@index | List transactions (filtered/paginated) |
| POST | `/transport_transaction` | TransportTransactionController@store | Create new transaction |
| GET | `/transport_transaction/{id}` | TransportTransactionController@show | Transaction detail |
| PUT | `/transport_transaction/{id}` | TransportTransactionController@update | Update transaction |
| DELETE | `/transport_transaction/{id}` | TransportTransactionController@destroy | Delete transaction |
| PUT | `/transport_transaction/update_players/{id}` | TransportTransactionController@updatePlayers | Update supplier/customer/transporter |
| POST | `/transport_transaction/clone` | TransportTransactionController@clone | Clone a transaction |

### Transaction Props (AJAX)

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/props/trade_slide_over` | Props for trade creation slide-over |
| GET | `/props/contract_link_modal` | Props for PC contract linking |
| GET | `/props/contract_link_sc_modal` | Props for SC contract linking |
| GET | `/props/contract_link_split/primary` | Props for primary split linking |

### Transaction Reports

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/excel_report/trade/generate` | Generate Excel report |
| GET | `/excel_report/trade/generate/{file_name}` | Download Excel report |

## Transaction Views

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/transaction_summary` | TransactionSummaryController@index | Trade summary view |
| PUT | `/transaction_summary/{id}` | TransactionSummaryController@update | Update summary |
| GET | `/transaction_spreadsheet` | TransactionSpreadSheetController@index | Spreadsheet view |

## Planning

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/planning/diary` | PlanningDiaryController@index | Daily diary view |
| GET | `/planning/weekly` | PlanningWeekController@index | Weekly planning view |

## Overview Pages

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/home_overview` | HomeOverviewController@index | Home operations overview |
| GET | `/pc_overview` | PcOverviewController@index | Purchase contract overview |
| GET | `/sc_overview` | ScOverviewController@index | Sales contract overview |

## Transport Sub-Resources

### Transport Job

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| PUT | `/transport_job/{id}` | TransportJobController@update | Update job details |

### Transport Load

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| PUT | `/transport_load/{id}` | TransportLoadController@update | Update load details |
| PUT | `/transport_load/update_units/{id}` | TransportLoadController@updateUnits | Update units |

### Transport Finance

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| PUT | `/transport_finance/{id}` | TransportFinanceController@update | Update financial data |

### Driver/Vehicle Assignments

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/transport_driver_vehicle` | TransportDriverVehicleController@store | Assign driver/vehicle |
| PUT | `/transport_driver_vehicle/{id}` | TransportDriverVehicleController@update | Update assignment |
| DELETE | `/transport_driver_vehicle/{id}` | TransportDriverVehicleController@destroy | Remove assignment |
| PUT | `/transport_driver_vehicle/update_state/{id}` | ...@updateState | Update status flags |
| PUT | `/transport_driver_vehicle/update_load/{id}` | ...@updateLoad | Update load info |
| PUT | `/transport_driver_vehicle/update_driver/{id}` | ...@updateDriverVehicle | Update driver/vehicle |

### Transport Status

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/transport_status` | TransportStatusController@store | Add status |
| PUT | `/transport_status/{id}` | TransportStatusController@update | Update status |
| DELETE | `/transport_status/{id}` | TransportStatusController@destroy | Remove status |

### Commission (AssignedUserComm)

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/assigned_user_comm` | AssignedUserCommController@store | Assign commission user |
| PUT | `/assigned_user_comm/{id}` | AssignedUserCommController@update | Update commission |
| DELETE | `/assigned_user_comm/{id}` | AssignedUserCommController@destroy | Remove commission user |

### Transport Invoice

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| PUT | `/transport_invoice/{id}` | TransportInvoiceController@update | Update invoice |

## Links

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/trans_link` | TransLinkController@store | Create contract link |
| POST | `/trans_link/split` | TransLinkController@storeSplits | Create split links |
| DELETE | `/trans_link/split/delete/{trade}` | TransLinkController@deleteSplits | Delete split links |

## Approvals

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/trans_approval/approve` | TransportApprovalController@approve | Approve deal ticket |
| POST | `/trans_approval/activate` | TransportApprovalController@activate | Activate deal ticket |

## Deal Ticket

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| PUT | `/deal_ticket/{id}` | DealTicketController@update | Update deal ticket |
| GET | `/pdf_report/deal_ticket_view/{id}` | DealTicketController@viewPDF | View deal ticket PDF |
| POST | `/pdf_report/deal_ticket_final/` | DealTicketController@generatePDF | Generate final PDF |
| GET | `/pdf_report/deal_ticket_final/download/{file}` | DealTicketController@downloadPDF | Download PDF |

## Sales Order

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/sales_order/activate` | SalesOrderController@activate | Activate SO |
| POST | `/sales_order/send` | SalesOrderController@send | Mark SO as sent |
| POST | `/sales_order/receive` | SalesOrderController@receive | Mark SO as received |
| GET | `/pdf_report/sales_order_view/{id}` | SalesOrderController@viewPDF | View SO PDF |
| GET | `/pdf_report/sales_order_confirmation_view/{id}` | SalesOrderController@viewConfirmationPDF | View confirmation |
| GET | `/pdf_report/sales_order_confirmation_view_split/{id}/{client_id}` | ...@viewConfirmationPDFSplit | Split confirmation |

## Purchase Order

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/purchase_order/activate` | PurchaseOrderController@activate | Activate PO |
| POST | `/purchase_order/send` | PurchaseOrderController@send | Mark PO as sent |
| POST | `/purchase_order/receive` | PurchaseOrderController@receive | Mark PO as received |
| GET | `/pdf_report/purchase_order_view/{id}` | PurchaseOrderController@viewPDF | View PO PDF |
| GET | `/pdf_report/purchase_order_confirmation_view/{id}` | ...@viewConfirmationPDF | View confirmation |

## Transport Order

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/transport_order/activate` | TransportOrderController@activate | Activate TO |
| POST | `/transport_order/send` | TransportOrderController@send | Mark TO as sent |
| POST | `/transport_order/receive` | TransportOrderController@receive | Mark TO as received |
| GET | `/pdf_report/transport_order_confirmation_view/{id}` | ...@viewConfirmationPDF | View confirmation |

## Customer Management

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/customer` | CustomerController@index | List customers |
| POST | `/customer` | CustomerController@store | Create customer |
| GET | `/customer/{id}` | CustomerController@show | Customer detail |
| PUT | `/customer/{id}` | CustomerController@update | Update customer |
| DELETE | `/customer/{id}` | CustomerController@destroy | Delete customer |
| GET | `/customer_parent` | CustomerParentController@index | List parent customers |
| POST | `/customer_parent` | CustomerParentController@store | Create parent |
| GET | `/customer_parent/{id}` | CustomerParentController@show | Parent detail |
| PUT | `/customer_parent/{id}` | CustomerParentController@update | Update parent |
| DELETE | `/customer_parent/{id}` | CustomerParentController@destroy | Delete parent |
| GET | `/debtor/standing` | DebtorStandingController@index | Debtor standing view |
| GET | `/debtor/calculating` | DebtorStandingController@calculateDebtors | Calculate debtors |

## Supplier Management

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/supplier` | SupplierController@index | List suppliers |
| POST | `/supplier` | SupplierController@store | Create supplier |
| GET | `/supplier/{id}` | SupplierController@show | Supplier detail |
| PUT | `/supplier/{id}` | SupplierController@update | Update supplier |
| DELETE | `/supplier/{id}` | SupplierController@destroy | Delete supplier |

## Product Management

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/product` | ProductController@index | List products |
| POST | `/product` | ProductController@store | Create product |
| GET | `/product/{id}` | ProductController@show | Product detail |
| PUT | `/product/{id}` | ProductController@update | Update product |

## Transport Resources

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET/POST | `/transporter` | TransporterController | CRUD |
| GET/PUT/DELETE | `/transporter/{id}` | TransporterController | Detail/Update/Delete |
| GET/POST | `/regular_driver` | RegularDriverController | CRUD |
| GET/PUT | `/regular_driver/{id}` | RegularDriverController | Detail/Update |
| GET/POST | `/regular_vehicle` | RegularVehicleController | CRUD |
| GET/PUT | `/regular_vehicle/{id}` | RegularVehicleController | Detail/Update |

## Staff & Contacts

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET/POST | `/staff` | StaffController | List/Create staff |
| GET/PUT | `/staff/{id}` | StaffController | Detail/Update staff |
| POST/PUT | `/staff_link` | StaffLinkController | Assign/Update staff link |
| GET/POST | `/contact` | ContactController | List/Create contacts |
| GET/PUT | `/contact/{id}` | ContactController | Detail/Update contact |
| POST/PUT/GET | `/email_contact_detail` | EmailContactDetailController | Email contacts |
| POST/PUT/GET | `/number_contact_detail` | NumberContactDetailController | Phone contacts |
| POST/PUT/DELETE | `/address` | AddressController | Address CRUD |

## Admin

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| POST | `/roles/modify` | RoleModifyController@store | Add role to user |
| PUT | `/roles/modify` | RoleModifyController@destroy | Remove role from user |
| GET | `/notifications` | NotificationController@index | List notifications |
| PUT | `/notification/{id}/seen` | NotificationSeenController@index | Mark notification read |
| GET/POST | `/import` | DataImportsController | Data import interface |

## Reporting

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET/POST | `/custom_report` | CustomReportController | List/Create reports |
| POST | `/custom_report_model` | CustomReportModelController@store | Create report model |

## API Routes (routes/api.php)

| Method | URI | Middleware | Description |
|--------|-----|-----------|-------------|
| GET | `/api/user` | auth:sanctum | Get authenticated user |
