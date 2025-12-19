# Al-Khair - Deployment Guide

## Server Update করার পদ্ধতি

### Option 1: pull.php Script (সহজ)
1. `pull.php` file টা server এর root directory তে upload করুন
2. Browser এ যান: `https://your-domain.com/pull.php`
3. Update complete হলে `pull.php` delete করুন

### Option 2: Manual File Upload
নিচের files গুলো FTP/File Manager দিয়ে upload করুন:

**Updated Files:**
- `config/app.php` ✅ (BASE_URL fix)
- `.htaccess` ✅ (Asset loading fix)
- `includes/functions.php`
- `includes/auth.php`
- `dashboard/*.php`
- `api/*.php`
- `assets/**/*`

### Option 3: SSH/Git (যদি SSH access থাকে)
```bash
cd /path/to/your/site
git pull origin main
```

## সমস্যা সমাধান

### 1. CSS/JS Load হচ্ছে না
**সমাধান:** 
- `.htaccess` file update করুন
- `config/app.php` file update করুন
- Browser cache clear করুন (Ctrl+Shift+R)

### 2. Login Page Error
**সমাধান:**
- Installation সম্পূর্ণ করুন প্রথমে
- `install.lock` এবং `config/database.php` থাকতে হবে

### 3. Too Many Redirects
**সমাধান:**
- Browser cookies clear করুন
- Login করুন প্রথমে
- সরাসরি dashboard এ যাবেন না

## Installation Process

1. **Install Page এ যান:**
   ```
   https://your-domain.com/install/index.php
   ```

2. **সব requirements green দেখান** (Step 1)

3. **Database Info দিন** (Step 2):
   - Database Host (সাধারণত `localhost`)
   - Database Name
   - Database Username
   - Database Password

4. **Admin Account তৈরি করুন** (Step 3):
   - Full Name
   - Username
   - Email
   - Password (minimum 8 characters)

5. **Organization Details** (Step 4):
   - Organization Name
   - Address
   - Phone
   - Email

6. **Installation Complete!**
   - Automatically `login.php` এ redirect হবে
   - Admin credentials দিয়ে login করুন

## After Installation

### Login করুন:
```
https://your-domain.com/login.php
```

### Dashboard:
```
https://your-domain.com/dashboard/index.php
```

### Features:
- ✅ Donor Management (full CRUD)
- ✅ Projects Management
- ✅ Reports Generation
- ✅ Settings
- ✅ User Management (Admin only)
- 🔄 Donations (coming soon)
- 🔄 Beneficiaries (coming soon)

## File Permissions

নিচের directories writable হতে হবে:
```
chmod 755 /config
chmod 755 /uploads
chmod 755 /logs
chmod 755 /cache
```

## Security

Installation complete হওয়ার পর:
1. ✅ `pull.php` delete করুন
2. ✅ `debug.php` delete করুন
3. ✅ `.htaccess` HTTPS enable করুন (SSL থাকলে)

## Support

সমস্যা হলে GitHub repository তে issue create করুন।

---
**Version:** 1.0.0  
**Author:** Tansiq Labs  
**License:** MIT
