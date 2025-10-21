# 📋 TvScreen Project Hub - Project Management System

<div align="center">

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap)
![License](https://img.shields.io/badge/license-MIT-green.svg)

**A comprehensive project management system for Mascot and Costume departments with real-time tracking and priority management.**

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Documentation](#-documentation)

</div>

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Configuration](#-configuration)
- [Project Structure](#-project-structure)
- [User Roles & Permissions](#-user-roles--permissions)
- [Usage Guide](#-usage-guide)
- [API Endpoints](#-api-endpoints)
- [Screenshots](#-screenshots)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

---

## 🎯 Overview

**TvScreen Project Hub** adalah sistem manajemen proyek berbasis web yang dirancang khusus untuk mengelola proyek-proyek di departemen **Mascot** dan **Costume**. Sistem ini memungkinkan admin untuk melacak status proyek, prioritas, deadline, serta mengelola gambar dan dokumentasi proyek dengan mudah.

### Key Highlights:

- ✅ Multi-department management (Mascot & Costume)
- ✅ Real-time project tracking
- ✅ Priority-based filtering
- ✅ Image gallery with Fancybox integration
- ✅ Responsive design for all devices
- ✅ Google Slides integration for presentations
- ✅ Advanced search and filtering

---

## ✨ Features

### 🎭 Department Management

- **Mascot Department**: Manage mascot projects with specialized workflow
  - Type classification: Compressed Foam, Inflatable, Props, Statue
  - Status tracking: Upcoming, In Progress, Revision, Completed
- **Costume Department**: Manage costume projects with tailored features
  - Status tracking: Sample, In Progress, Revision, Completed

### 📊 Project Management

- **CRUD Operations**: Create, Read, Update, Delete projects
- **Multiple Image Upload**: Support untuk upload multiple images per project
- **Image Gallery**: View images dengan Fancybox lightbox
- **Google Slides Integration**: Embed presentation links
- **Priority Levels**: Urgent, High, Normal, Low
- **Deadline Tracking**: Visual indicators untuk deadline yang mendekat
- **This Week Filter**: Quick filter untuk proyek dengan deadline minggu ini

### 🔍 Search & Filter

- **Real-time Search**: Cari project berdasarkan nama
- **Status Filter**: Filter berdasarkan status proyek
- **Priority Filter**: Filter berdasarkan tingkat prioritas
- **Type Filter** (Mascot only): Filter berdasarkan tipe mascot
- **Combined Filters**: Kombinasi multiple filters

### 👥 User Management

- **Role-based Access Control**: Mascot Admin & Costume Admin
- **Secure Authentication**: Password hashing dengan PHP
- **Session Management**: Secure session handling
- **Unauthorized Access Protection**: Automatic redirect untuk unauthorized users

### 📱 UI/UX Features

- **Responsive Design**: Mobile-first approach
- **Dark Mode Support**: Toggle between light and dark themes
- **Real-time Clock**: Display waktu real-time
- **Pagination**: Efficient data loading dengan pagination
- **DataTables Integration**: Sortable dan searchable tables
- **SweetAlert2**: Beautiful alert notifications
- **Floating Action Buttons**: Quick access ke helpdesk
- **Scroll to Top**: Smooth scroll navigation

---

## 🛠 Tech Stack

### Backend

- **PHP** 8.1+ - Server-side scripting
- **MySQL** 8.0+ - Database management
- **PDO** - Database abstraction layer
- **Composer** - Dependency management

### Frontend

- **HTML5** - Markup
- **CSS3** - Styling with modern features
- **JavaScript** (ES6+) - Client-side logic
- **Bootstrap** 5.3 - UI framework
- **jQuery** 3.6+ - DOM manipulation
- **Bootstrap Icons** - Icon library

### Libraries & Plugins

- **Carbon** 3.8+ - DateTime manipulation
- **DataTables** 1.13+ - Advanced table features
- **Fancybox** 5.0 - Lightbox gallery
- **SweetAlert2** 11+ - Beautiful alerts
- **Bootstrap Icons** 1.11+ - Icon set

---

## 💻 System Requirements

### Minimum Requirements:

```plaintext
- PHP >= 8.1
- MySQL >= 8.0 / MariaDB >= 10.4
- Apache 2.4+ / Nginx 1.18+
- Composer 2.0+
- Web Browser: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
```

### Recommended Server Configuration:

```ini
; php.ini settings
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
max_input_time = 300
```

### Required PHP Extensions:

```plaintext
- ext-json
- ext-mbstring
- ext-pdo
- ext-pdo_mysql
- ext-mysqli
```

---

## 📥 Installation

### 1. Clone Repository

```bash
# Clone project
git clone https://github.com/itskulen/bigscreenproject.git

# Masuk ke direktori project
cd bigscreenproject
```

### 2. Install Dependencies

```bash
# Install PHP dependencies via Composer
composer install
```

### 3. Setup Web Server

#### Option A: Laragon (Recommended for Windows)

```plaintext
1. Install Laragon dari https://laragon.org/
2. Pindahkan folder project ke: C:\laragon\www\bigscreenproject
3. Start Laragon
4. Akses: http://bigscreenproject.test
```

#### Option B: XAMPP

```plaintext
1. Install XAMPP dari https://www.apachefriends.org/
2. Pindahkan folder project ke: C:\xampp\htdocs\bigscreenproject
3. Start Apache & MySQL dari XAMPP Control Panel
4. Akses: http://localhost/bigscreenproject
```

#### Option C: Manual Apache/Nginx Configuration

```apache
# Apache Virtual Host
<VirtualHost *:80>
    ServerName bigscreenproject.local
    DocumentRoot "C:/path/to/bigscreenproject"

    <Directory "C:/path/to/bigscreenproject">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 🗄 Database Setup

### 1. Create Database

```sql
-- Buat database baru
CREATE DATABASE tv_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### 2. Import Database Schema

```bash
# Import schema SQL
mysql -u root -p tv_db < tv_db.sql
```

### 3. Verify Tables

```sql
-- Check tables
USE tv_db;
SHOW TABLES;

-- Expected output:
-- +------------------+
-- | Tables_in_tv_db  |
-- +------------------+
-- | gallery          |
-- | users            |
-- +------------------+
```

### 4. Create Initial Users

```bash
# Run seeder script
php seed_users.php
```

**Default User Credentials:**

```plaintext
Mascot Admin:
  Username: mascot_admin
  Password: mascot123

Costume Admin:
  Username: costume_admin
  Password: costume123
```

> ⚠️ **Security Note**: Segera ganti password default setelah first login!

---

## ⚙ Configuration

### 1. Database Configuration

Edit file [`db.php`](db.php):

```php
// filepath: c:\laragon\www\bigscreenproject\db.php
<?php
$host = 'localhost';          // Database host
$dbname = 'tv_db';           // Database name
$username = 'root';          // Database username
$password = '';              // Database password (kosong untuk XAMPP/Laragon)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('DB Error: ' . $e->getMessage());
}
?>
```

### 2. Upload Directory Permissions

```bash
# Windows (Command Prompt as Admin)
icacls "uploads" /grant Everyone:F /T

# Linux/Mac
chmod -R 755 uploads/
```

### 3. Environment Setup

**Production Settings** (`.htaccess`):

```apache
# Security headers
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"

# Error reporting (disable di production)
php_flag display_errors Off
```

**Development Settings**:

```php
// Enable error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📁 Project Structure

```plaintext
bigscreenproject/
│
├── 📂 uploads/                   # Upload directory
│   ├── 📂 projects/              # Project images
│   └── 📂 materials/             # Material/notes images
│
├── 📂 vendor/                    # Composer dependencies
│   ├── 📂 nesbot/carbon/         # DateTime library
│   ├── 📂 symfony/               # Symfony components
│   └── autoload.php              # Composer autoloader
│
├── 📄 index.php                  # Landing page / Hub
├── 📄 login.php                  # Authentication page
├── 📄 logout.php                 # Logout handler
│
├── 📂 Mascot Department/
│   ├── 📄 mascot_index.php       # Public project list
│   ├── 📄 mascot_admin.php       # Admin dashboard
│   ├── 📄 mascot_upload.php      # Upload handler
│   ├── 📄 mascot_update.php      # Update handler
│   ├── 📄 mascot_edit.php        # Edit page
│   └── 📄 mascot_delete.php      # Delete handler
│
├── 📂 Costume Department/
│   ├── 📄 costume_index.php      # Public project list
│   ├── 📄 costume_admin.php      # Admin dashboard
│   ├── 📄 costume_upload.php     # Upload handler
│   ├── 📄 costume_update.php     # Update handler
│   ├── 📄 costume_edit.php       # Edit page
│   └── 📄 costume_delete.php     # Delete handler
│
├── 📂 Utilities/
│   ├── 📄 db.php                 # Database connection
│   ├── 📄 config.php             # Configuration
│   ├── 📄 middleware.php         # Auth middleware
│   ├── 📄 error_handler.php      # Error handling
│   ├── 📄 image_helper.php       # Image utilities
│   ├── 📄 update_status.php      # AJAX status updater
│   └── 📄 update_priority.php    # AJAX priority updater
│
├── 📂 Error Pages/
│   ├── 📄 404.php                # Not Found
│   ├── 📄 500.php                # Server Error
│   └── 📄 unauthorized.php       # Access Denied
│
├── 📂 Database/
│   ├── 📄 tv_db.sql              # Database schema
│   ├── 📄 database_priority_update.sql  # Migration script
│   └── 📄 seed_users.php         # User seeder
│
├── 📂 Assets/
│   └── 📄 favicon.ico            # Site favicon
│
├── 📂 Config Files/
│   ├── 📄 .gitignore             # Git ignore rules
│   ├── 📄 composer.json          # Composer dependencies
│   └── 📄 composer.lock          # Dependency lock file
│
└── 📄 README.md                  # This file
```

---

## 👤 User Roles & Permissions

### Role Matrix

| Feature                 | Mascot Admin | Costume Admin | Public User |
| ----------------------- | ------------ | ------------- | ----------- |
| View Projects           | ✅           | ✅            | ✅          |
| Create Project          | ✅ (Mascot)  | ✅ (Costume)  | ❌          |
| Edit Project            | ✅ (Mascot)  | ✅ (Costume)  | ❌          |
| Delete Project          | ✅ (Mascot)  | ✅ (Costume)  | ❌          |
| Update Status           | ✅ (Mascot)  | ✅ (Costume)  | ❌          |
| Update Priority         | ✅ (Mascot)  | ✅ (Costume)  | ❌          |
| Access Admin Panel      | ✅ (Mascot)  | ✅ (Costume)  | ❌          |
| Cross-Department Access | ❌           | ❌            | ❌          |

### Permission Enforcement

**Middleware Implementation** ([`middleware.php`](middleware.php)):

```php
<?php
function checkUserRole($requiredRole) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    if ($_SESSION['role'] !== $requiredRole) {
        ErrorHandler::handle403("You don't have permission");
    }
}
?>
```

---

## 📖 Usage Guide

### For Public Users

#### 1. View Projects

```plaintext
1. Navigate to homepage: http://yoursite.com
2. Pilih department: Mascot atau Costume
3. Browse project list
4. Use search & filters untuk menemukan project
5. Click project card untuk detail
6. Click image untuk view gallery
```

#### 2. Filter Projects

**By Status:**

- Click status button (Upcoming, In Progress, Revision, Completed)
- Projects akan di-filter real-time

**By Priority:**

- Click priority button (Urgent, High, Normal, Low)
- Visual indicators: Red (Urgent), Yellow (High), Blue (Normal), Gray (Low)

**By This Week:**

- Click "This Week" button untuk melihat projects dengan deadline minggu ini
- Projects dengan deadline mendekat akan diberi visual indicator

**Combined Filters:**

- Combine status + priority filters untuk pencarian lebih spesifik
- Click "Reset Filters" untuk clear all filters

#### 3. Search Projects

```plaintext
1. Use search bar di top
2. Type project name
3. Results update real-time
4. Case-insensitive search
```

---

### For Admin Users

#### 1. Login

```plaintext
1. Navigate to: http://yoursite.com/login.php
2. Enter credentials:
   - Mascot Admin: mascot_admin / mascot123
   - Costume Admin: costume_admin / costume123
3. Click "Login"
4. Redirect to respective admin panel
```

#### 2. Create New Project

**Step-by-Step:**

```plaintext
1. Login ke admin panel
2. Scroll ke "Add New Project" form
3. Fill required fields:
   ✓ Project Name (required)
   ✓ Status (required)
   ✓ Priority (required)
   ✓ Quantity (required, minimum 1)

4. Fill optional fields:
   ○ Deadline
   ○ Description
   ○ Google Slide URL
   ○ Type (Mascot only)

5. Upload images:
   - Project Images: Click drop zone atau drag & drop
   - Material/Notes Images: Click drop zone atau drag & drop
   - Support multiple images (JPG, PNG, GIF)
   - Max file size: 10MB per file

6. Preview images sebelum upload
7. Click "Upload Project"
8. Success notification akan muncul
```

**Validation Rules:**

```javascript
- Project Name: Max 255 characters
- Quantity: Positive integer
- Deadline: YYYY-MM-DD format
- Google Slide URL: Valid URL format
- Images: JPG, PNG, GIF (max 10MB each)
```

#### 3. Edit Existing Project

```plaintext
1. Di admin panel, scroll ke project table
2. Click "Edit" button pada project yang ingin di-edit
3. Edit form akan terbuka
4. Modify fields yang diperlukan:
   - Text fields: langsung edit
   - Images: upload baru akan replace yang lama
   - Status/Priority: pilih dari dropdown
5. Preview perubahan
6. Click "Update Project"
7. Confirmation notification
```

#### 4. Update Status/Priority (Quick Update)

**Real-time Update via Dropdown:**

```plaintext
1. Di project table
2. Find project yang ingin di-update
3. Click dropdown di kolom Status/Priority
4. Select new value
5. Automatic AJAX update
6. Success notification
7. Page doesn't reload (seamless UX)
```

**Available Status Values:**

**Mascot Department:**

- Upcoming
- In Progress
- Revision
- Completed
- Archived

**Costume Department:**

- Sample
- In Progress
- Revision
- Completed
- Archived

**Priority Levels (Both Departments):**

- Urgent (Red indicator)
- High (Yellow indicator)
- Normal (Blue indicator)
- Low (Gray indicator)

#### 5. Delete Project

```plaintext
1. Di project table
2. Click "Delete" button
3. Confirmation dialog muncul
4. Confirm deletion
5. Project dan semua images akan dihapus
6. Success notification
```

> ⚠️ **Warning**: Deletion is permanent and cannot be undone!

#### 6. View Google Slides

```plaintext
1. Projects dengan Google Slide link memiliki icon
2. Click "View Slides" button
3. Modal popup dengan embedded presentation
4. Full-screen view available
5. Close modal when done
```

#### 7. Manage Images

**View Images:**

```plaintext
1. Click thumbnail image di project card
2. Fancybox gallery opens
3. Navigate dengan arrow keys atau buttons
4. Zoom in/out dengan scroll
5. Swipe untuk navigate (mobile)
6. ESC untuk close
```

**Upload Multiple Images:**

```plaintext
1. Saat create/edit project
2. Click "Add More" untuk multiple files
3. Atau drag & drop multiple files sekaligus
4. Preview all images sebelum submit
5. Remove individual images sebelum upload
```

**Image Format Support:**

```plaintext
✓ JPEG/JPG
✓ PNG
✓ GIF
✓ Maximum size: 10MB per file
✓ Recommended resolution: 1920x1080px atau lebih kecil
```

---

## 🔌 API Endpoints

### Authentication

#### POST `/login.php`

**Description**: Authenticate user

**Request:**

```http
POST /login.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

username=mascot_admin&password=mascot123
```

**Response (Success):**

```php
// Redirect to mascot_admin.php or costume_admin.php
Session started with:
$_SESSION['logged_in'] = true
$_SESSION['username'] = 'mascot_admin'
$_SESSION['role'] = 'mascot'
```

**Response (Error):**

```php
// Redirect back to login.php with error
$_SESSION['login_error'] = 'Invalid username or password!'
```

---

### Project Management

#### POST `/mascot_upload.php`

**Description**: Create new mascot project

**Request:**

```http
POST /mascot_upload.php HTTP/1.1
Content-Type: multipart/form-data

project_name=Test Project
project_status=In Progress
priority=High
quantity=5
description=Test description
deadline=2025-12-31
type=Compressed Foam
subform_embed=https://docs.google.com/presentation/...
project_image[]=file1.jpg
material_image[]=file2.png
```

**Response (Success):**

```php
$_SESSION['message'] = 'Project successfully uploaded!'
$_SESSION['message_type'] = 'success'
// Redirect to mascot_admin.php
```

**Response (Error):**

```php
$_SESSION['message'] = 'Error message here'
$_SESSION['message_type'] = 'danger'
// Redirect to mascot_admin.php
```

---

#### POST `/update_status.php`

**Description**: Update project status (AJAX)

**Request:**

```http
POST /update_status.php HTTP/1.1
Content-Type: application/json

{
  "id": 123,
  "status": "Completed",
  "category": "mascot"
}
```

**Response:**

```json
{
  "success": true
}
```

---

#### POST `/update_priority.php`

**Description**: Update project priority (AJAX)

**Request:**

```http
POST /update_priority.php HTTP/1.1
Content-Type: application/json

{
  "id": 123,
  "priority": "Urgent"
}
```

**Response:**

```json
{
  "success": true
}
```

---

#### GET `/mascot_edit.php?id=123`

**Description**: Display edit form

**Response**: HTML edit form with pre-filled data

---

#### POST `/mascot_update.php`

**Description**: Update existing project

**Request**: Same as upload, plus `id` field

**Response**: SweetAlert notification + redirect

---

#### GET `/mascot_delete.php?id=123`

**Description**: Delete project

**Response:**

```php
$_SESSION['message'] = 'Project successfully deleted!'
$_SESSION['message_type'] = 'success'
// Redirect to mascot_admin.php
```

---

### Public Views

#### GET `/mascot_index.php`

**Query Parameters:**

```plaintext
?search=keyword              # Search by project name
&project_status=In%20Progress # Filter by status
&priority=Urgent             # Filter by priority
&type=Inflatable            # Filter by type (mascot only)
&this_week=1                # Filter this week's deadlines
&page=2                     # Pagination
```

**Example:**

```http
GET /mascot_index.php?priority=Urgent&this_week=1&page=1
```

---

## 📸 Screenshots

### Homepage

```
┌───────────────────────────────────────┐
│   TvScreen Project Hub                │
│   Choose your department:             │
│                                       │
│   [Mascot Projects] [Costume Projects]│
│                                       │
│   [Admin Login]                       │
└───────────────────────────────────────┘
```

### Project List (Public View)

```
┌────────────────────────────────────────┐
│ 🔍 Search: [____________] [🔎]        │
│                                        │
│ Filters:                               │
│ Status: [All] [Upcoming] [In Progress] │
│ Priority: [All] [Urgent] [High]        │
│ [This Week: 5]                         │
│                                        │
│ ┌───────┐ ┌───────┐ ┌───────┐          │
│ │ Proj1 │ │ Proj2 │ │ Proj3 │          │
│ │ [img] │ │ [img] │ │ [img] │          │
│ │Status │ │Status │ │Status │          │
│ └───────┘ └───────┘ └───────┘          │
└────────────────────────────────────────┘
```

### Admin Dashboard

```
┌────────────────────────────────────────┐
│ Add New Project                        │
│ ┌─────────────────────────────────┐    │
│ │ Project Name: [_____________]   │    │
│ │ Status: [Dropdown ▼]            │    │
│ │ Priority: [Dropdown ▼]          │    │
│ │ Upload Images: [Drop Zone]      │    │
│ │         [Upload Project]        │    │
│ └─────────────────────────────────┘    │
│                                        │
│ Project List                           │
│ ┌────────────────────────────────────┐ │
│ │ Name │ Status │ Priority │ Actions │ │
│ │ Proj1│[▼]    │[▼]      │[Edit][Del]│ │
│ │ Proj2│[▼]    │[▼]      │[Edit][Del]│ │
│ └────────────────────────────────────┘ │
└────────────────────────────────────────┘
```

---

## 🐛 Troubleshooting

### Common Issues & Solutions

#### Issue 1: "Database connection failed"

**Symptoms:**

```
DB Error: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
```

**Solutions:**

```plaintext
1. Check database credentials di db.php
2. Verify MySQL service is running
3. Test connection:
   mysql -u root -p
4. Reset MySQL password if needed
```

---

#### Issue 2: "Upload directory not writable"

**Symptoms:**

```
Warning: move_uploaded_file(): Failed to open stream
```

**Solutions:**

**Windows:**

```cmd
icacls "uploads" /grant Everyone:F /T
```

**Linux/Mac:**

```bash
chmod -R 755 uploads/
chown -R www-data:www-data uploads/
```

---

#### Issue 3: "Composer dependencies not found"

**Symptoms:**

```
Fatal error: require(): Failed opening 'vendor/autoload.php'
```

**Solution:**

```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

---

#### Issue 4: "Session not working"

**Symptoms:**

- Auto-logout setelah redirect
- Login berhasil tapi kembali ke login page

**Solutions:**

```php
// Check php.ini:
session.save_path = "C:\laragon\tmp"  # Windows
session.save_path = "/tmp"            # Linux/Mac

// Verify directory exists and writable
```

---

#### Issue 5: "Images not displaying"

**Symptoms:**

- Broken image icons
- 404 errors untuk image paths

**Solutions:**

```plaintext
1. Check file exists di uploads/ folder
2. Verify file permissions (755)
3. Check .htaccess tidak block image access
4. Clear browser cache
5. Inspect Network tab di DevTools
```

---

#### Issue 6: "DataTables not initializing"

**Symptoms:**

- Table tidak sortable
- Search box tidak muncul
- Console error: "$ is not defined"

**Solutions:**

```html
<!-- Pastikan jQuery dimuat sebelum DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<!-- Check console untuk errors -->
Press F12 > Console tab
```

---

#### Issue 7: "AJAX update not working"

**Symptoms:**

- Dropdown changes tidak tersimpan
- No success notification

**Solutions:**

```javascript
// Check browser console for errors
// Verify AJAX endpoint returns JSON
// Check Content-Type header

fetch('/update_status.php', {
  headers: {
    'Content-Type': 'application/json',
  },
})
  .then((res) => res.json())
  .then((data) => console.log(data));
```

---

### Debug Mode

**Enable Detailed Errors:**

```php
// Add to top of index.php for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
```

**Check PHP Error Log:**

```bash
# Linux/Mac
tail -f /var/log/apache2/error.log

# Windows (Laragon)
C:\laragon\www\bigscreenproject\error.log
```

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### Development Workflow

1. **Fork the repository**

```bash
git clone https://github.com/itskulen/bigscreenproject.git
cd bigscreenproject
```

2. **Create feature branch**

```bash
git checkout -b feature/amazing-feature
```

3. **Make changes**

```plaintext
- Follow coding standards
- Add comments untuk complex logic
- Test thoroughly
```

4. **Commit changes**

```bash
git add .
git commit -m "Add: Amazing feature description"
```

5. **Push to branch**

```bash
git push origin feature/amazing-feature
```

6. **Open Pull Request**

```plaintext
- Describe changes clearly
- Reference issues if applicable
- Wait for review
```

### Coding Standards

**PHP:**

```php
// PSR-12 Extended Coding Style
<?php
namespace App;

class ExampleClass
{
    public function exampleMethod(string $param): void
    {
        // Use camelCase for methods
        // Use snake_case for database columns
        // Add type hints
        // Document with PHPDoc
    }
}
```

**JavaScript:**

```javascript
// ES6+ syntax
const functionName = (param) => {
  // Use const/let, not var
  // Use arrow functions
  // Use template literals
  // Add JSDoc comments
};
```

**CSS:**

```css
/* BEM Naming Convention */
.block {
  /* Block styles */
}

.block__element {
  /* Element styles */
}

.block--modifier {
  /* Modifier styles */
}
```

---

## 📄 License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2025 IT DCM

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 📞 Contact & Support

### Support Channels

**📧 Email:**

- Technical Support: dcmit55@gmail.com
- General Inquiries: dcmit55@gmail.com

**💬 WhatsApp Helpdesk:**

- +62 877-2198-8393

**🐛 Bug Reports:**

- GitHub Issues: [Create New Issue](https://github.com/itskulen/bigscreenproject/issues)

**📖 Documentation:**

- Wiki: [Project Wiki](https://github.com/itskulen/bigscreenproject/wiki)

### Team

**👨‍💻 Developed by:**

- IT Department - DCM

**🙏 Credits:**

- Bootstrap Team
- Carbon PHP Team
- DataTables Team
- Fancybox Team
- All contributors

---

## 🔄 Changelog

### Version 1.0.0 (January 2025)

```
✨ Initial Release
- Multi-department management (Mascot & Costume)
- User authentication & authorization
- CRUD operations untuk projects
- Image upload & gallery
- Real-time search & filtering
- Priority & status management
- Responsive design
- DataTables integration
- Google Slides embedding
```

### Planned Features (v1.1.0)

```
🚀 Coming Soon:
- [ ] Export to PDF
- [ ] Email notifications
- [ ] Project timeline view
- [ ] Advanced analytics dashboard
- [ ] File attachment support
- [ ] Comment system
- [ ] Activity logs
- [ ] Mobile app
```

---

## 🎓 Additional Resources

### Tutorials

- [Getting Started Guide](docs/getting-started.md)
- [Admin Tutorial](docs/admin-guide.md)
- [API Documentation](docs/api.md)
- [Deployment Guide](docs/deployment.md)

### External Links

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Manual](https://dev.mysql.com/doc/)
- [Bootstrap Docs](https://getbootstrap.com/docs/)
- [Carbon Docs](https://carbon.nesbot.com/docs/)
- [DataTables Docs](https://datatables.net/manual/)

---

<div align="center">

### ⭐ Star this project if you find it helpful!

**Made with ❤️ by IT DCM**

[Report Bug](https://github.com/itskulen/bigscreenproject/issues) • [Request Feature](https://github.com/itskulen/bigscreenproject/issues) • [Documentation](https://github.com/itskulen/bigscreenproject/wiki)

</div>
