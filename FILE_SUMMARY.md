# Computer Parts Ordering System - File Summary

## Core Files

### `index.php`
**Summary:** Main entry point and router for the application
**Functions:**
- Routes requests based on `module` and `action` parameters
- Handles authentication checks and role-based access control
- Includes appropriate layout files (header, sidebar, footer)
- Renders public homepage with hero, features, about, and contact sections
- Dispatches to module-specific action files

### `db.php`
**Summary:** Database connection configuration
**Functions:**
- Establishes MySQL connection to `ordering_db` database
- Sets UTF-8 character encoding
- Handles connection errors

### `config/auth.php`
**Summary:** Authentication helper functions
**Functions:**
- `isLoggedIn()` - Checks if user is logged in
- `isAdmin()` - Checks if current user is admin
- `requireLogin()` - Redirects to login if not authenticated
- `requireAdmin()` - Redirects to login if not admin
- `getUserId()` - Returns current user ID
- `getUsername()` - Returns current username
- `getUserRole()` - Returns current user role
- `loginUser($user_id, $username, $role)` - Sets session variables
- `logoutUser()` - Destroys session

### `create_admin.php`
**Summary:** Script to create default admin user
**Functions:**
- Creates admin user with default credentials (admin@gmail.com / admin123)
- Checks if admin already exists
- Hashes password before storing
- Provides security warnings

---

## Layout Files

### `layout/public_header.php`
**Summary:** Public-facing header with navigation
**Functions:**
- Renders HTML head with Bootstrap CSS and icons
- Displays top navigation bar with Home, About, Contact links
- Shows Login/Register buttons
- Sets up public body styling

### `layout/public_footer.php`
**Summary:** Public footer with authentication modal
**Functions:**
- Renders footer with copyright and vendor links
- Contains authentication modal (login/register forms)
- JavaScript functions:
  - `showLoginForm()` - Switches modal to login form
  - `showRegisterForm()` - Switches modal to register form
  - Auto-opens modal based on URL parameters
  - Handles error/success message display

### `layout/header.php`
**Summary:** Basic HTML header for authenticated pages
**Functions:**
- Renders HTML head with Bootstrap, icons, and Chart.js
- Sets up basic body structure

### `layout/footer.php`
**Summary:** Basic footer for authenticated pages
**Functions:**
- Auto-closes modals after form submission
- Closes HTML body and document

### `layout/sidebar.php`
**Summary:** Admin sidebar navigation
**Functions:**
- Displays admin navigation menu (Categories, Products, Customers, Orders, Reports)
- Shows current user info and logout button
- Mobile-responsive menu toggle

### `layout/customer_nav.php`
**Summary:** Customer sidebar navigation
**Functions:**
- Displays customer navigation menu (Dashboard, Products, Cart, Orders)
- Shows cart item count badge
- Displays user info and logout button
- Mobile-responsive menu toggle

---

## Authentication Module (`modules/auth/`)

### `modules/auth/login.php`
**Summary:** Login page redirect handler
**Functions:**
- Redirects already logged-in users to appropriate dashboard
- Redirects to public home with login modal

### `modules/auth/process_login.php`
**Summary:** Processes login form submission
**Functions:**
- Validates email and password
- Queries user from database (checks both users and customers tables)
- Verifies password using `password_verify()`
- Sets session variables via `loginUser()`
- Redirects to appropriate dashboard based on role

### `modules/auth/register.php`
**Summary:** Registration page redirect handler
**Functions:**
- Redirects already logged-in users
- Redirects to public home with register modal

### `modules/auth/process_register.php`
**Summary:** Processes registration form submission
**Functions:**
- Validates all required fields
- Checks password confirmation match
- Checks for duplicate username and email
- Hashes password
- Creates user record in `users` table
- Creates customer record in `customers` table
- Uses database transaction for data integrity
- Redirects to login on success

### `modules/auth/logout.php`
**Summary:** Logout handler
**Functions:**
- Calls `logoutUser()` to destroy session
- Redirects to login page

---

## Categories Module (`modules/categories/`)

### `modules/categories/list.php`
**Summary:** Lists all product categories
**Functions:**
- Displays categories in a table
- Provides Add/Edit/Delete actions
- JavaScript: `openCategoryModal(id)` - Opens modal for add/edit

### `modules/categories/save.php`
**Summary:** Saves new or updates existing category
**Functions:**
- Handles POST data for category name
- Inserts new category or updates existing
- Returns JSON response

### `modules/categories/delete.php`
**Summary:** Deletes a category
**Functions:**
- Validates category ID
- Deletes category from database
- Returns JSON response

### `modules/categories/get.php`
**Summary:** API endpoint to get category data
**Functions:**
- Returns category data as JSON
- Used for populating edit forms

---

## Products Module (`modules/products/`)

### `modules/products/list.php`
**Summary:** Lists all products
**Functions:**
- Displays products with category, price, stock
- Provides Add/Edit/Delete actions
- JavaScript: `openProductModal(id)` - Opens modal for add/edit

### `modules/products/form.php`
**Summary:** Product form page (if used separately)
**Functions:**
- Renders product creation/edit form

### `modules/products/save.php`
**Summary:** Saves new or updates existing product
**Functions:**
- Handles POST data (name, category, price, stock)
- Validates data
- Inserts or updates product
- Redirects to product list

### `modules/products/delete.php`
**Summary:** Deletes a product
**Functions:**
- Validates product ID
- Deletes product from database
- Returns JSON response

### `modules/products/get.php`
**Summary:** API endpoint to get product data
**Functions:**
- Returns product data as JSON
- Used for populating edit forms

---

