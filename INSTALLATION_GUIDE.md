# Quick Installation Guide

## Step-by-Step Setup (5 minutes)

### 1. Database Setup
```bash
# Open MySQL command line and create database
mysql -u root -p
> CREATE DATABASE task_management_system;
> EXIT;
```

### 2. Project Configuration
```bash
cd c:\xampp\htdocs\assignment\task-management-system

# Update .env file with database credentials (already configured with defaults)
# DB_DATABASE=task_management_system
# DB_USERNAME=root
# DB_PASSWORD= (leave blank if no password)
```

### 3. Install & Migrate
```bash
# Install PHP dependencies
composer install

# Generate encryption key
php artisan key:generate

# Run database migrations
php artisan migrate

# Optional: Seed sample data
# php artisan tinker
# > User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => Hash::make('password123'), 'role' => 'admin']);
# > User::create(['name' => 'John', 'email' => 'user@test.com', 'password' => Hash::make('password123'), 'role' => 'user']);
# > exit
```

### 4. Start the Application

**Option A: Using PHP Built-in Server**
```bash
cd c:\xampp\htdocs\assignment\task-management-system
php artisan serve
# Access: http://localhost:8000
```

**Option B: Using XAMPP**
1. Start XAMPP Apache
2. Access: http://localhost/assignment/task-management-system/public

### 5. Login

**Admin Account:**
- Email: admin@test.com
- Password: password123
- Access: Admin Dashboard for approvals

**User Account:**
- Email: user@test.com
- Password: password123
- Access: User Dashboard for task management

---

## Feature Quick Test

1. **Create a Task (User)**
   - Login as user@test.com
   - Click "+ Create Task"
   - Fill form and submit
   - Task awaits admin approval

2. **Approve Task (Admin)**
   - Login as admin@test.com
   - Go to "Pending Requests" tab
   - Click "Approve" to create task
   - User sees task instantly (3-second poll)

3. **Submit Task Completion (User)**
   - Click task "Submit" button
   - Upload 3+ screenshots
   - Add remarks
   - Await admin verification

4. **Verify Completion (Admin)**
   - Review completion in "Pending Requests"
   - Click "Verify" or "Reject"
   - All actions logged in approval_logs table

---

## File Structure Overview

```
public/
├── index.html                    ← Login page
├── dashboard.html                ← User dashboard
└── admin-dashboard.html          ← Admin panel

app/Http/Controllers/Api/
├── AuthController.php            ← Login/Register
├── TaskController.php            ← User tasks
└── AdminController.php           ← Admin actions

app/Models/
├── User.php                      ← With role field
├── Task.php                      ← Task model
├── TaskRequest.php               ← Approval tracking
├── TaskCompletion.php            ← Screenshots + remarks
└── ApprovalLog.php               ← Audit trail

routes/
└── api.php                       ← All API endpoints

database/migrations/
├── ...users_table
├── ...tasks_table
├── ...task_requests_table
├── ...task_completions_table
└── ...approval_logs_table
```

---

## Key Files to Review

| File | Purpose |
|------|---------|
| `README.md` | Complete documentation |
| `routes/api.php` | All API endpoints |
| `app/Http/Kernel.php` | Middleware configuration |
| `.env` | Environment settings |
| `public/dashboard.html` | User interface |
| `public/admin-dashboard.html` | Admin interface |

---

## API ENDPOINTS SUMMARY

### Public
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration

### User (requires token)
- `GET /api/tasks` - List user tasks
- `POST /api/tasks` - Create task (for approval)
- `PUT /api/tasks/{id}` - Update task (for approval)
- `DELETE /api/tasks/{id}` - Delete task (for approval)
- `POST /api/tasks/{id}/submit-completion` - Submit completion

### Admin (requires admin role + token)
- `GET /api/admin/tasks` - All tasks
- `GET /api/admin/requests` - Pending requests
- `POST /api/admin/approve-request/{id}` - Approve action
- `POST /api/admin/reject-request/{id}` - Reject action
- `POST /api/admin/verify-completion/{id}` - Verify task
- `POST /api/admin/reject-completion/{id}` - Reject task
- `GET /api/admin/filter-tasks` - Filter tasks

---

## Real-Time Updates

- **Polling Interval**: 3 seconds
- **No WebSocket Required**: Works everywhere
- **Auto-Refresh**: Dashboard updates without manual refresh
- **No Page Reload**: Seamless experience

---

## Troubleshooting

### "SQLSTATE[HY000]: General error"
↳ Run: `php artisan migrate`

### "Class not found" errors
↳ Run: `composer dump-autoload`

### 404 on routes
↳ Ensure accessing: `http://localhost/.../public/`

### Token expired
↳ Expires in 24 hours - login again

### File upload fails
↳ Check `storage/app/public/` is writable

---

## Important Notes

1. **All changes** go through approval workflow - no direct updates
2. **Screenshots** at least 3 required for task submission
3. **Admin controls everything** - users can't bypass approval
4. **Audit trail** - all actions tracked in approval_logs
5. **Color coding** - automatic based on status and deadline

---

## Next Steps

1. ✅ Setup database
2. ✅ Run migrations  
3. ✅ Create test users
4. ✅ Login and explore
5. ✅ Test approval workflow
6. ✅ Check database tables

---

**Happy Task Managing!** 📋✨

For detailed API docs, see `README.md`
