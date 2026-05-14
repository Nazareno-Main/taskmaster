# 📋 TaskMaster — Student Task Manager

**Final Project | Full-Stack Web Application**
Built with Laravel, MySQL, React, PHP, JavaScript, and CSS.

---

## 📌 Project Overview

**Problem Identified:**
Students often struggle to track deadlines across multiple subjects, leading to missed assignments and poor time management.

**Solution:**
TaskMaster is a web-based student planner that allows students to create, view, update, and delete their tasks per subject, with priority levels and status tracking — all in one clean, organized dashboard.

**System Scope:**
- **Users:** Registered students
- **Features:** Authentication, full task CRUD, status toggle, dashboard stats, search & filter by subject/priority/status
- **Limitations:** Single user per account (no shared tasks), no file attachments, no calendar sync

---

## 🛠 Tech Stack

| Layer        | Technology              |
|--------------|-------------------------|
| Frontend     | HTML5, CSS3, JavaScript |
| UI Framework | Custom CSS (no Bootstrap dependency) |
| Reactive UI  | React 18 (via CDN + Babel) |
| Backend      | PHP 8.1 via Laravel 10  |
| Architecture | MVC (Model-View-Controller) |
| Database     | MySQL (via Laravel Eloquent ORM) |
| Templating   | Laravel Blade           |
| Version Control | Git + GitHub         |
| Fonts        | Syne (display), DM Sans (body) |

---

## 🗄 Database Design (3 Normalized Tables)

### Entity-Relationship Diagram (ERD)

```
┌─────────────┐       ┌──────────────────────────────────────┐       ┌─────────────────┐
│   users     │       │              tasks                   │       │   categories    │
│─────────────│       │──────────────────────────────────────│       │─────────────────│
│ id (PK)     │──┐    │ id (PK)                              │  ┌────│ id (PK)         │
│ name        │  └───>│ user_id (FK → users.id)              │  │    │ name            │
│ email       │       │ category_id (FK → categories.id) ───>│──┘    │ color           │
│ password    │       │ title                                │       │ created_at      │
│ created_at  │       │ description                          │       │ updated_at      │
│ updated_at  │       │ due_date                             │       └─────────────────┘
└─────────────│       │ priority (low|medium|high)           │
              │       │ status (pending|in_progress|done)    │
              │       │ created_at                           │
              │       │ updated_at                           │
              │       └──────────────────────────────────────┘
```

**Relationships:**
- `users` → `tasks`: One-to-Many (a student has many tasks)
- `categories` → `tasks`: One-to-Many (a category has many tasks)
- Both use foreign keys with `ON DELETE CASCADE`

---

## 🏗 MVC Architecture

```
routes/web.php
    │
    ├── GET  /tasks         → TaskController@index    (reads from Task model)
    ├── GET  /tasks/create  → TaskController@create   (passes categories to View)
    ├── POST /tasks         → TaskController@store    (validated by TaskRequest)
    ├── GET  /tasks/{id}/edit → TaskController@edit
    ├── PUT  /tasks/{id}    → TaskController@update
    ├── DELETE /tasks/{id}  → TaskController@destroy
    └── PATCH /tasks/{id}/toggle → TaskController@toggle (JSON for React)

app/Models/
    ├── User.php       → hasMany(Task)
    ├── Task.php       → belongsTo(User), belongsTo(Category)
    └── Category.php   → hasMany(Task)

resources/views/
    ├── layouts/app.blade.php    → Master layout (sidebar, CSS, React CDN)
    ├── auth/login.blade.php     → Login form
    ├── auth/register.blade.php  → Registration form
    ├── tasks/index.blade.php    → Dashboard (stats, filters, table, React widget)
    ├── tasks/create.blade.php   → Add task form
    └── tasks/edit.blade.php     → Edit task form (pre-filled)
```

---

## 🌊 Application Flowchart

