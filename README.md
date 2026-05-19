# JobSure

# JobSure - Job Portal System

A full-stack Job Portal web application built using PHP and MySQL.

## 🚀 Features

- User Registration & Login
- Admin Dashboard
- Job Posting System
- Resume Upload
- OTP Verification
- Password Reset System
- Assessment & Result Module
- Internship & Job Listings
- Bookmark Jobs
- Certificate Generation

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap
- PHPMailer

## 📂 Project Structure

```bash
CAPSTONE1/
│
├── PHPMailer/
├── uploads/
├── admin_dashboard.php
├── login.php
├── register.php
├── db_config.php
├── jobsure_db.sql
└── ...
```

## ⚙️ Installation Steps

1. Clone the repository

```bash
git clone https://github.com/HimanshuPandey03/JobSure.git
```

2. Move project to XAMPP htdocs folder

3. Start Apache and MySQL from XAMPP

4. Import database

- Open phpMyAdmin
- Create database:
  
```text
job
```

- Import:

```text
jobsure_db.sql
```

5. Run project

```text
http://localhost/capstone1
```

---

## 🔐 Default Database Configuration

Update `db_config.php` according to your local database settings.

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job";
```

---

## 📸 Screenshots

(Add screenshots here later)

---

## 👨‍💻 Author

Himanshu Pandey

GitHub:
https://github.com/HimanshuPandey03

---

## 📄 License

This project is for educational purposes.
