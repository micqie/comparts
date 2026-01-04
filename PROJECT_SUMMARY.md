# Comparts - Computer Parts Ordering System
## Project Summary & Module Functions

---

## 📁 ROOT FILES

### `index.php`
**Purpose:** Main entry point and router for the application
- Handles routing based on `module` and `action` GET parameters
- Manages authentication and authorization (admin vs customer)
- Includes appropriate layout files (public/admin/customer)
- Contains inline homepage HTML (hero, about, contact sections)

### `db.php`
**Purpose:** Database connection configuration
- Establishes MySQL connection to `ordering_db` database
- Sets UTF-8 charset
- Handles connection errors

### `config/auth.php`
**Purpose:** Authentication helper functions
**Functions:**
- `isLoggedIn()` - Check if user is logged in
- `isAdmin()` - Check if user is admin
- `requireLogin()` - Redirect to login if not logged in
- `requireAdmin()` - Redirect if not admin
- `getUserId()` - Get current user ID
- `getUsername()` - Get current username
- `getUserRole()` - Get current user role
- `loginUser($user_id, $username, $role)` - Set session variables
- `logoutUser()` - Destroy session

### `create_admin.php`
**Purpose:** Script to create default admin user
- Creates admin user with username: `admin@gmail.com`, password: `admin123`
- Checks if admin already exists before creating
- Hashes password using `password_hash()`

