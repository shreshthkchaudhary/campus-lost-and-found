# 🎓 Campus Lost & Found Management System

> **A centralized, production-ready web portal designed to streamline lost & found item reporting, real-time searching, and verified recovery across university campuses.**

[![Live Demo](https://img.shields.io/badge/Demo-Live%20Website-brightgreen?style=for-the-badge&logo=google-chrome)](http://krmangalam-lostfound.infinityfreeapp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-InnoDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla%20ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![CSS3](https://img.shields.io/badge/CSS3-Modern%20UI-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![Security Hardened](https://img.shields.io/badge/Security-RCE%20%26%20SQLi%20Protected-success?style=for-the-badge&logo=shield)](https://owasp.org/)

---

## 📌 About The Project

On large university campuses, thousands of belongings (student IDs, lab equipment, laptops, wallets, notebooks, keys) are misplaced or found every semester. Traditional recovery channels—such as WhatsApp broadcast groups, pinned posters, and informal physical inquiries—are scattered, slow, and lack verification.

**Campus Lost & Found Management System** is a modern, full-stack digital solution built for **K.R. Mangalam University (KRMU)** to digitize and automate this workflow:
- **Instant Item Reporting:** Dedicated forms for both *Lost* and *Found* entries with categories, building/lab locations, date constraints, and image uploads.
- **Debounced Live Search:** Fast client-side filtering combined with backend pagination and full-text keyword indexing.
- **Secure Authentication & RBAC:** Role-Based Access Control distinguishing regular university students/staff from administrative moderators.
- **Admin Governance Dashboard:** Real-time system analytics, item status management (Active / Claimed / Recovered), and moderation tools.
- **Defense-in-Depth Security:** Server-side MIME validation, cryptographically random file hashing, `.htaccess` upload execution denial, and parameterized PDO queries.

---

## 👥 Academic Credits & Team Division

* **Institution:** School of Engineering & Technology (SOET), K.R. Mangalam University
* **Course & Section:** B.Tech Computer Science & Engineering (CSE-D), Section 26E1152
* **Project Supervisor:** **Mr. Anurag Singh** (*Department of Computer Science & Engineering*)

### Authors & Responsibilities

```
                                  Team Code Nemesis
                     ┌────────────────────┴────────────────────┐
                     ▼                                         ▼
        Shreshth Kumar Chaudhary                        Shreyansh Kumar
         Roll No: 2501010080                         Roll No: 2501010019
      [ Frontend Lead & UI/UX ]                   [ Backend & DB Architect ]
```

| Member | Roll Number | Key Areas of Responsibility |
|---|---|---|
| **Shreshth Kumar Chaudhary** | `2501010080` | • Single-Page Application (SPA) architecture & tab routing (`index.html`)<br>• Real-time search debounce & filter controls (`app.js`)<br>• Modern university branding, responsive flex/grid layouts & modal dialogs (`styles.css`)<br>• Client-side form validation, toast notifications & keyboard accessibility (`Esc` navigation) |
| **Shreyansh Kumar** | `2501010019` | • RESTful API design & modular endpoint architecture (`/api/*.php`)<br>• Relational database schema, indexing strategy & seed data (`schema.sql`)<br>• Secure image upload pipeline & `.htaccess` upload directory hardening<br>• Role-Based Access Control (RBAC), session management & Admin Dashboard (`admin.php`) |

---

## 🌟 Key Features

### 👤 Student / Faculty Portal
* **SPA Dynamic Switching:** Smooth client-side navigation across Home, Browse Items, Report Lost, Report Found, and About without page reload.
* **Smart Filter Matrix:** Filter simultaneously by Item Type (*All / Lost / Found*), Category (*Electronics, ID Cards, Bags, Books, Keys, etc.*), and Campus Location (*Library, Cafeteria, Blocks A/B/C, Parking, etc.*).
* **Direct Handover Coordination:** Generates pre-populated `mailto:` handover requests to reach reporters securely.
* **Image Uploads with Live Previews:** Instant client-side picture preview before upload with 5MB file-size limits.
* **Resolution Tracking:** One-click recovery confirmation to mark items as *Resolved* once handed over.

### 🛡️ Administrative Dashboard (`admin.php`)
* **Real-Time KPIs:** Live metric cards showing Total Users, Total Items, Active Reports, Resolved Count, and Recent Items (last 7 days).
* **Item Moderation:** Moderation grid to inspect, resolve, or permanently delete items with cascade image cleanup.
* **User Directory:** Full listing of registered students and staff with their ID numbers, registration dates, and post counts.

---

## 🛠️ Technology Stack

| Layer | Technologies Used |
|---|---|
| **Frontend** | HTML5, CSS3 (Custom Variables, Flexbox, CSS Grid), Vanilla JavaScript (ES6+ Asynchronous `fetch()` API) |
| **Backend** | PHP 8.2 (Modular RESTful API architecture, Singleton PDO connection) |
| **Database** | MySQL / MariaDB (InnoDB engine, Full-Text indexes, Foreign Key constraints) |
| **Web Server** | Apache (mod_rewrite, .htaccess security) / PHP Built-in Server |
| **Deployment** | InfinityFree Cloud Hosting (Production) & XAMPP (Local Development) |

---

## 🔒 Security Implementations

1. **Arbitrary File Upload & RCE Protection:**
   - Uploaded files are strictly validated server-side using `finfo_file(FILEINFO_MIME_TYPE)` and `@getimagesize()`. Client-supplied MIME headers and original file extensions are ignored.
   - Files are stored with cryptographically secure random names (`bin2hex(random_bytes(16))`) mapped to an explicit image extension whitelist (`jpg`, `png`, `gif`, `webp`).
2. **Upload Directory Execution Shield:**
   - The `uploads/` directory includes an Apache `.htaccess` rule that turns off the PHP engine (`php_flag engine off`) and denies requests for `.php`, `.phtml`, `.cgi`, `.py`, `.sh`, `.exe`, or script files.
3. **Path Traversal Defense:**
   - File deletion in `delete_item.php` uses canonical `realpath()` validation to ensure files can only be removed from within the `uploads/` boundary.
4. **SQL Injection Defense:**
   - All queries use PDO prepared statements with bound parameters.
   - Dynamic `LIMIT` and `OFFSET` clauses are cast to bounded integers to satisfy strict MySQL drivers with disabled prepare-emulation.
5. **Cross-Site Scripting (XSS) Mitigation:**
   - Client-side DOM injection uses dedicated HTML entity sanitizers.
   - User passwords are encrypted with one-way `PASSWORD_BCRYPT` hashing.

---

## 📁 Repository Structure

```plaintext
├── api/                             # Modular REST API Endpoints
│   ├── add_item.php                 # POST: Create report with image upload
│   ├── admin_stats.php              # GET: Admin metric counters (admin only)
│   ├── admin_users.php              # GET: User directory listing (admin only)
│   ├── delete_item.php              # POST: Delete item & associated file (admin only)
│   ├── items.php                    # GET: Filtered & paginated item search
│   ├── login.php                    # POST: User authentication & session init
│   ├── logout.php                   # POST: Destroy session
│   ├── register.php                 # POST: New student/faculty account creation
│   ├── resolve_item.php             # POST: Mark item claimed/recovered
│   └── session.php                  # GET: Active session verification
│
├── config/                          # Configuration & Helpers
│   └── database.php                 # PDO singleton, auto-environment DB detection, auth guards
│
├── sql/                             # Database Schemas
│   ├── schema.sql                   # Full schema with local database creation
│   └── schema_infinityfree.sql      # Cloud-ready schema for shared hosting (tables & seeds)
│
├── uploads/                         # User Uploaded Photos
│   ├── .htaccess                    # Security policy: blocks script/PHP execution
│   └── index.php                    # 403 Forbidden directory guard
│
├── admin.css                        # Admin dashboard styles
├── admin.php                        # Protected Admin Dashboard
├── app.js                           # Frontend controller (state, DOM, API requests)
├── index.html                       # Main user portal SPA
├── styles.css                       # Application branding & layout styles
└── README.md                        # Project documentation
```

---

## 🚀 Local Installation & Setup

### Prerequisites
* [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x) or standalone PHP & MySQL.
* Git installed on your system.

### Quick Start Guide

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/<your-username>/<repo-name>.git
   cd <repo-name>
   ```

2. **Start MySQL & Apache:**
   * Open the **XAMPP Control Panel** and start both **Apache** and **MySQL**.

3. **Import Database:**
   * Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or use terminal:
     ```bash
     mysql -u root -p < sql/schema.sql
     ```
   * This creates the `campus_lost_found` database, tables, indexes, and pre-seeded demo records.

4. **Run the Application:**
   * **Via XAMPP Apache:**
     Move the folder to `C:\xampp\htdocs\` and open:  
     `http://localhost/<repo-name>/index.html`
   * **Via PHP Built-in Server:**
     ```bash
     php -S localhost:8000
     ```
     Then open: `http://localhost:8000/index.html`

---

## 🔑 Demo Credentials

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Administrator** | `admin@krmangalam.edu.in` | `admin123` | Full Access & Admin Dashboard (`admin.php`) |
| **Student User** | `rk@student.krmangalam.edu.in` | `password123` | Browse, Report, Claim & Resolve |
| **Student User** | `ms@student.krmangalam.edu.in` | `password123` | Browse, Report, Claim & Resolve |

*(New user accounts can also be registered directly using the **Register** tab in the login dialog).*

---

## 🌐 Live Cloud Deployment

* **Live URL:** [http://krmangalam-lostfound.infinityfreeapp.com](http://krmangalam-lostfound.infinityfreeapp.com)
* **Admin Portal:** [http://krmangalam-lostfound.infinityfreeapp.com/admin.php](http://krmangalam-lostfound.infinityfreeapp.com/admin.php)

---

## 📄 License

Developed for academic presentation and submission at **K.R. Mangalam University**.  
Copyright © 2026 **Shreshth Kumar Chaudhary** & **Shreyansh Kumar** (Code Nemesis).
