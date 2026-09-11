# ci-datatables Sample Application

This directory contains a complete, ready-to-run **CodeIgniter 4** sample application demonstrating the [`cahyadsn/ci-datatables`](https://github.com/cahyadsn/ci-datatables) library with server-side processing for DataTables 2.x.

---

## 🚀 Quick Start (Zero Configuration)

The sample application is configured with an embedded **SQLite** database by default. Tables and 50 realistic sample employee records are automatically migrated and seeded upon first run!

### Option 1: Using CodeIgniter Spark (Recommended)

From the project root, navigate to `apps/` and launch the built-in development server:

```bash
cd apps
php spark serve
```

Then open your browser and visit:  
👉 **`http://localhost:8080`**

---

### Option 2: Using PHP Built-in Server

You can also run directly using PHP's built-in web server:

```bash
php -S localhost:8080 -t apps/public
```

Then navigate to:  
👉 **`http://localhost:8080`**

---

### Option 3: Using Laragon / Apache Virtual Host

If running inside Laragon or Apache:
1. Point your Apache DocumentRoot to the `apps/public` folder, or
2. If accessing via directory path (e.g. `http://localhost/ci-datatables/apps/`), the entry `index.php` will automatically direct requests to `public/`.

---

## 🗄️ Optional: Using MySQL Database

If you prefer to run against MySQL:

1. Create a MySQL database (e.g., `ci_datatables_sample`):
   ```sql
   CREATE DATABASE ci_datatables_sample CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import the included schema and data dump:
   ```bash
   mysql -u root ci_datatables_sample < apps/sample.sql
   ```

3. Update `apps/.env` with your MySQL credentials:
   ```env
   database.default.hostname = localhost
   database.default.database = ci_datatables_sample
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   ```

---

## 💡 Key Features Demonstrated

This sample demonstrates:

1. **Table Joins (`join`)**: Joining `employees` with `departments` to display relational data.
2. **Column Modifications (`editColumn`)**:
   - Dynamic currency formatting for salaries (`$ $1`).
   - HTML badge rendering for employee status (`Active`, `Inactive`, `On Leave`).
3. **Synthetic Columns (`addColumn`)**: Adding an interactive action column with View Modal & Edit buttons dynamically referencing the record's primary ID (`$1`).
4. **Custom Filtering (`where`)**: Dropdown filters for Department and Status that update the table on-the-fly.
5. **Server-side Paging, Sorting, and Searching**: Fully handled by `ci-datatables`.

---

## 📁 Application Structure

```
apps/
├── app/
│   ├── Config/              # App, Database, Routes, Autoload, Paths configuration
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── Home.php         # Redirects to /employees
│   │   └── Employees.php    # ci-datatables implementation & AJAX endpoints
│   ├── Database/
│   │   ├── Migrations/      # Migration to create departments & employees tables
│   │   └── Seeds/           # Seeder with 50 realistic employee records
│   └── Views/
│       └── employees.php    # Modern responsive UI with Bootstrap 5 & DataTables 2.x
├── public/
│   ├── index.php            # Web front controller
│   └── .htaccess            # Apache URL rewrite rules
├── writable/
│   └── database/            # SQLite database storage (sample.db)
├── .env                     # Environment settings
├── spark                    # CodeIgniter 4 CLI tool
├── sample.sql               # MySQL dump
└── README.md                # This documentation
```
