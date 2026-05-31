# AidFlow — Premium Welfare & Fund Management System

AidFlow is a secure, modern, and production-ready **Welfare Management System** built with **PHP 8, MySQL, Bootstrap 5, Vanilla CSS, AJAX, PHPMailer, and MVC Architecture**. 

Designed for communities, churches, schools, clubs, and welfare organizations, AidFlow streamlines member management, automates monthly contributions, facilitates welfare request workflows, logs fund disbursements, maintains system settings, and generates structured analytical reports—all under an audited, secure, role-based ecosystem.

---

## 🚀 Key Modules & Role Ecosystem

AidFlow implements **four distinct user roles** with customized dashboards and visual panels:

### 1. 🔑 Administrator Workspace
* **Member Directory:** Approve or reject newly registered pending members.
* **Welfare Governance:** Set up welfare categories, descriptions, and maximum payout limits.
* **Audit Trail:** Query real-time, system-wide transaction and activity logs (tracks users, actions, timestamps, and IP addresses).
* **System Settings:** Configure virtual welfare fund balances, adjust monthly contribution fees, and perform instant database backups.

### 2. 💰 Treasurer Workspace
* **Financial Ledger:** Record and track monthly contributions.
* **Welfare Disbursements:** Payout approved welfare requests, record references, and log receipts.
* **Receipts & Vouchers:** Generate beautiful, printable receipt vouchers and disbursement slips.
* **Analytics:** Monitor real-time fund health, active members, and transaction logs.

### 3. 🛡️ Welfare Officer Workspace
* **Request Evaluation:** View pending welfare claims, examine circumstances, and download supporting files.
* **Collaborative Timeline:** Post timeline comments and recommendations directly on request threads.
* **Under Review Queue:** Transition verified requests forward for final administrative clearance.

### 4. 👤 Member Workspace
* **Profile Management:** Register accounts and manage personal demographic data.
* **Ledger Statements:** Monitor individual contribution status, pay-in history, and outstanding fees.
* **Claim Submission:** Request welfare payouts, enter circumstances, and upload supporting document attachments (PDF/Images).

---

## 🛠️ Technology Stack & Architecture

* **Core Engine:** Custom Object-Oriented **MVC Router** (`App\Core\App`) mapping clean URIs to logical Controller actions.
* **Database Integration:** Secure **PDO Wrapper** (`App\Core\Database`) using a Singleton pattern to prevent SQL Injections.
* **Session & CSRF Security:** Centralized session-lifetime guards (`App\Core\Session`) verifying roles and validating unique state-modifying CSRF tokens.
* **Premium Design System:** Vanilla CSS (`public/css/style.css`) incorporating custom scrollbars, dark/light theme variables, glassmorphism card panels, and smooth hover micro-animations.
* **Front-End Interfaces:** Custom JS (`public/js/main.js`) with responsive layouts, notification polling, and Chart.js reporting widgets.
* **Dependencies:** Managed using Composer with standard, secure **PHPMailer** integrations for automated transactional emails.

---

## 📋 Directory Structure

```
AidFlow/
├── .htaccess             # Apache URL rewriting rules (directs to index.php)
├── index.php             # Front controller & Autoloader bootstrapping
├── composer.json         # PHPMailer dependency definition
├── config/
│   ├── config.php        # Active local environment configurations
│   ├── config.example.php# Configuration template for deployments
│   └── database.sql      # Database schema and seed data (Verified passwords!)
├── app/
│   ├── Core/             # Engine: Router, PDO, Controller, Session, View, Email
│   ├── Controllers/      # Application controllers (Auth, Welfare, Members, etc.)
│   ├── Models/           # Database access classes (User, Member, Contribution, etc.)
│   └── Views/            # Beautiful view folders (Auth, Layouts, Dashboards, etc.)
└── public/
    ├── css/
    │   └── style.css     # Premium UI styling and Dark Mode variables
    └── js/
        └── main.js       # Dynamic AJAX logic, transitions, and Chart.js graphs
```

---

## 🔌 Quick Deployment Guide

### Prerequisites
* **Web Server:** WAMP, XAMPP, or manual Apache + PHP 8+ and MySQL environment.
* **Composer:** Installed globally for package management.

### Step 1: Clone & Setup Config
1. Move the `AidFlow` project directory to your server's root directory (e.g. `c:/wamp64/www/AidFlow`).
2. Copy `config/config.example.php` to `config/config.php`.
3. Open `config/config.php` and configure your database host, user, password, and optional SMTP settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'aidflow_db');
   ```

### Step 2: Import MySQL Database
1. Open phpMyAdmin or your MySQL CLI.
2. Create a new database:
   ```sql
   CREATE DATABASE `aidflow_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import the SQL file located at: `config/database.sql`.

### Step 3: Configure Apache URL Overrides
Enable Apache's `rewrite_module` (`mod_rewrite`) and ensure `AllowOverride All` is set for your server directory in `httpd.conf` to process the `.htaccess` clean URL parameters:
```apache
<Directory "c:/wamp64/www/">
    AllowOverride All
    Require all granted
</Directory>
```

### Step 4: Access Default Demo Accounts
Open your browser and navigate to `http://localhost/AidFlow/`.
You can sign in with any of the seeded staff credentials (password is **`Password123`** for all):
* **Admin:** `admin` (Email: `admin@aidflow.org`)
* **Treasurer:** `treasurer` (Email: `treasurer@aidflow.org`)
* **Welfare Officer:** `welfare_officer` (Email: `officer@aidflow.org`)

---

## 🔒 Security Features
* **PDO Prepared Statements:** Absolute protection against SQL injection attacks.
* **CSRF Protection:** State-modifying requests require verified tokens matched against session keys.
* **XSS Defenses:** Recursive HTML escaping sanitizes all input and output fields.
* **Role Verification:** Base controller guards restrict workspace access based on session roles.
* **IP-Audited Logs:** Critical events (logins, settings edits, disbursements) log user IDs and IP addresses.


//Database connection error: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)

#0 C:\wamp64\www\Aidflow\app\Core\Database.php(29): App\Core\Database->__construct()
#1 C:\wamp64\www\Aidflow\app\Core\Model.php(8): App\Core\Database::getInstance()
#2 C:\wamp64\www\Aidflow\app\Controllers\AuthController.php(17): App\Core\Model->__construct()
#3 C:\wamp64\www\Aidflow\app\Core\App.php(29): App\Controllers\AuthController->__construct()
#4 C:\wamp64\www\Aidflow\index.php(53): App\Core\App->__construct()
#5 {main}
