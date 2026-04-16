# Task Management System

A complete task management system built with Laravel and modern web technologies, featuring JWT authentication, role-based access control (User & Admin), real-time task updates, and an approval-based workflow for all user actions.

## Features

### 🔐 Authentication
- JWT Token-based authentication
- User registration and login
- Role-based access (User & Admin)
- Secure password hashing

### 📋 Task Management (Approval-Based)
- **Create Task** - Users submit tasks requiring admin approval
- **Update Task** - Users can update existing tasks with approval workflow
- **Delete Task** - Users can request task deletion with admin approval
- **Task Submission** - Users submit task completion with screenshots and remarks

### 🎨 Task Status Visualization
Tasks are color-coded based on their status and deadline:
- **Yellow (#FFF2CC)** - Ongoing tasks within deadline
- **Green (#D5E8D4)** - Tasks completed on time
- **Red (#F8CECC)** - Tasks with breached deadline

### 👨‍💼 Admin Features
- **View All Tasks** - Dashboard showing all user tasks
- **Filter Tasks** - Filter by user, status, or approval status
- **Manage Requests** - Approve or reject user actions
- **Audit Trail** - Complete approval logs for all actions

### 🔄 Real-Time Updates
- All pages update without requiring page reload
- Admin sees user submissions instantly
- User sees approval/rejection status in real-time
- Polling-based real-time update mechanism (3-second intervals)

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Backend | Laravel 9 (PHP) |
| Database | MySQL |
| Authentication | JWT (JSON Web Tokens) |
| API | RESTful JSON API |

## Project Structure

```
task-management-system/
├── app/
│   ├── Models/
│   │   ├── User.php                 # User model with role support
│   │   ├── Task.php                 # Task model
│   │   ├── TaskRequest.php          # Approval request tracking
│   │   ├── TaskCompletion.php       # Task completion submissions
│   │   └── ApprovalLog.php          # Audit trail
│   └── Http/
│       ├── Controllers/Api/
│       │   ├── AuthController.php   # Authentication endpoints
│       │   ├── TaskController.php   # User task operations
│       │   └── AdminController.php  # Admin operations
│       ├── Middleware/
│       │   └── AdminMiddleware.php  # Admin role verification
│       └── Kernel.php               # HTTP middleware configuration
├── database/
│   └── migrations/
│       ├── 2014_10_12_000000_create_users_table.php
│       ├── 2024_04_16_000001_create_tasks_table.php
│       ├── 2024_04_16_000002_create_task_requests_table.php
│       ├── 2024_04_16_000003_create_task_completions_table.php
│       └── 2024_04_16_000004_create_approval_logs_table.php
├── public/
│   ├── index.html                   # Login/Register page
│   ├── dashboard.html               # User dashboard
│   └── admin-dashboard.html         # Admin dashboard
├── routes/
│   └── api.php                      # API routes
├── .env                             # Environment configuration
└── README.md                        # This file
```

## Database Schema

### Users Table
```sql
- id (Primary Key)
- name (string)
- email (unique)
- password (hashed)
- role (enum: 'user', 'admin')
- timestamps
```

### Tasks Table
```sql
- id (Primary Key)
- user_id (Foreign Key to Users)
- title (string)
- description (text)
- status (enum: 'Pending', 'Ongoing', 'Completed')
- deadline (datetime)
- approval_status (enum: 'Pending', 'Approved', 'Rejected')
- admin_remarks (text, nullable)
- timestamps
```

### Task Requests Table
```sql
- id (Primary Key)
- task_id (Foreign Key to Tasks, nullable)
- user_id (Foreign Key to Users)
- action_type (enum: 'Create', 'Update', 'Delete', 'Completion')
- old_data (json, nullable)
- new_data (json)
- status (enum: 'Pending', 'Approved', 'Rejected')
- timestamps
```

### Task Completions Table
```sql
- id (Primary Key)
- task_id (Foreign Key to Tasks)
- user_id (Foreign Key to Users)
- screenshots (json - array of file paths)
- remarks (text)
- verification_status (enum: 'Pending', 'Verified', 'Rejected')
- admin_remarks (text, nullable)
- timestamps
```

### Approval Logs Table
```sql
- id (Primary Key)
- task_id (Foreign Key to Tasks, nullable)
- admin_id (Foreign Key to Users - Admin)
- action (enum: 'Approved', 'Rejected', 'Verified', 'Rejected Completion')
- remarks (text, nullable)
- old_data (json, nullable)
- new_data (json, nullable)
- timestamps
```

## Setup Instructions

### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Composer
- Apache/Nginx web server (XAMPP recommended)

### Installation Steps

1. **Navigate to the project directory**
```bash
cd c:\xampp\htdocs\assignment\task-management-system
```

2. **Create MySQL Database**
```sql
CREATE DATABASE task_management_system;
```

3. **Configure Environment**
   - Copy `.env.example` to `.env` (already done)
   - Update `.env` with your database credentials:
```env
DB_DATABASE=task_management_system
DB_USERNAME=root
DB_PASSWORD=
```

4. **Install Dependencies**
```bash
composer install
```

5. **Generate Application Key**
```bash
php artisan key:generate
```

6. **Run Database Migrations**
```bash
php artisan migrate
```

7. **Create Test Users (Optional)**
```bash
php artisan tinker
# Then run:
>>> use App\Models\User, Illuminate\Support\Facades\Hash;
>>> User::create(['name' => 'Admin User', 'email' => 'admin@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);
>>> User::create(['name' => 'Test User', 'email' => 'user@test.com', 'password' => Hash::make('password'), 'role' => 'user']);
>>> exit
```

8. **Start the Application**
```bash
php artisan serve
```
Or access via XAMPP: `http://localhost/assignment/task-management-system/public`

## API Endpoints

### Authentication Endpoints

#### POST `/api/auth/login`
Login user with email and password.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "user"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

#### POST `/api/auth/register`
Register a new user.

**Request:**
```json
{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password",
  "role": "user"
}
```

#### GET `/api/auth/me`
Get current user information (requires token).

### Task Endpoints

#### GET `/api/tasks`
Get all tasks for the current user (requires token).

#### POST `/api/tasks`
Create a new task (requires admin approval, requires token).

**Request:**
```json
{
  "title": "Complete Project",
  "description": "Finish the Laravel project",
  "deadline": "2024-04-30T18:00:00"
}
```

#### PUT `/api/tasks/{id}`
Update task (requires admin approval, requires token).

#### DELETE `/api/tasks/{id}`
Delete task (requires admin approval, requires token).

#### POST `/api/tasks/{id}/submit-completion`
Submit task completion with screenshots and remarks (requires token).

**Request:** (multipart/form-data)
- `screenshots[]` - Array of image files (minimum 3)
- `remarks` - Text remarks about completion

### Admin Endpoints

#### GET `/api/admin/tasks`
Get all tasks in the system (admin only).

#### GET `/api/admin/requests`
Get all pending requests (action and completion requests).

#### POST `/api/admin/approve-request/{requestId}`
Approve a pending request (admin only).

#### POST `/api/admin/reject-request/{requestId}`
Reject a pending request (admin only).

**Request:**
```json
{
  "remarks": "Title needs to be more descriptive"
}
```

#### POST `/api/admin/verify-completion/{completionId}`
Verify task completion (admin only).

#### POST `/api/admin/reject-completion/{completionId}`
Reject task completion (admin only).

**Request:**
```json
{
  "remarks": "Screenshots do not show completion"
}
```

#### GET `/api/admin/filter-tasks`
Filter tasks with query parameters (admin only).

**Query Parameters:**
- `user_id` - Filter by user ID
- `status` - Filter by task status (Pending, Ongoing, Completed)
- `approval_status` - Filter by approval status (Pending, Approved, Rejected)

## Workflow Documentation

### Task Creation Workflow

1. **User Creates Task**
   - User fills in task form with title, description, and deadline
   - Submits form through `/api/tasks`
   - A `TaskRequest` record is created with action_type = 'Create' and status = 'Pending'

2. **Admin Reviews Request**
   - Admin sees the request in "Pending Requests" tab
   - Admin can approve or reject

3. **If Approved**
   - New `Task` record is created with approval_status = 'Approved'
   - Status is set to 'Pending' (ready for user to work on)
   - `ApprovalLog` entry recorded

4. **If Rejected**
   - `TaskRequest` status becomes 'Rejected'
   - User sees rejection with admin remarks

### Task Completion Workflow

1. **User Submits Completion**
   - User clicks "Submit" button on task
   - Form opens for screenshot upload (minimum 3) and remarks
   - Submits via `/api/tasks/{id}/submit-completion`
   - A `TaskCompletion` record is created with verification_status = 'Pending'

2. **Admin Reviews Completion**
   - Admin sees the completion request in "Pending Requests"
   - Can view screenshots and remarks
   - Can verify or reject

3. **If Verified**
   - Task status changes to 'Completed'
   - `TaskCompletion` verification_status = 'Verified'
   - `ApprovalLog` entry recorded

4. **If Rejected**
   - Task status reverts to 'Pending'
   - User must resubmit with improvements
   - Admin remarks shown to user

## Real-Time Updates Implementation

The system uses **polling-based real-time updates** with 3-second intervals:

- **Frontend**: JavaScript `setInterval(loadTasks, 3000)` fetches updates every 3 seconds
- **Admin Dashboard**: Automatically refreshes pending requests and task list
- **User Dashboard**: Shows latest task statuses and approval decisions
- **No page reload required**: Updates happen silently in background

## Authentication & Authorization

### JWT Token Structure
```
Header.Payload.Signature
```

**Token Expiry**: 24 hours from generation

### Authorization Middleware
- `AdminMiddleware` - Verifies user role is 'admin'
- Applied to all `/api/admin/*` routes

## Edge Cases Handled

1. **Concurrent Task Updates** - Last-write-wins with audit trail
2. **Completion Verification Failure** - Task reverts to pending with human-readable remarks
3. **Unauthorized Access** - 403 Forbidden responses for role violations
4. **Invalid Token** - 401 Unauthorized for expired/missing tokens
5. **File Upload Validation** - Minimum 3 screenshots, image format verification
6. **Deadline Breaching** - Automatic red color coding for overdue tasks

## Testing the System

### Test Credentials (after running seeder)

**Admin:**
- Email: admin@test.com
- Password: password

**Regular User:**
- Email: user@test.com
- Password: password

### Workflow Testing

1. **Login as User** → Create Task → Observe pending approval
2. **Login as Admin** → Approve Task → Check user dashboard updates
3. **Login as User** → View approved task → Submit completion with 3+ screenshots  
4. **Login as Admin** → Review completion → Verify or reject
5. **Check Audit Logs** in database `approval_logs` table

## Key Implementation Details

### Real-Time Without WebSockets
Using JavaScript polling (3-second intervals) for simplicity:
- No WebSocket server required
- Works in all browsers
- Battery-efficient on mobile
- Network-friendly

### Approval Workflow
All user actions (create/update/delete) go through approval:
- No direct database modifications
- All changes tracked in `TaskRequest` table
- Complete audit trail in `ApprovalLog`
- Admin has full control

### File Management
- Screenshots stored in `storage/app/public/completions/`
- Served through Laravel's `disk('public')`
- Supports multiple image formats (jpg, png, gif, webp)
- Maximum 5MB per file

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running
- Check database credentials in `.env`
- Run `php artisan migrate` again

### 501 Routes Not Found
- Check `.env` `APP_URL` matches your server URL
- Ensure `.htaccess` in public folder is properly configured

### JWT Token Errors
- Tokens expire after 24 hours - user must login again
- Check `JWT_SECRET` in `.env`

### Upload Errors
- Ensure `storage/app/public` directory is writable
- Check file size limits in php.ini

## Security Considerations

1. **Password Hashing**: Using Laravel's `Hash::make()` with bcrypt
2. **Token Expiry**: JWT tokens expire after 24 hours
3. **Input Validation**: All inputs validated server-side
4. **CORS**: Configure as needed for production
5. **SQL Injection**: Protected via Eloquent ORM
6. **XSS**: Frontend uses text content binding

## Performance Optimization

1. **Database Indexing**: Foreign keys indexed for faster queries
2. **Lazy Loading Prevention**: Using `with()` for eager loading
3. **Pagination**: Can be added to large result sets
4. **Response Caching**: Can implement for admin dashboard

## Future Enhancements

1. **WebSocket Real-Time Updates** - Using Laravel Websockets
2. **Email Notifications** - Send updates via email
3. **Task Comments** - Discussion thread per task
4. **Activity Feed** - Timeline of all actions
5. **Export Reports** - CSV/PDF export functionality
6. **Mobile App** - React Native mobile client
7. **Two-Factor Authentication** - Enhanced security
8. **Task Dependencies** - Linked tasks and subtasks

## API Request Headers

All protected endpoints require:
```
Authorization: Bearer {token}
Content-Type: application/json
```

## Deliverables Checklist

- ✅ Source code with proper Git structure
- ✅ README with setup instructions
- ✅ Architecture explanation (see this document)
- ✅ API documentation (see above)
- ✅ Clean database schema
- ✅ Approval workflow implementation
- ✅ Real-time updates (polling-based)
- ✅ User and Admin interfaces
- ✅ Audit logs for all actions
- ✅ Proper error handling and edge cases

## License

This project is open source and available under the MIT License.

---

**Version**: 1.0.0  
**Last Updated**: April 16, 2024  
**Author**: Development Team
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