## Customers Module (`modules/customers/`)

### `modules/customers/list.php`
**Summary:** Lists all customers
**Functions:**
- Displays customers in a table
- Provides Add/Edit/Delete actions
- JavaScript: `openCustomerModal(id)` - Opens modal for add/edit

### `modules/customers/form.php`
**Summary:** Customer form page
**Functions:**
- Renders customer creation/edit form
- Pre-fills form if editing existing customer

### `modules/customers/save.php`
**Summary:** Saves new or updates existing customer
**Functions:**
- Handles POST data (full_name, email, contact_number, address)
- Validates data
- Inserts or updates customer record
- Redirects to customer list

### `modules/customers/delete.php`
**Summary:** Deletes a customer
**Functions:**
- Validates customer ID
- Deletes customer from database
- Returns JSON response

### `modules/customers/get.php`
**Summary:** API endpoint to get customer data
**Functions:**
- Returns customer data as JSON
- Used for populating edit forms

---

## Orders Module (`modules/orders/`)

### `modules/orders/list.php`
**Summary:** Lists all orders
**Functions:**
- Displays orders with customer, date, total, status
- Provides View/Complete/Delete actions
- Shows status badges (pending, completed, cancelled)

### `modules/orders/form.php`
**Summary:** Order creation form
**Functions:**
- Renders form to create new order
- Allows selecting customer and products
- Calculates order total

### `modules/orders/save.php`
**Summary:** Saves new order
**Functions:**
- Handles POST data for order creation
- Creates order and order items
- Updates product stock quantities
- Uses database transaction

### `modules/orders/view.php`
**Summary:** View order details
**Functions:**
- Displays full order information
- Shows order items and totals
- Displays customer information

### `modules/orders/complete.php`
**Summary:** Marks order as completed
**Functions:**
- Updates order status to 'completed'
- Returns JSON response

### `modules/orders/delete.php`
**Summary:** Deletes an order
**Functions:**
- Validates order ID
- Deletes order and order items
- Returns JSON response

### `modules/orders/get.php`
**Summary:** API endpoint to get order data
**Functions:**
- Returns order data as JSON
- Used for populating edit forms

---

## Customer Module (`modules/customer/`)

### `modules/customer/dashboard.php`
**Summary:** Customer dashboard
**Functions:**
- Displays customer statistics (total orders, total spent, pending orders)
- Shows recent orders
- Displays welcome message with customer name

### `modules/customer/products.php`
**Summary:** Product catalog for customers
**Functions:**
- Displays available products in a grid
- Shows product details (name, price, stock)
- Provides "Add to Cart" functionality
- Filters by category

### `modules/customer/cart.php`
**Summary:** Shopping cart page
**Functions:**
- Displays cart items with quantities and subtotals
- Allows updating quantities
- Allows removing items
- Shows cart total
- Provides checkout button

### `modules/customer/add_to_cart.php`
**Summary:** Adds product to cart
**Functions:**
- Adds product to session cart array
- Updates quantity if product already in cart
- Returns JSON response

### `modules/customer/update_cart.php`
**Summary:** Updates cart item quantity
**Functions:**
- Updates quantity for cart item
- Validates stock availability
- Returns JSON response

### `modules/customer/remove_from_cart.php`
**Summary:** Removes item from cart
**Functions:**
- Removes product from session cart
- Returns JSON response

### `modules/customer/checkout.php`
**Summary:** Checkout page
**Functions:**
- Displays order summary
- Shows customer information
- Allows placing order
- Creates order in database

### `modules/customer/pay.php`
**Summary:** Payment processing (API endpoint)
**Functions:**
- Processes payment for order
- Updates order status
- Clears cart
- Returns JSON response

### `modules/customer/orders.php`
**Summary:** Customer's order history
**Functions:**
- Lists all orders for logged-in customer
- Shows order status and details
- JavaScript functions:
  - `viewOrderDetails(orderId)` - Shows order details modal
  - `processPayment(orderId)` - Processes payment
  - `escapeHtml(text)` - HTML escaping utility

---

## Reports Module (`modules/reports/`)

### `modules/reports/dashboard.php`
**Summary:** Admin reports dashboard
**Functions:**
- Displays key statistics (total sales, orders, customers, products, pending orders)
- Shows sales chart (last 6 months)
- Displays top products by sales
- Shows recent orders table
- Uses Chart.js for visualizations

### `modules/reports/sales.php`
**Summary:** Detailed sales report
**Functions:**
- Generates comprehensive sales report
- Filters by date range
- Shows sales breakdown by product/category
- Exports functionality (if implemented)

---

## Assets

### `assets/css/style.css`
**Summary:** Main stylesheet
**Functions:**
- Defines CSS variables for colors and layout
- Styles for public pages (hero, sections)
- Sidebar navigation styles
- Card, table, button, modal styles
- Responsive design (mobile menu)
- Form styling
- Chart container styles

---

## Database

### `ordering_db.sql`
**Summary:** Database schema file
**Functions:**
- Contains CREATE TABLE statements
- Defines database structure (users, customers, categories, products, orders, order_items)

---

## Summary by Function Type

### Authentication Functions
- Login/logout
- Registration
- Session management
- Role checking

### CRUD Operations
- Create, Read, Update, Delete for:
  - Categories
  - Products
  - Customers
  - Orders

### Shopping Cart Functions
- Add to cart
- Update cart
- Remove from cart
- Checkout

### Reporting Functions
- Sales statistics
- Order analytics
- Dashboard metrics
- Charts and visualizations

### UI/UX Functions
- Modal management
- Form validation
- Responsive navigation
- Error/success messaging
