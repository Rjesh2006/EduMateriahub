

---

# 📚 Study Material App

This is a web-based **Study Material App** built using **Laravel** and **MySQL**, designed for Students, Teachers, Admins, and Super Admins. It provides educational content, quizzes, and dynamic role-based access.

## 🔑 Authentication

- Login system with **4 user roles**:
  - **Student**
  - **Teacher**
  - **Admin**
  - **SuperAdmin**
- Authentication is handled using **Laravel Breeze**.
- **Forgot Password** functionality is included with a secure **reset link** sent to the user's email.

## 🔐 Role & Permission System

- Roles and their permissions are **dynamic**.
- Admins can manage roles and assign specific permissions to each user type.

## 📚 Student Features

Students can:
- View and download:
  - **Previous Year Papers (PYQs)**
  - **Study Materials**
  - **Syllabus**
  - **Roadmaps**
- **Attempt quizzes/tests** created by teachers.

## 🧪 Quiz & Test Module

- **Teachers** can:
  - Create and manage quizzes.
  - Monitor student reports.
  - Export reports to **Excel and PDF**.

- **Students** can:
  - Attempt quizzes.
  - If a student **changes tab or tries to cheat**, the test is:
    - **Auto-submitted**
    - A report is **auto-generated**.

## 📊 Reports

- After the quiz:
  - System shows:
    - Number of attempted questions.
    - Number of correct & wrong answers.
  - **Teachers** can:
    - View reports of all students.
    - Export reports in **Excel or PDF** format.

## 🛠️ Technologies Used

- **Laravel** (Backend)
- **MySQL** (Database)
- **Laravel Breeze** (Authentication)
- Email system for **Forgot Password**

## 📎 Installation (for developers)

```bash
git clone <your-repo-url>
cd study-material-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run dev
php artisan serve
```

## 📸 Screenshots

### 📚 SuperAdmin Dashboard
![Superadmin Dashboard](public/assets/screenshots/superadmin_dashboard.png)

### 🔐 Login Page
![Login Page](public/assets/screenshots/login.png)


### 📚 Student Dashboard
![Student Dashboard](public/assets/screenshots/student_dashboard.png)

### 🧪 Quiz Interface
![Quiz Interface](public/assets/screenshots/quiz.png)

### 📊 Report Page
![Report Page](public/assets/screenshots/report.png)

## 🙋‍♂️ Contact

For any queries, contact the developer: **Prashant Kumar, Dwaipyan Singha**

---