```
[User visits /]
      │
      ▼
[Not logged in?] ──Yes──> [Login Page]
      │                        │
     No                   [Valid credentials?]
      │                   Yes │    No│
      │                        │      └──> [Show error, back to login]
      ▼                        ▼
[Dashboard /tasks] <─────── [Auth::login()]
      │
      ├─ [View all tasks] ──> SELECT * FROM tasks WHERE user_id = ?
      │
      ├─ [Add Task] ──> [Create Form] ──> [JS Validation]
      │                                        │
      │                               [POST /tasks]
      │                                        │
      │                               [TaskRequest validates]
      │                                        │
      │                               [INSERT INTO tasks]
      │                                        │
      │                               [Redirect to Dashboard]
      │
      ├─ [Edit Task] ──> [Edit Form pre-filled] ──> [PUT /tasks/{id}]
      │                                                    │
      │                                           [UPDATE tasks SET ...]
      │
      ├─ [Delete Task] ──> [JS confirm()] ──> [DELETE /tasks/{id}]
      │                                              │
      │                                    [DELETE FROM tasks WHERE id=?]
      │
      └─ [Toggle Status] ──> [React PATCH /tasks/{id}/toggle]
                                       │
                              [UPDATE tasks SET status=?]
                                       │
                              [Return JSON to React component]
```

---

## 🚀 Setup & Installation

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL (XAMPP, WAMP, Laragon, or native)
- Node.js (optional, only for Vite hot-reload)

### Step-by-Step

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/taskmaster.git
cd taskmaster

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Create the MySQL database
# Open phpMyAdmin or MySQL CLI and run:
#   CREATE DATABASE taskmaster_db;

# 6. Edit .env with your database credentials
#   DB_DATABASE=taskmaster_db
#   DB_USERNAME=root
#   DB_PASSWORD=

# 7. Run migrations (creates the 3 tables)
php artisan migrate

# 8. Seed demo data (categories + demo account)
php artisan db:seed

# 9. Start the development server
php artisan serve
```

Then open **http://localhost:8000** in your browser.

**Demo Account:**
- Email: `demo@taskmaster.com`
- Password: `password`

---

## 📁 Project Structure

```
taskmaster/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      ← Login, register, logout
│   │   │   └── TaskController.php      ← Full CRUD + toggle
│   │   └── Requests/
│   │       └── TaskRequest.php         ← Server-side validation rules
│   └── Models/
│       ├── User.php                    ← User model + relationships
│       ├── Task.php                    ← Task model + helper methods
│       └── Category.php               ← Category model
│
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_categories_table.php
│   │   └── ..._create_tasks_table.php
│   └── seeders/
│       └── DatabaseSeeder.php          ← Demo data
│
├── resources/
│   └── views/
│       ├── layouts/app.blade.php       ← Master layout (CSS + sidebar)
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       └── tasks/
│           ├── index.blade.php         ← Dashboard + React component
│           ├── create.blade.php        ← Add task form
│           └── edit.blade.php          ← Edit task form
│
├── routes/
│   └── web.php                         ← All URL routes
│
├── .env.example                        ← Environment config template
├── composer.json                       ← PHP dependencies
└── README.md                           ← This file
```

---

## ✅ Rubric Checklist

| Category | Requirement | Implementation |
|----------|-------------|----------------|
| Planning | Problem ID | Student deadline management problem |
| Planning | Scope | Defined in README |
| Version Control | GitHub Repo | Full project on GitHub |
| Version Control | Commits | Semantic commits per feature |
| Frontend | HTML | Blade templates with semantic structure |
| Frontend | CSS | Custom design system with CSS variables, animations |
| Frontend | JavaScript | Form validation, confirm-delete dialog, live search |
| Frontend | React | StatusToggle component with useState + fetch |
| Backend | PHP | Laravel controllers with business logic |
| Backend | CRUD | Full Create/Read/Update/Delete on tasks |
| Backend | Validation | TaskRequest server-side validation |
| Database | Design | 3 normalized tables: users, tasks, categories |
| Database | SQL | INSERT, SELECT, UPDATE, DELETE via Eloquent |
| Database | Relationships | FK constraints, cascade delete, JOIN via eager loading |
| MVC | Model | User, Task, Category with Eloquent relationships |
| MVC | View | Blade views connected to controllers |
| MVC | Controller | TaskController + AuthController |
| Features | Dashboard | Stats grid + task table |
| Features | Forms | Create & Edit forms with validation |
| Features | Data Display | Dynamic table with badges and filters |
| Docs | Diagrams | ERD + MVC diagram + Flowchart (in README) |
| Docs | Description | Full technical write-up in README |
| Code Quality | Organization | Laravel's standard MVC folder structure |
| Code Quality | Comments | PHPDoc on all methods, inline JS comments |

---

## 👨‍💻 Developer

**Student Name:** *(Your Name)*
**Course:** *(Your Course)*
**School:** *(Your School)*
**Year/Section:** *(Your Section)*

---

*Built as a Final Project requirement demonstrating full-stack web development with PHP, Laravel, MySQL, JavaScript, and React.*
