## Computer Parts Ordering System (PHP + MySQL)

This is a simple modular CRUD-based ordering system for computer parts, built on top of your existing `db.php` connection and `ordering_db.sql` schema.

### Features

- Products (computer parts) CRUD
- Customers CRUD
- Orders CRUD with:
  - Multiple products per order
  - Automatic total calculation
  - Stock deduction and inventory transaction records

### Project Structure (key files)

- `db.php` – database connection (already provided)
- `index.php` – front controller and simple router
- `layout/header.php`, `layout/nav.php`, `layout/footer.php` – shared layout
- `modules/products/*` – product CRUD pages
- `modules/customers/*` – customer CRUD pages
- `modules/orders/*` – order CRUD and view pages

### Setup

1. Import `ordering_db.sql` into MySQL to create the database and tables.
2. Make sure `db.php` has the correct credentials and database name.
3. Place the project under your web root (e.g. `C:\xampp\htdocs\comparts`).
4. Open in browser:
   - `http://localhost/comparts/index.php`

You should see navigation for **Products**, **Customers**, and **Orders**, each providing full CRUD operations.

# comparts
