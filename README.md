# 📦 Inventory Management System
### Built with Laravel · Blade · Bootstrap 5 · Double-Entry Accounting

---

## 📌 Project Overview

This is a simplified **Inventory Management System** built with Laravel that handles:

- Product management with stock tracking
- Sales recording with automatic financial calculations
- Expense tracking
- **Double-entry accounting journal entries** (auto-generated on every transaction)
- **Date-wise financial reports** with profit/loss summary

The system is designed around a real business scenario:

> A product is purchased at **100 TK**, sold at **200 TK**, with **50 units** opening stock.
> A sale of **10 units** is made with **50 TK discount**, **5% VAT**, and **1000 TK** partial payment.

---

## 🗂️ Project Structure

```
inventory-management/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php     # KPIs, recent sales, low stock
│   │   ├── ProductController.php       # Product CRUD + opening stock journal
│   │   ├── SaleController.php          # Sale recording + 7-line journal entry
│   │   ├── ExpenseController.php       # Expense recording + journal entry
│   │   └── ReportController.php        # Financial + Journal ledger reports
│   └── Models/
│       ├── Product.php                 # Has many Sales
│       ├── Sale.php                    # Belongs to Product
│       ├── Expense.php
│       ├── JournalEntry.php            # Has many JournalEntryLines
│       └── JournalEntryLine.php        # Individual debit/credit lines
│
├── database/
│   ├── migrations/
│   │   ├── create_products_table.php
│   │   ├── create_sales_table.php
│   │   ├── create_expenses_table.php
│   │   └── create_journal_entries_table.php
│   └── seeders/
│       └── InventorySeeder.php         # Seeds the exact business scenario
│
├── resources/views/
│   ├── layouts/app.blade.php           # Master layout with sidebar
│   ├── dashboard.blade.php
│   ├── products/                       # index, create, edit, show
│   ├── sales/                          # index, create, show (invoice)
│   ├── expenses/                       # index, create
│   └── reports/                        # financial, journal
│
└── routes/web.php                      # All named routes
```

---

## ⚙️ Installation & Setup

### Requirements
- PHP >= 8.1
- Composer
- MySQL
- Laravel 10 or 11

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/your-username/inventory-management.git
cd inventory-management

# 2. Install dependencies
composer install

# 3. Copy environment file and configure
cp .env.example .env
php artisan key:generate

# 4. Set your database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_management_system
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations and seed the demo data
php artisan migrate:fresh
php artisan db:seed --class=InventorySeeder

# 6. Start the development server
php artisan serve
```

Visit: **http://localhost:8000**

> The seeder automatically creates the full business scenario with product, sale, expense, and all journal entries ready to view.

---

## 🔐 Authentication (Laravel Breeze)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run build
```

All routes are protected with `auth` middleware. Users must log in to access the system.

---

## 🗄️ Database Schema

### `products`
| Column | Type | Description |
|---|---|---|
| id | bigint | Primary key |
| name | string | Product name |
| sku | string | Unique product code |
| purchase_price | decimal | Cost price per unit |
| sell_price | decimal | Selling price per unit |
| opening_stock | integer | Initial stock quantity |
| current_stock | integer | Live stock (reduces on every sale) |

### `sales`
| Column | Type | Description |
|---|---|---|
| invoice_no | string | Unique invoice number |
| product_id | FK | Links to products table |
| quantity | integer | Units sold |
| gross_amount | decimal | quantity × sell_price |
| discount | decimal | Discount given in TK |
| vat_percent | decimal | VAT % applied |
| vat_amount | decimal | Calculated VAT in TK |
| net_amount | decimal | gross − discount + vat |
| paid_amount | decimal | Cash received |
| due_amount | decimal | net − paid (receivable) |
| sale_date | date | Transaction date |

### `expenses`
| Column | Type | Description |
|---|---|---|
| title | string | Expense name |
| amount | decimal | Amount in TK |
| category | string | rent, salary, utilities, etc. |
| expense_date | date | Date of expense |

### `journal_entries`
| Column | Type | Description |
|---|---|---|
| reference_no | string | e.g. JE-00001, PUR-00001 |
| reference_type | string | sale / purchase / expense |
| reference_id | bigint | ID of the related record |
| entry_date | date | Journal date |
| description | text | Human-readable description |

### `journal_entry_lines`
| Column | Type | Description |
|---|---|---|
| journal_entry_id | FK | Parent journal entry |
| account_name | string | e.g. Cash, Sales Revenue |
| account_type | string | asset / liability / revenue / expense |
| debit | decimal | Debit amount (0 if credit side) |
| credit | decimal | Credit amount (0 if debit side) |

---

## 💼 How the Business Scenario Works

### Step 1 — Product Added
```
Name:           Mobile Phone X
Purchase Price: 100 TK
Sell Price:     200 TK
Opening Stock:  50 units
```

Auto-generated journal entry:
```
DR  Inventory (Stock)              5,000 TK
    CR  Capital / Accounts Payable         5,000 TK
```
50 units × 100 TK = 5,000 TK of goods entered the business.

---

### Step 2 — Sale Recorded
```
Quantity:    10 units
Unit Price:  200 TK
Discount:    50 TK
VAT:         5%
Payment:     1,000 TK
```

