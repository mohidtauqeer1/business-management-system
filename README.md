

# Business Management System

A comprehensive Laravel-based Business Management System designed to manage products, inventory, purchases, sales, customers, suppliers, payments, returns, expenses, financial reports, and business activity auditing.

The system is designed for small and medium-sized businesses that need a centralized solution for managing their complete purchasing-to-sales workflow.

---

## 🚀 Project Overview

The Business Management System manages the complete business cycle:

**Supplier → Purchase → Inventory → Sale → Customer → Payment → Financial Reporting**

The application provides role-based access control, transaction-safe inventory management, customer credit control, supplier/customer payment tracking, sales and purchase returns, expenses, financial reporting, PDF invoices, and activity logging.

The application follows a layered Laravel architecture with a dedicated Service Layer for business logic and database transactions.

---

## ✨ Key Features

### 🔐 Authentication & Authorization

- User login and logout
- Session management
- Password hashing
- Active/inactive user status
- Role-based access control
- Protected routes
- Unauthorized access returns `403 Forbidden`

### 👥 User Roles

The system supports:

- **Admin**
- **Manager**
- **Cashier**
- **Staff**

Different roles have access to different areas of the application.

---

### 📦 Product & Category Management

- Create, update and manage products
- Product SKU management
- Product categories
- Parent/child categories
- Purchase price
- Selling price
- Stock quantity
- Reorder level
- Product status
- Low-stock identification

---

### 🏭 Supplier Management

- Supplier records
- Supplier contact information
- Supplier purchase history
- Supplier payment tracking
- Outstanding supplier balances

---

### 🛒 Purchase Management

- Create purchase invoices
- Add multiple products to a purchase
- Automatic subtotal and total calculation
- Automatic inventory increase
- Supplier payment tracking
- Purchase history
- Purchase invoices
- Purchase returns

All important purchase operations are processed using database transactions to maintain data integrity.

---

### 🛍️ Sales / POS

- Create sales
- Customer selection
- Product selection
- Quantity validation
- Automatic stock deduction
- Discounts
- Tax calculation
- Payment status tracking
- Payment method tracking
- Sales invoices
- PDF invoice generation
- Sales returns

The system prevents sales from reducing inventory below zero.

---

### 💳 Customer Credit Management

The system supports customer credit sales and credit limits.

A customer can purchase products without paying the full amount immediately, provided the transaction does not exceed their configured credit limit.

The system tracks:

- Customer credit limit
- Outstanding balance
- Paid amount
- Partial payments
- Customer payment history
- Credit availability

Excessive credit sales are automatically rejected.

---

### 💰 Payment Management

Supports payments for both customers and suppliers.

Payment methods include:

- Cash
- Bank
- Card
- Online

The system prevents payments from exceeding the outstanding balance.

Payments are associated with the relevant purchase or sale and recorded in the payment ledger.

---

### 🔄 Returns Management

#### Sales Returns

- Return sold products
- Validate return quantity
- Restock returned products when applicable
- Adjust customer balances/refunds
- Record return activity

#### Purchase Returns

- Return purchased products to suppliers
- Validate return quantities
- Reduce inventory
- Update supplier-related financial records
- Record return activity

---

### 📊 Inventory Management

Inventory is controlled through a dedicated stock management service.

The system supports:

- Stock increases
- Stock decreases
- Manual stock adjustments
- Stock movement history
- Purchase stock increases
- Sale stock decreases
- Sales return restocking
- Purchase return stock reduction
- Low-stock monitoring

Every important stock operation records:

- Product
- Movement type
- Quantity
- Stock before
- Stock after
- Reference transaction
- User
- Notes

---

### 💸 Expense Management

- Record business expenses
- Categorize expenses
- Track expense amounts
- Include expenses in financial reporting
- Activity logging

---

### 📈 Reports & Business Intelligence

The system provides business reporting features including:

- Profit & Loss
- Sales reports
- Purchase reports
- Inventory reports
- Financial information
- Dashboard statistics
- Business performance insights

The Profit & Loss calculation considers sales revenue, historical product costs, returns, and expenses to provide a more meaningful view of business profitability.

---

### 🧾 PDF Invoices

The system generates printable PDF invoices for sales transactions.

Invoices contain relevant transaction information including:

- Invoice number
- Customer
- Products
- Quantities
- Prices
- Discounts
- Tax
- Total
- Payment information

---

### 📝 Activity & Audit Logs

Important system operations are automatically logged.

Activity logs can be used to track:

- User actions
- Resource operations
- Authentication-related activity
- Business transactions
- System changes

This provides an audit trail for accountability and security.

---

## 🏗️ Architecture

The project follows a Laravel MVC architecture with a dedicated Service Layer.

