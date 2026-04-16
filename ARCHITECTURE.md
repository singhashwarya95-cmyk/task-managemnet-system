# System Architecture Documentation

## Overview

The Task Management System is built on a **role-based approval workflow architecture** where:
- **Users** can create/update/delete tasks but changes require admin approval
- **Admins** review, approve, or reject all user actions
- **System** maintains complete audit trail for transparency

This ensures quality control and accountability in task management.

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                           FRONTEND LAYER                         │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ index.html (Login/Register) - Vanilla JS + CSS          │   │
│  │ dashboard.html (User Dashboard) - Task Management       │   │
│  │ admin-dashboard.html (Admin Panel) - Approvals          │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              ↓
           ┌──────────────────────────────────────┐
           │   API Gateway (Routes)               │
           │   /api/auth/* - Authentication      │
           │   /api/tasks/* - User tasks          │
           │   /api/admin/* - Admin operations   │
           └──────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                      BACKEND / BUSINESS LOGIC LAYER              │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Controllers (API)                                         │   │
│  │ ├── AuthController - Login/Logout/Register              │   │
│  │ ├── TaskController - CRUD operations                    │   │
│  │ └── AdminController - Approval workflows                │   │
│  └──────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Models (Eloquent ORM)                                     │   │
│  │ ├── User - User with role field                         │   │
│  │ ├── Task - Task model                                   │   │
│  │ ├── TaskRequest - Approval tracking                     │   │
│  │ ├── TaskCompletion - Submission tracking                │   │
│  │ └── ApprovalLog - Audit trail                           │   │
│  └──────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Middleware                                                │   │
│  │ ├── Authenticate - JWT token validation                 │   │
│  │ └── AdminMiddleware - Role-based access                 │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                       DATABASE LAYER (MySQL)                     │
│  ┌────────────┐  ┌────────────┐  ┌─────────────────┐            │
│  │   users    │  │   tasks    │  │  task_requests  │            │
│  ├────────────┤  ├────────────┤  ├─────────────────┤            │
│  │ id         │  │ id         │  │ id              │            │
│  │ name       │  │ user_id →  │  │ task_id →       │            │
│  │ email      │  │ title      │  │ user_id →       │            │
│  │ password   │  │ description│  │ action_type     │            │
│  │ role       │  │ status     │  │ old_data        │            │
│  │ timestamps │  │ deadline   │  │ new_data        │            │
│  └────────────┘  │ approval   │  │ status          │            │
│                  │ timestamps │  │ timestamps      │            │
│                  └────────────┘  └─────────────────┘            │
│  ┌──────────────────────┐  ┌──────────────────────┐             │
│  │ task_completions     │  │  approval_logs       │             │
│  ├──────────────────────┤  ├──────────────────────┤             │
│  │ id                   │  │ id                   │             │
│  │ task_id →            │  │ task_id →            │             │
│  │ user_id →            │  │ admin_id →           │             │
│  │ screenshots (json)   │  │ action               │             │
│  │ remarks              │  │ remarks              │             │
│  │ verification_status  │  │ old_data (json)      │             │
│  │ admin_remarks        │  │ new_data (json)      │             │
│  │ timestamps           │  │ timestamps           │             │
│  └──────────────────────┘  └──────────────────────┘             │
└─────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagram

### Task Creation Workflow
```
User                              System                            Admin
 │                                  │                               │
 ├─→ Create Task Form ───────────→ API /tasks (POST) ──────────────→ │
 │                                  │                               │
 │                            Create TaskRequest                    │
 │                            (status: Pending)                     │
 │                                  │                               │
 │                              ✓ Saved                             │
 │                                  │                               │
 │  ← ← ← ← ← ← ← (polling every 3s)← ← ← ← ← ← ← ← ← ← ← ← ← ← ← │
 │                                  │ ← Show in "Pending" ← ← ← ← ←  │
 │                                  │                      Admin sees request
 │                                  │                               │
 │                                  │ ← ← ← ← ← Approve/Reject ←─── │
 │                                  │                               │
 │                         Create Task OR Reject                    │
 │                                  │                               │
 │  ← ← ← ← (auto poll, 3s) ← ← ← Show Updated Status              │
 │  See Approved/Rejected ←──────────────────────────────────────← │
```

### Task Completion Verification Workflow
```
User                              System                            Admin
 │                                  │                               │
 ├─→ Submit Completion ─────────→ API /tasks/{id}/submit-completion │
 │   (3+ screenshots               │                               │
 │    + remarks)                   Create TaskCompletion             │
 │                               (status: Pending)                  │
 │                                  │                               │
 │  ← ← ← ← ← (polling, 3s) ← ← ← Request in "Pending" ← ← ← ← ← ← │
 │                                  │        Admin sees submission   │
 │                                  │                               │
 │                                  │ ← ← ← Verify/Reject ←──────── │
 │                                  │                               │
 │                   Update Task Status & Task Completion            │
 │                                  │                               │
 │  ← ← ← (auto poll, 3s) ← ← ← See "Completed" or "Rejected" ←─── │
```

---

## Service Layer Components

### 1. AuthController
**Responsibility**: User authentication and authorization

```
POST /auth/login
  ├─ Validate credentials
  ├─ Check user exists
  ├─ Verify password
  ├─ Generate JWT token
  └─ Return token + user data

POST /auth/register
  ├─ Validate input
  ├─ Hash password
  ├─ Create user record
  ├─ Assign role (default: user)
  └─ Return token

GET /auth/me
  ├─ Verify token
  └─ Return current user
```

### 2. TaskController
**Responsibility**: User task operations with approval workflow

```
GET /tasks
  ├─ Get authenticated user ID
  └─ Return user's tasks with status

POST /tasks
  ├─ Validate input (title, description, deadline)
  ├─ Create TaskRequest (action: Create, status: Pending)
  └─ Return approval notification

PUT /tasks/{id}
  ├─ Verify user owns task
  ├─ Create TaskRequest (action: Update)
  ├─ Store old_data and new_data
  └─ Return pending approval message

DELETE /tasks/{id}
  ├─ Verify user owns task
  ├─ Create TaskRequest (action: Delete)
  └─ Mark for admin deletion

POST /tasks/{id}/submit-completion
  ├─ Validate 3+ screenshots
  ├─ Validate remarks text
  ├─ Upload images to storage
  ├─ Create TaskCompletion record
  └─ Await admin verification
```

### 3. AdminController
**Responsibility**: Approval management and oversight

```
GET /admin/tasks
  ├─ Eager load relationships
  └─ Return all tasks with user info

GET /admin/requests
  ├─ Get pending TaskRequests
  ├─ Get pending TaskCompletions
  └─ Return both arrays

POST /admin/approve-request/{requestId}
  ├─ Verify admin role
  ├─ Get TaskRequest
  ├─ Apply changes based on action_type:
  │  ├─ CREATE → Create new Task
  │  ├─ UPDATE → Update Task
  │  └─ DELETE → Delete Task
  ├─ Create ApprovalLog entry
  └─ Return success

POST /admin/reject-request/{requestId}
  ├─ Verify admin role
  ├─ Mark TaskRequest as Rejected
  ├─ If task exists, set status to Rejected
  ├─ Store admin remarks
  ├─ Create ApprovalLog entry
  └─ Return rejection details

POST /admin/verify-completion/{completionId}
  ├─ Verify admin role
  ├─ Get TaskCompletion
  ├─ Update Task status to Completed
  ├─ Set verification_status to Verified
  ├─ Create ApprovalLog entry
  └─ Return success

POST /admin/reject-completion/{completionId}
  ├─ Verify admin role
  ├─ Set verification_status to Rejected
  ├─ Revert Task to Pending
  ├─ Store admin remarks
  ├─ Create ApprovalLog entry
  └─ Return rejection details
```

---

## Database State Transitions

### Task State Machine
```
┌─────────────┐
│   Pending   │← ← ← ← ← ← ← ← ← ← Rejected Completion
│  (approval) │
└──────┬──────┘
       │ Admin Approves
       ↓
┌─────────────┐
│   Pending   │← ← ← ← ← ← ← ← ← ← Rejected Update
│ (may start) │
└──────┬──────┘
       │ User starts work / Deadline passed
       ↓
┌─────────────┐
│  Ongoing    │
│(in progress)│
└──────┬──────┘
       │ User submits completion
       ↓
┌─────────────┐
│ Completion  │← Admin can Reject and revert to Pending
│  Pending    │
└──────┬──────┘
       │ Admin Verifies
       ↓
┌─────────────┐
│ Completed   │ (Final State)
│ (Verified)  │
└─────────────┘
```

### TaskRequest State Machine
```
┌─────────────┐
│  Pending    │ (User request submitted)
│ (awaiting)  │
└──────┬──────┘
       ├─ (Approve) → Apply to system
       │
       └─ (Reject) → Rejected (final)
```

---

## Approval Logic

### Create Task Approval
```javascript
if (admin.approves) {
  Task.create({
    title: request.new_data.title,
    description: request.new_data.description,
    deadline: request.new_data.deadline,
    user_id: request.user_id,
    approval_status: 'Approved',
    status: 'Pending' // Ready for user
  })
}
```

### Update Task Approval
```javascript
if (admin.approves) {
  task.update({
    title: request.new_data.title,
    description: request.new_data.description,
    deadline: request.new_data.deadline,
    approval_status: 'Approved'
  })
}
```

### Completion Verification
```javascript
if (admin.verifies) {
  task.update({
    status: 'Completed',
    approval_status: 'Verified'
  })
  completion.update({
    verification_status: 'Verified'
  })
} else if (admin.rejects) {
  task.update({
    status: 'Pending', // Back to work
    approval_status: 'Pending'
  })
  completion.update({
    verification_status: 'Rejected',
    admin_remarks: remarks
  })
}
```

---

## Real-Time Update Mechanism

### Frontend Polling Algorithm
```javascript
// User Dashboard
every 3 seconds:
  FETCH /api/tasks (with token)
  DISPLAY updated tasks
  SHOW approval status changes
  
// Admin Dashboard
every 3 seconds:
  FETCH /api/admin/requests (with token)
  FETCH /api/admin/tasks (with token)
  UPDATE pending requests display
  UPDATE task list display
```

### Event Timeline (3-Second Cycle)
```
Time 0s:    User creates task → API receives request
Time 0s+:   TaskRequest created in DB
Time 3s:    Admin dashboard polls → Sees new request
Time 3s+:   Admin approves → Task created
Time 6s:    User dashboard polls → Sees approved task
```

---

## Security Architecture

### Authentication Layer
```
User Login
  ↓
Check credentials (email + password)
  ↓
Generate JWT Token (valid 24 hours)
  ↓
Store in localStorage (frontend)
  ↓
Send with every API request header
  ↓
Middleware validates on backend
  ↓
Request continues OR 401 Unauthorized
```

### Authorization Layer
```
AdminMiddleware:
  if (user.role !== 'admin') {
    return 403 Forbidden
  }

User Resource Check:
  if (task.user_id !== request.user().id) {
    if (request.user().role !== 'admin') {
      return 403 Forbidden
    }
  }
```

---

## Data Integrity Safeguards

1. **Foreign Key Constraints**
   - task_id → tasks(id) on delete cascade
   - user_id → users(id) on delete cascade
   - admin_id → users(id) on delete cascade

2. **Audit Trail**
   - All approvals logged in approval_logs
   - Both old_data and new_data stored
   - Timestamp of action recorded

3. **State Validation**
   - Can't approve already approved task
   - Can't complete already completed task
   - Can't verify already verified completion

4. **Permission Checks**
   - User can only see own tasks
   - Admin can see all tasks
   - Only admin can approve

---

## API Response Patterns

### Success Response (200/201)
```json
{
  "message": "Action completed",
  "data": { /* resource */ },
  "task": { /* affected task */ }
}
```

### Error Response (400/401/403/500)
```json
{
  "message": "Error description"
}
```

### List Response
```json
[
  { /* resource 1 */ },
  { /* resource 2 */ }
]
```

---

## Performance Characteristics

| Operation | Complexity | Notes |
|-----------|-----------|-------|
| Login | O(1) | Password hash verification |
| Get Tasks | O(n) | n = number of user's tasks |
| Create Task | O(1) | Creates TaskRequest |
| Approve Task | O(1) | Creates/Updates 1-2 records |
| Filter Tasks | O(n) | n = all tasks, filtered in SQL |
| List Admin Requests | O(m+p) | m=requests, p=completions |

---

## Scalability Considerations

### Database Optimization
- Index on user_id, approval_status, task deadlines
- Foreign key relationships indexed
- JSON columns for flexible data storage

### API Optimization
- Eager loading with `with()` to prevent N+1 queries
- Single query per endpoint where possible
- Response pagination for large datasets

### Frontend Optimization
- Polling interval (3s) balanced for UX and server load
- Minimal data transfer (only changed records)
- Client-side caching of token

---

## Testing Scenarios

### User Workflow
1. Register → Create → Submit Completion
2. Update Task Request → Admin approval
3. Delete Request → Admin deletion

### Admin Workflow
1. View all requests
2. Approve/reject actions
3. Verify/reject completions
4. Filter tasks by criteria

### Edge Cases
1. Concurrent update requests
2. Expired token handling
3. Invalid file uploads
4. Deleted user impact

---

## Future Architecture Enhancements

1. **Event-Driven Architecture**
   - Task events (created, updated, approved)
   - User notifications via events
   - WebSocket for real-time push

2. **Microservices**
   - Notification service
   - File processing service
   - Report generation service

3. **Caching Layer**
   - Redis for session tokens
   - Cache frequent queries
   - Task list caching

4. **Message Queue**
   - Queue approval notifications
   - Queue email sends
   - Async file processing

---

**Architecture Version**: 1.0  
**Last Updated**: April 16, 2024