System calculates automatically:
```
Gross Amount    = 10 × 200          = 2,000.00 TK
(-) Discount                        =    -50.00 TK
                                    ────────────
Subtotal                            = 1,950.00 TK
(+) VAT 5% on 1,950                 =    +97.50 TK
                                    ────────────
Net Amount                          = 2,047.50 TK
(-) Payment Received                = -1,000.00 TK
                                    ────────────
Due Balance (Accounts Receivable)   = 1,047.50 TK
```

Auto-generated 7-line journal entry:
```
ACCOUNT                     TYPE        DEBIT      CREDIT
─────────────────────────────────────────────────────────
Cash / Bank                 Asset    1,000.00
Accounts Receivable         Asset    1,047.50
Discount Allowed            Expense     50.00
  Sales Revenue             Revenue              2,000.00
  VAT Payable               Liability               97.50
Cost of Goods Sold (COGS)   Expense  1,000.00
  Inventory (Stock)         Asset                1,000.00
─────────────────────────────────────────────────────────
TOTAL                                3,097.50   3,097.50  ✅ Balanced
```

Why each line exists:

| Line | Reason |
|---|---|
| DR Cash/Bank 1,000 | 1,000 TK physically received from customer |
| DR Accounts Receivable 1,047.50 | Customer still owes this — it's your asset |
| DR Discount Allowed 50 | You gave a discount — that is your loss |
| CR Sales Revenue 2,000 | Full earning from selling 10 units |
| CR VAT Payable 97.50 | Collected on behalf of govt, owed to them |
| DR COGS 1,000 | It cost 10 × 100 TK to acquire these goods |
| CR Inventory 1,000 | 10 units physically left your warehouse |

---

### Step 3 — Expense Added
```
Title:    Office Rent
Amount:   500 TK
Category: Rent
```

Auto-generated journal entry:
```
DR  Rent Expense    500 TK
    CR  Cash / Bank         500 TK
```

---

### Step 4 — Financial Report (Date-wise)

Filter by any date range to get a full breakdown:

```
Total Net Sales   2,047.50 TK
Total Expenses      500.00 TK
Gross Profit      1,047.50 TK
Net Profit/Loss     547.50 TK ✅
```

How Net Profit is calculated:
```
Net Sales Revenue        2,047.50 TK
(-) Cost of Goods Sold   1,000.00 TK   (10 units × 100 TK purchase price)
                         ──────────
Gross Profit             1,047.50 TK
(-) Operating Expenses     500.00 TK   (Office Rent)
                         ──────────
NET PROFIT                 547.50 TK ✅
```

The date-wise table breaks this down per day so you can see exactly which day had which sales and expenses.

---

## 🗺️ Pages & Routes

| Page | URL | What It Does |
|---|---|---|
| Dashboard | `/dashboard` | KPI cards, recent sales, low stock alerts |
| Products List | `/products` | All products with stock levels and values |
| Add Product | `/products/create` | Form with live journal entry preview |
| Edit Product | `/products/{id}/edit` | Update price/name (stock changes via sales only) |
| Product Detail | `/products/{id}` | Full sales history for that product |
| Sales List | `/sales` | All invoices with column totals |
| New Sale | `/sales/create` | Live calculation + real-time journal preview |
| Sale Invoice | `/sales/{id}` | Printable invoice + full journal entry breakdown |
| Expenses | `/expenses` | All expenses listed by date |
| Add Expense | `/expenses/create` | Record a new expense with category |
| Journal Ledger | `/reports/journal` | All DR/CR entries, filterable by date |
| Financial Report | `/reports/financial` | Date-wise P&L with summary cards |

---

## 📒 Accounting Logic — All Entry Types

| Transaction | Debit Side | Credit Side |
|---|---|---|
| Add product (opening stock) | Inventory (Asset) | Capital / Payable |
| Sale — cash received | Cash / Bank | Sales Revenue |
| Sale — amount unpaid | Accounts Receivable | Sales Revenue |
| Sale — discount given | Discount Allowed | Sales Revenue |
| Sale — VAT collected | (part of receivable) | VAT Payable |
| Sale — cost of goods | Cost of Goods Sold | Inventory |
| Add any expense | Expense Account | Cash / Bank |

**The Golden Rule: Every entry must satisfy Total Debit = Total Credit.**
This is standard double-entry bookkeeping as used in real-world accounting systems.

---

## ✅ Feature Checklist

- [x] Product CRUD with SKU and stock tracking
- [x] Opening stock journal entry auto-created on product save
- [x] Sale form with live calculation (gross, VAT, discount, due amount)
- [x] Auto-generated 7-line double-entry journal per sale
- [x] Stock automatically reduces on every sale
- [x] Expense recording with categories
- [x] Expense journal entry auto-created (DR Expense / CR Cash)
- [x] Printable invoice view with full journal entry breakdown
- [x] Journal Ledger report with date range filter
- [x] Financial report with date-wise sales and expense breakdown
- [x] Net Profit/Loss calculation (Revenue − COGS − Expenses)
- [x] Dashboard with KPI summary cards and low stock alerts
- [x] Laravel Breeze authentication support

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 12 |
| Templating | Blade |
| Styling | Bootstrap 5.3 |
| Icons | Bootstrap Icons |
| Database | MySQL |
| Authentication | Laravel Breeze |
| Accounting Model | Double-Entry Bookkeeping |

---

*Built for coursework assessment — Covers Product Management, Sales, Expenses, Accounting Journals, and Financial Reports*