```text
Business Management System
│
├── Presentation Layer
│   └── Blade Views + Custom CSS
│
├── Application Layer
│   ├── Controllers
│   ├── Form Requests
│   └── Middleware
│
├── Service Layer
│   ├── PurchaseService
│   ├── SaleService
│   ├── PaymentService
│   ├── StockService
│   └── Other Business Services
│
├── Data Layer
│   ├── Eloquent Models
│   ├── Migrations
│   └── MySQL
│
└── Supporting Systems
    ├── Authentication
    ├── RBAC
    ├── Activity Logging
    ├── PDF Generation
    └── Reporting

Business logic is kept inside dedicated services instead of placing complex operations directly inside controllers.


---

🛡️ Business Rules & Data Integrity

The system implements several important business rules:

❌ Inventory cannot become negative

❌ Customers cannot exceed their credit limit

❌ Payments cannot exceed outstanding balances

❌ Invalid return quantities are rejected

✅ Historical product costs are preserved for financial calculations

✅ Critical operations use database transactions

✅ Stock changes are recorded as stock movements

✅ Important operations are recorded in activity logs


These rules help maintain consistency between inventory, sales, purchases, payments, and financial records.


---

🗄️ Database

The application uses MySQL with foreign-key constraints and relational Eloquent models.

Major tables include:

users
categories
products
suppliers
customers
purchases
purchase_items
sales
sale_items
payments
stock_movements
sale_returns
sale_return_items
purchase_returns
purchase_return_items
expenses
activity_logs
settings


---

🧮 Financial Calculations

The system calculates important financial values server-side.

Line Item

Line Total = Quantity × Unit Price

Subtotal

Subtotal = Sum of Line Totals

Grand Total

Grand Total = Subtotal + Tax - Discount

Customer Receivable

Outstanding Balance =
Unpaid Sales - Payments - Applicable Returns/Refunds

Gross Profit

Gross Profit =
Sales Revenue - Cost of Goods Sold

Net Profit

Net Profit =
Gross Profit - Operating Expenses

Historical product costs are preserved so that profitability is not incorrectly calculated using a product's current purchase price.


---

🧪 Testing

The project has been tested through a comprehensive automated test suite.

Current Test Results

Tests Passed:     38 / 38
Assertions:       97
Execution Time:   ~4.44 seconds
Status:           PASS

Tested Functionality

Test Area	Status

Authentication	✅
Admin Permissions	✅
Manager Permissions	✅
Category Creation	✅
Product Creation	✅
Supplier Creation	✅
Purchase Processing	✅
Inventory Tracking	✅
Sales Processing	✅
Customer Credit	✅
Supplier Payments	✅
Customer Payments	✅
Sales Returns	✅
Purchase Returns	✅
Expenses	✅
Profit & Loss	✅
Reports	✅
PDF Invoices	✅
Activity Logging	✅


Run Tests

php artisan test


---

🛠️ Technologies Used

Technology	Purpose

PHP 8.2+	Backend
Laravel 11	Application Framework
MySQL	Database
Eloquent ORM	Database Interaction
Blade	Frontend Templating
Vanilla CSS	UI Styling
JavaScript	Frontend Interactions
Chart.js	Dashboard & Data Visualization
DomPDF	PDF Invoice Generation
PHPUnit	Automated Testing
Git	Version Control
GitHub	Source Code Management



---

⚙️ Installation

1. Clone the Repository

git clone https://github.com/mohidtauqeer1/business-management-system.git

2. Navigate to the Project

cd business-management-system

3. Install PHP Dependencies

composer install

4. Install Frontend Dependencies

npm install

5. Create Environment File

cp .env.example .env

On Windows, you can also create .env manually from .env.example.

6. Generate Application Key

php artisan key:generate

7. Configure Database

Update the database configuration in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=business_management
DB_USERNAME=root
DB_PASSWORD=

8. Run Migrations

php artisan migrate

If the project contains seeders:

php artisan db:seed

9. Create Storage Link

php artisan storage:link

10. Start the Laravel Development Server

php artisan serve

The application will be available at:

http://127.0.0.1:8000

11. Compile Frontend Assets

For development:

npm run dev

For production:

npm run build


---

🔒 Security

The application includes several security mechanisms:

CSRF protection

Laravel authentication

Password hashing

Eloquent/PDO parameterized database queries

Blade escaping for XSS protection

Role-based route protection

Active/inactive user control

Session regeneration on authentication

Database transactions for critical operations

Audit/activity logging

Foreign-key constraints



---

📁 Project Structure

app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
│
├── Models/
│
└── Services/
    ├── PurchaseService.php
    ├── SaleService.php
    ├── PaymentService.php
    └── StockService.php

database/
├── migrations/
└── seeders/

resources/
├── views/
└── css/

routes/
├── web.php
└── ...

tests/
├── Feature/
└── Unit/

public/
└── ...


---

🔄 Core Business Workflow

Purchase Flow

Supplier
   ↓
Purchase
   ↓
Purchase Items
   ↓
Stock Increase
   ↓
Stock Movement
   ↓
Supplier Balance / Payment

Sales Flow

Customer
   ↓
Sale
   ↓
Sale Items
   ↓
Stock Validation
   ↓
Stock Decrease
   ↓
Stock Movement
   ↓
Payment / Customer Credit
   ↓
Invoice

Return Flow

Sale / Purchase
      ↓
Return Request
      ↓
Quantity Validation
      ↓
Inventory Adjustment
      ↓
Financial Adjustment
      ↓
Activity Log


---

📌 Current Project Status

The project has completed its core development and automated testing phase.

Core Development       ✅
Business Logic         ✅
Authentication         ✅
RBAC                   ✅
Inventory Management   ✅
Sales & Purchases      ✅
Payments               ✅
Returns                ✅
Financial Reports      ✅
PDF Invoices           ✅
Activity Logging       ✅
Automated Testing      ✅
38/38 Tests Passed     ✅
Production Deployment  🚧


---

🚧 Future Roadmap

Potential future improvements include:

Multi-branch / multi-location support

Hardware POS integration

REST API

Automated customer/supplier notifications

Additional reporting and analytics

Expanded dashboard functionality



---

🎯 Project Purpose

This project was developed as a practical Laravel application focused on solving real-world business management problems.

Rather than being limited to basic CRUD operations, the system implements:

Transaction-safe business workflows

Inventory accounting

Customer credit control

Supplier accounts payable

Customer receivables

Returns management

Financial calculations

Role-based authorization

Audit logging

Automated testing


The goal is to demonstrate the development of a maintainable, secure, and business-oriented Laravel application.


---

👨‍💻 Author

Mohid Tauqeer

Computer Science Student & Software Developer

GitHub:
https://github.com/mohidtauqeer1


---

📄 License

This project is intended for educational, portfolio, and development 
