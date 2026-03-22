Working on financial calculation logic for: **$ARGUMENTS**

Read these files first:
- `app/Models/TransportFinance.php` — specifically the `calculateFields()` method which is the central financial computation
- `AI/BUSINESS_RULES.md` — section 2 "Financial Calculations" for the exact formulas

**Key financial rules to keep in mind:**
- Currency is South African Rand (ZAR)
- The system maintains **dual calculations** — planned (from load units) and actual (from weighbridge weights). Both must stay in sync.
- `is_transport_costs_inc_price` flag: when `true`, transport costs are NOT added to `total_cost_price` (they're already included in selling price)
- `transport_rate_basis_id = 3` means Per Load (flat rate). All other IDs use per-ton calculation.
- Commission = `gross_profit / 2` per side (supplier/customer), then split equally among assigned users
- Up to 5 customers per transaction: `customer_id` through `customer_id_5`, each with their own selling price and transport cost fields

After reading those files, make the requested change. Ensure both planned and actual financial fields are updated consistently.
