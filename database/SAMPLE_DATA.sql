-- Task Management System - Database Dump
-- Version: 1.0.0
-- Created: April 16, 2024

-- Sample Data (Optional - for testing purposes)

-- Create test users
INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
('Admin User', 'admin@test.com', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'admin', NOW(), NOW()),
('John Doe', 'user@test.com', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'user', NOW(), NOW()),
('Jane Smith', 'jane@test.com', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'user', NOW(), NOW());

-- Create sample tasks approved by admin
INSERT INTO `tasks` (`user_id`, `title`, `description`, `status`, `deadline`, `approval_status`, `admin_remarks`, `created_at`, `updated_at`) VALUES
(2, 'Complete Laravel Project', 'Build a complete task management system with Laravel and API integration', 'Pending', DATE_ADD(NOW(), INTERVAL 7 DAY), 'Approved', NULL, NOW(), NOW()),
(2, 'Write API Documentation', 'Create comprehensive API documentation with examples', 'Ongoing', DATE_ADD(NOW(), INTERVAL 5 DAY), 'Approved', NULL, NOW(), NOW()),
(3, 'Database Design', 'Design normalized database schema', 'Completed', DATE_ADD(NOW(), INTERVAL 3 DAY), 'Approved', NULL, NOW(), NOW());

-- Create sample task requests awaiting approval
INSERT INTO `task_requests` (`task_id`, `user_id`, `action_type`, `old_data`, `new_data`, `status`, `created_at`, `updated_at`) VALUES
(NULL, 2, 'Create', NULL, JSON_OBJECT('title', 'Review Code', 'description', 'Perform code review for new features', 'deadline', DATE_ADD(NOW(), INTERVAL 4 DAY)), 'Pending', NOW(), NOW()),
(1, 3, 'Update', JSON_OBJECT('title', 'OLD: Complete Laravel Project'), JSON_OBJECT('title', 'Complete Laravel + Vue.js Project', 'description', 'Updated description'), 'Pending', NOW(), NOW());

-- Create sample approval logs
INSERT INTO `approval_logs` (`task_id`, `admin_id`, `action`, `remarks`, `old_data`, `new_data`, `created_at`, `updated_at`) VALUES
(1, 1, 'Approved', NULL, NULL, JSON_OBJECT('title', 'Complete Laravel Project'), NOW(), NOW()),
(2, 1, 'Approved', NULL, NULL, JSON_OBJECT('title', 'Write API Documentation'), NOW(), NOW());

-- Note: Password hashes above are placeholders
-- To create actual test users, use:
-- php artisan tinker
-- User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);
-- User::create(['name' => 'John', 'email' => 'user@test.com', 'password' => Hash::make('password'), 'role' => 'user']);