### `assets/css/style.css`
**Purpose:** Global CSS styles
- Defines color scheme (primary: #3b82f6, secondary: #2563eb)
- Styles for sidebar, cards, tables, modals, forms
- Public page styles (hero, footer)
- Responsive design rules

---

## 📁 LAYOUT FILES

### `layout/public_header.php`
**Purpose:** Public site header with navigation
- Bootstrap navbar with Home, About, Contact links
- Login/Register buttons that open modal
- Includes Bootstrap CSS and icons

### `layout/public_footer.php`
**Purpose:** Public site footer
- Footer with brand links (Intel, AMD, NVIDIA)
- Auth modal (login/register forms)
- JavaScript functions: `showLoginForm()`, `showRegisterForm()`
- Handles URL parameters for errors/success messages

### `layout/header.php`
**Purpose:** Admin/Customer header (for authenticated users)

### `layout/footer.php`
**Purpose:** Admin/Customer footer

### `layout/sidebar.php`
**Purpose:** Admin sidebar navigation

### `layout/customer_nav.php`
**Purpose:** Customer navigation bar

---

## 📁 MODULE: AUTH (Authentication)

### `modules/auth/login.php`
**Purpose:** Login page handler
- Redirects to homepage with login modal if not logged in
- Redirects to appropriate dashboard if already logged in

### `modules/auth/logout.php`
**Purpose:** Logout handler
- Calls `logoutUser()` function
- Redirects to login page

### `modules/auth/register.php`
**Purpose:** Registration page handler
- Redirects to homepage with register modal if not logged in
- Redirects to dashboard if already logged in

### `modules/auth/process_login.php`
**Purpose:** Process login form submission
**Functions:**
- Validates email and password
- Queries database for user (checks both `users` and `customers` tables)
- Verifies password using `password_verify()`
- Calls `loginUser()` to set session
- Redirects to admin products page or customer dashboard based on role

### `modules/auth/process_register.php`
**Purpose:** Process customer registration
**Functions:**
- Validates all required fields (username, password, full_name, email)
- Checks password confirmation match
- Checks if username already exists
- Checks if email already exists
- Hashes password
- Creates user in `users` table
- Creates customer record in `customers` table
- Uses database transaction for atomicity
- Redirects to homepage with success message

---

## 📁 MODULE: CATEGORIES (Admin Only)

### `modules/categories/list.php`
**Purpose:** Display all categories in a table
- Shows category ID, name, description
- Add/Edit buttons open modal
- Delete button with confirmation
- JavaScript function: `openCategoryModal(id)` - Fetches category data via AJAX

### `modules/categories/get.php`
**Purpose:** API endpoint - Get single category as JSON
- Returns category data (id, category_name, description) as JSON
- Used by modal to populate edit form

### `modules/categories/save.php`
**Purpose:** Create or update category
**Functions:**
- Validates category_name
- If ID > 0: UPDATE existing category
- If ID = 0: INSERT new category
- Redirects to list page

### `modules/categories/delete.php`
**Purpose:** Delete category
**Functions:**
- Checks if category is used by any products
- If in use: redirects with error message
- If safe: deletes category
- Redirects to list page

---

## 📁 MODULE: PRODUCTS (Admin Only)

### `modules/products/list.php`
**Purpose:** Display all products in a table
- Shows product ID, name, category, price, stock quantity
- Add/Edit buttons open modal
- Delete button with confirmation
- Loads categories for dropdown
- JavaScript function: `openProductModal(id)` - Fetches product data via AJAX

### `modules/products/get.php`
**Purpose:** API endpoint - Get single product as JSON
- Returns product data (id, product_name, category_id, price, stock_quantity) as JSON
- Used by modal to populate edit form

### `modules/products/form.php`
**Purpose:** Standalone product form page (fallback if modal fails)
- Displays form for creating/editing products
- Loads categories for dropdown
- Pre-fills form if editing

### `modules/products/save.php`
**Purpose:** Create or update product
**Functions:**
- Validates product_name and category_id
- If ID > 0: UPDATE existing product
- If ID = 0: INSERT new product
- Redirects to list page

### `modules/products/delete.php`
**Purpose:** Delete product
**Functions:**
- Deletes product by ID
- Redirects to list page

---

## 📁 MODULE: CUSTOMERS (Admin Only)

### `modules/customers/list.php`
**Purpose:** Display all customers in a table
- Shows customer ID, name, email, contact, address
- Add/Edit buttons open modal
- Delete button with confirmation
- JavaScript function: `openCustomerModal(id)` - Fetches customer data via AJAX

### `modules/customers/get.php`
**Purpose:** API endpoint - Get single customer as JSON
- Returns customer data (id, full_name, email, contact_number, address) as JSON
- Used by modal to populate edit form

### `modules/customers/form.php`
**Purpose:** Standalone customer form page (fallback if modal fails)
- Displays form for creating/editing customers
- Pre-fills form if editing

### `modules/customers/save.php`
**Purpose:** Create or update customer
**Functions:**
- Validates full_name and email
- If ID > 0: UPDATE existing customer
- If ID = 0: INSERT new customer (with user_id = 0)
- Redirects to list page

### `modules/customers/delete.php`
**Purpose:** Delete customer
**Functions:**
- Deletes customer by ID
- Redirects to list page

---

## 📁 MODULE: ORDERS (Admin Only)

### `modules/orders/list.php`
**Purpose:** Display all orders in a table
- Shows order ID, customer name, date, total, status
- View and Delete buttons
- Status badges (pending/completed/cancelled)

### `modules/orders/get.php`
**Purpose:** API endpoint - Get single order as JSON
**Functions:**
- Checks if customer can view order (if not admin)
- Returns order data with customer info and order items
- Returns JSON: order details + items array

### `modules/orders/form.php`
**Purpose:** Create new order form
- Customer dropdown
- Dynamic items table (add/remove rows)
- JavaScript calculates line totals and grand total
- Shows product price and stock when product selected
- Functions: `recalcRow()`, `recalcGrandTotal()`, `bindRowEvents()`

### `modules/orders/save.php`
**Purpose:** Save new order with items
**Functions:**
- Validates customer_id and items
- Starts database transaction
- Inserts order record
- For each item:
  - Gets product price and stock (with FOR UPDATE lock)
  - Validates stock availability
  - Inserts order_detail
  - Updates product stock
  - Records inventory_transaction (type: 'out')
- Calculates and updates order total
- Commits transaction or rolls back on error
- Redirects to list page

### `modules/orders/view.php`
**Purpose:** View single order details
**Functions:**
- Checks if customer can view order (if not admin)
- Displays customer information card
- Displays order information card (date, status, total)
- Shows order items table
- Admin can confirm payment (if status is pending)

### `modules/orders/delete.php`
**Purpose:** Delete order
**Functions:**
- Deletes order_details first (foreign key constraint)
- Deletes order
- Note: Does NOT restore stock quantities
- Redirects to list page

### `modules/orders/complete.php`
**Purpose:** Complete order payment (Admin only)
**Functions:**
- Verifies user is admin
- Verifies order exists and is pending
- Updates order status to 'completed'
- Redirects to order view page with success message

---

## 📁 MODULE: CUSTOMER (Customer Portal)

### `modules/customer/dashboard.php`
**Purpose:** Customer dashboard
**Functions:**
- Gets customer info from database
- Calculates statistics:
  - Total orders count
  - Total spent (completed orders only)
  - Pending orders count
- Displays recent orders (last 5)
- Shows stats cards and orders table

### `modules/customer/products.php`
**Purpose:** Browse products (customer view)
**Functions:**
- Filters products by search term and category
- Only shows products with stock_quantity > 0
- Displays products in grid layout
- Each product has "Add to Cart" form with quantity input
- Search and category filter functionality

### `modules/customer/cart.php`
**Purpose:** Shopping cart display
**Functions:**
- Reads cart from `$_SESSION['cart']`
- Calculates subtotals and grand total
- Displays cart items in table
- Update quantity form for each item
- Remove item button
- "Proceed to Checkout" button

### `modules/customer/add_to_cart.php`
**Purpose:** Add product to cart
**Functions:**
- Validates product_id and quantity
- Gets product info from database
- Checks stock availability
- If product already in cart: updates quantity (if within stock limit)
- If new product: adds to cart array
- Stores cart in `$_SESSION['cart']`
- Redirects to cart page

### `modules/customer/remove_from_cart.php`
**Purpose:** Remove item from cart
**Functions:**
- Gets item index from POST
- Removes item from `$_SESSION['cart']` array
- Redirects to cart page

### `modules/customer/update_cart.php`
**Purpose:** Update cart item quantity
**Functions:**
- Gets item index and new quantity from POST
- Checks stock availability in database
- Updates quantity in `$_SESSION['cart']` if stock allows
- Redirects to cart page

### `modules/customer/checkout.php`
**Purpose:** Checkout page
**Functions:**
- Validates cart is not empty
- Gets customer info from database
- Verifies stock for all items
- Displays order summary table
- Displays delivery information
- On form submission (`confirm_order`):
  - Starts database transaction
  - Inserts order record
  - For each item:
    - Inserts order_detail
    - Updates product stock
    - Records inventory_transaction
  - Commits transaction
  - Clears cart from session
  - Redirects to orders page with success message

### `modules/customer/orders.php`
**Purpose:** Customer orders list
**Functions:**
- Gets customer ID from user_id
- Fetches all orders for this customer
- Displays orders in table
- JavaScript function: `viewOrderDetails(orderId)` - Opens modal with order details
- JavaScript function: `processPayment(orderId)` - Processes payment via AJAX
- JavaScript function: `escapeHtml(text)` - XSS protection

### `modules/customer/pay.php`
**Purpose:** Process payment for order (API endpoint)
**Functions:**
- Validates order_id
- Gets customer ID from user_id
- Verifies order belongs to customer and is pending
- Updates order status to 'completed'
- Returns JSON response: `{success: true/false, message: string}`

---

## 📁 MODULE: REPORTS (Admin Only)

### `modules/reports/dashboard.php`
**Purpose:** Reports dashboard with analytics
**Functions:**
- Calculates statistics:
  - Total sales (completed orders)
  - Total orders count
  - Total customers count
  - Total products count
  - Pending orders count
- Gets sales by month (last 6 months)
- Gets top selling products (top 5)
- Gets sales by status
- Displays charts using Chart.js:
  - Line chart: Sales trend
  - Doughnut chart: Orders by status
- Displays top products table

### `modules/reports/sales.php`
**Purpose:** Detailed sales report
**Functions:**
- Gets date range from query parameters (defaults to current month)
- Calculates summary:
  - Order count
  - Total sales
  - Average order value
- Gets daily sales breakdown
- Gets product sales breakdown
- Displays bar chart: Daily sales
- Displays product sales table
- Date filter form

---

## 📊 SUMMARY BY MODULE

### Auth Module (5 files)
- Login/logout/register handlers
- Form processing with validation
- Session management

### Categories Module (4 files)
- CRUD operations (Create, Read, Update, Delete)
- Validation (prevents deletion if in use)

### Products Module (5 files)
- CRUD operations
- Category association
- Stock management

### Customers Module (5 files)
- CRUD operations
- Customer information management

### Orders Module (7 files)
- Order creation with multiple items
- Order viewing with details
- Order status management
- Payment processing
- Stock updates on order creation
- Inventory transaction logging

### Customer Module (9 files)
- Dashboard with statistics
- Product browsing with filters
- Shopping cart (session-based)
- Checkout process
- Order history
- Payment processing

### Reports Module (2 files)
- Analytics dashboard
- Sales reports with date filtering
- Charts and visualizations

---

## 🔑 KEY FEATURES

1. **Authentication System**: Role-based (admin/customer) with session management
2. **Shopping Cart**: Session-based cart with stock validation
3. **Order Management**: Full order lifecycle (pending → completed)
4. **Stock Management**: Automatic stock updates on order creation
5. **Inventory Tracking**: Records all inventory transactions
6. **Reports & Analytics**: Sales trends, top products, status breakdowns
7. **Responsive Design**: Bootstrap-based UI with custom styling
8. **Security**: Prepared statements, password hashing, XSS protection

---

## 📝 NOTES

- All database operations use prepared statements for security
- Transactions used for multi-step operations (order creation, registration)
- Stock validation prevents overselling
- Admin and customer have separate interfaces
- Public homepage with contact information (no forms)
- Modal-based forms for quick CRUD operations
- JSON API endpoints for AJAX operations
