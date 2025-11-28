# Strategic Plan Manager - Project Plan

## Current Status
✅ **Phase 1: Core Application (COMPLETED)**
- PHP/MySQL migration from SvelteKit
- Database schema for goals and projects
- CRUD operations for goals and projects
- Scottish Government Design System (SGDS) integration
- Switchable design systems (SGDS/Tailwind)
- Basic routing and page structure
- Local and Hostinger deployment ready

---

## Phase 2: Multi-Tenancy & User Management (PLANNED)

### Overview
Transform the application from a single-organization system to a multi-tenant platform where:
- Organizations can register and manage their own strategic plans
- Super administrators can create and manage organizations
- Domain-based seat allocation and user management
- Email verification system
- Seat limit enforcement

### Requirements

#### 1. Super User System
- **Super Admin Account**
  - Create/manage organizations
  - Allocate seats per domain
  - View all organizations and their usage
  - Manage seat limits
  - Deactivate/reactivate organizations

#### 2. Organization Management
- **Organization Model**
  - Organization name
  - Domain (e.g., ramh.org.uk)
  - Seat allocation (number of allowed users)
  - Status (active/inactive)
  - Created date
  - Contact information

#### 3. User Registration & Authentication
- **User Registration**
  - Email-based registration
  - Domain validation (must match organization domain)
  - Email verification required
  - Password requirements
  - Terms of service acceptance

- **User Authentication**
  - Login/logout
  - Password reset
  - Session management
  - Remember me functionality

#### 4. Seat Management System
- **Domain-Based Seat Allocation**
  - Super admin allocates seats per domain (e.g., 10 seats for ramh.org.uk)
  - Users register with email matching domain
  - Email verification required before seat is "occupied"
  - Seat count = number of verified users from that domain
  - When seat limit reached, new registrations blocked
  - When user leaves/deactivated, seat becomes available

- **Seat Enforcement Rules**
  - Only verified users count toward seat limit
  - Pending verifications don't count
  - Deactivated users free up seats
  - Super admin can manually adjust seat counts

#### 5. Email Verification System
- **Verification Process**
  - Send verification email on registration
  - Unique verification token (expires after 7 days)
  - Click link to verify
  - Resend verification email option
  - Only verified users can access organization data

#### 6. Data Isolation
- **Multi-Tenant Data Structure**
  - All goals/projects linked to organization
  - Users can only see/edit their organization's data
  - Super admin can view all organizations
  - Database queries filtered by organization_id

---

## Database Schema Changes

### New Tables Required

#### `organizations`
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR(255))
- domain (VARCHAR(255), UNIQUE) - e.g., "ramh.org.uk"
- seat_allocation (INT) - number of seats allocated
- status (ENUM: 'active', 'inactive', 'suspended')
- contact_email (VARCHAR(255))
- contact_name (VARCHAR(255))
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- created_by (INT) - super admin user_id
```

#### `users`
```sql
- id (INT, PRIMARY KEY)
- organization_id (INT, FOREIGN KEY)
- email (VARCHAR(255), UNIQUE)
- password_hash (VARCHAR(255))
- first_name (VARCHAR(100))
- last_name (VARCHAR(100))
- role (ENUM: 'admin', 'user', 'super_admin')
- email_verified (BOOLEAN, DEFAULT FALSE)
- verification_token (VARCHAR(255))
- verification_token_expires (TIMESTAMP)
- password_reset_token (VARCHAR(255))
- password_reset_expires (TIMESTAMP)
- last_login (TIMESTAMP)
- status (ENUM: 'active', 'inactive', 'pending_verification')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### `sessions`
```sql
- id (VARCHAR(128), PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- ip_address (VARCHAR(45))
- user_agent (TEXT)
- last_activity (TIMESTAMP)
- created_at (TIMESTAMP)
```

### Modified Tables

#### `goals`
```sql
- Add: organization_id (INT, FOREIGN KEY)
- Add: created_by (INT, FOREIGN KEY to users)
```

#### `projects`
```sql
- Add: organization_id (INT, FOREIGN KEY)
- Add: created_by (INT, FOREIGN KEY to users)
```

---

## Implementation Plan

### Step 1: Database Schema Updates
- [ ] Create organizations table
- [ ] Create users table
- [ ] Create sessions table
- [ ] Add organization_id to goals table
- [ ] Add organization_id to projects table
- [ ] Add created_by fields to goals and projects
- [ ] Create migration script
- [ ] Update existing data (assign to default organization)

### Step 2: Authentication System
- [ ] User registration form
- [ ] Domain validation
- [ ] Seat availability check
- [ ] Password hashing (bcrypt)
- [ ] Email verification token generation
- [ ] Email sending functionality (SMTP/PHPMailer)
- [ ] Verification email template
- [ ] Verification link handler
- [ ] Login system
- [ ] Session management
- [ ] Password reset functionality
- [ ] Logout functionality

### Step 3: Super Admin Interface
- [ ] Super admin login (separate from regular users)
- [ ] Organization creation form
- [ ] Organization listing page
- [ ] Organization edit page
- [ ] Seat allocation management
- [ ] Organization status management
- [ ] User management (view all users per organization)
- [ ] Dashboard with organization statistics

### Step 4: Multi-Tenant Data Isolation
- [ ] Middleware to check user organization
- [ ] Update all queries to filter by organization_id
- [ ] Update Goal model to include organization_id
- [ ] Update Project model to include organization_id
- [ ] Ensure users can only access their organization's data
- [ ] Add organization context to all pages

### Step 5: Seat Management Logic
- [ ] Function to count verified users per domain
- [ ] Function to check seat availability
- [ ] Registration blocking when seats full
- [ ] Seat release on user deactivation
- [ ] Seat release on email verification expiry
- [ ] Display seat usage in organization dashboard

### Step 6: User Interface Updates
- [ ] Registration page
- [ ] Login page
- [ ] Email verification page
- [ ] Password reset pages
- [ ] User profile page
- [ ] Organization selector (if user belongs to multiple orgs - future)
- [ ] Update navigation for authenticated users
- [ ] Add "My Organization" section

### Step 7: Email System
- [ ] Configure SMTP settings in .env
- [ ] Email templates (verification, password reset, welcome)
- [ ] Email queue system (optional, for production)
- [ ] Email logging

### Step 8: Security Enhancements
- [ ] CSRF protection on all forms
- [ ] Rate limiting on login/registration
- [ ] SQL injection prevention (already using prepared statements)
- [ ] XSS protection (already using htmlspecialchars)
- [ ] Secure password requirements
- [ ] Session security (httponly, secure flags)
- [ ] Password strength validation

### Step 9: Testing
- [ ] Test registration flow
- [ ] Test email verification
- [ ] Test seat limits
- [ ] Test multi-tenant data isolation
- [ ] Test super admin functions
- [ ] Test edge cases (expired tokens, full seats, etc.)

### Step 10: Documentation
- [ ] Update README with multi-tenant setup
- [ ] Super admin user guide
- [ ] Organization admin guide
- [ ] User guide
- [ ] API documentation (if needed)

---

## Language & Localisation

### UK English Standards
- **Spelling**: All user-facing text must use UK English spelling
  - "customise" not "customize"
  - "organise" not "organize"
  - "labelled" not "labeled"
  - "colour" not "color" (if used)
  - "centre" not "center" (if used)
  - "recognise" not "recognize"
  - "realise" not "realize"
- **Grammar**: UK English grammar conventions throughout
- **Date Format**: UK date format (DD/MM/YYYY or DD MMM YYYY)
- **Note**: Database table/column names and code variables may use US spelling for consistency with technical conventions, but all user-facing content must be UK English

## Technical Decisions

### Email Service
- **Option 1**: PHPMailer with SMTP
- **Option 2**: SendGrid/Mailgun API
- **Recommendation**: Start with PHPMailer, can upgrade to service later

### Password Hashing
- Use PHP's `password_hash()` with `PASSWORD_BCRYPT`
- Minimum 12 characters recommended

### Session Management
- Store sessions in database for multi-server compatibility
- Session timeout: 2 hours of inactivity
- Remember me: 30 days

### Domain Matching
- Exact domain match required (ramh.org.uk)
- Case-insensitive
- Subdomain support? (e.g., user@mail.ramh.org.uk) - **Decision needed**

### Seat Counting
- Only count users with `email_verified = TRUE` and `status = 'active'`
- Pending verifications don't count
- Deactivated users don't count

---

## User Roles

### Super Admin
- Full system access
- Create/manage organizations
- View all data across organizations
- Manage seat allocations
- Deactivate organizations

### Organization Admin
- **Strategic Plan Management**
  - Create and edit strategic goals
  - Create and edit projects
  - Link projects to goals
  - Manage project milestones and progress
  - View organization-wide reports
  - Full CRUD access to all organization data
- **User Management**
  - View all users in their organization
  - Promote users to admin role
  - Demote admins to user role
  - Remove users from organization (frees up seats)
  - View seat usage and availability
  - Manage user status (active/inactive)
- **Organization Settings** (Future)
  - Update organization profile
  - Manage organization preferences

### Organization User
- View organization strategic plan
- Create/edit goals and projects (permissions TBD)
- Limited to their organization's data

---

---

## Phase 3: Organization Admin Management (PLANNED)

### Overview
Enable organization administrators to fully manage their organization's strategic plans and users, including promoting/demoting users and removing staff.

### Requirements

#### 1. Organization Admin Strategic Plan Management
- **Full CRUD Access**
  - Create, edit, and delete strategic goals
  - Create, edit, and delete projects
  - Link projects to goals
  - Manage project milestones and timelines
  - Update project progress and status
  - View and generate organization reports

#### 2. Organization Admin User Management
- **User Role Management**
  - View all users in their organization
  - Promote regular users to admin role
  - Demote admins back to user role
  - Cannot modify super admin accounts
  - Cannot change their own role (safety measure)

- **User Removal** (Future Enhancement)
  - Permanently remove users from organization (not just deactivate)
  - When user is removed:
    - Their account is removed from organization (organization_id set to NULL or archived)
    - Seat is freed up for new registrations
    - Data they created remains (created_by preserved)
    - Cannot log in anymore
    - User list can decrease (not just grow)
  - Confirmation dialog before removal
  - Audit log of removals
  - **Note**: Currently users are only deactivated (status='inactive'), which frees seats but keeps them in the organization. Full removal functionality needed for organizations to reduce their user pool.

- **User Status Management**
  - Activate/deactivate users
  - View user details (email, name, role, last login)
  - See user's created goals/projects count

#### 3. Organization Admin Interface
- **User Management Page**
  - List all organization users
  - Filter by role (admin/user)
  - Filter by status (active/inactive)
  - Search by name or email
  - Actions: Promote, Demote, Remove, Activate/Deactivate

- **User Detail View**
  - User information
  - Role management controls
  - List of goals/projects created by user
  - Activity history (future)

- **Dashboard Enhancements**
  - Seat usage indicator
  - User count by role
  - Recent user activity

### Database Schema Changes

#### No new tables required
- Use existing `users` table with `role` field
- Use existing `organization_id` for filtering

#### Potential Enhancements
- Add `removed_at` timestamp to users table (soft delete)
- Add `removed_by` to track who removed the user
- Add audit log table for role changes (future)

### Implementation Plan

#### Step 1: Organization Admin User Management Interface
- [ ] Create `/admin/organization/users` page (for org admins)
- [ ] List all users in organization
- [ ] User detail view
- [ ] Role promotion/demotion functionality
- [ ] User removal functionality
- [ ] User activation/deactivation
- [ ] Search and filter functionality

#### Step 2: Permission System
- [ ] Update `requireRole()` to support 'organization_admin'
- [ ] Add `isOrgAdmin()` helper function
- [ ] Ensure org admins can only manage their own organization
- [ ] Prevent org admins from modifying super admins
- [ ] Prevent org admins from changing their own role

#### Step 3: User Management Logic
- [ ] Implement role promotion (user → admin)
- [ ] Implement role demotion (admin → user)
- [ ] Implement user removal (deactivate + free seat)
- [ ] Update seat counting after user removal
- [ ] Handle data ownership (preserve created_by)

#### Step 4: UI Components
- [ ] User list table with actions
- [ ] Role change confirmation dialogs
- [ ] User removal confirmation dialog
- [ ] Success/error flash messages
- [ ] Loading states for async operations

#### Step 5: Security & Validation
- [ ] Verify user belongs to admin's organization
- [ ] Prevent self-role modification
- [ ] Prevent super admin modification
- [ ] CSRF protection on all actions
- [ ] Rate limiting on user management actions

#### Step 6: Testing
- [ ] Test role promotion/demotion
- [ ] Test user removal
- [ ] Test seat freeing on removal
- [ ] Test permission boundaries
- [ ] Test edge cases (last admin, etc.)

### Technical Decisions

#### Role Management
- **Role Promotion**: Simple UPDATE query to change role
- **Role Demotion**: Same process, just different target role
- **User Removal**: Set status to 'inactive', don't delete record
- **Seat Freeing**: Automatic when user status changes to inactive

#### Safety Measures
- Org admins cannot remove themselves
- Org admins cannot change their own role
- Org admins cannot modify super admins
- At least one admin must remain in organization (validation)

#### Data Preservation
- When user removed, keep all their created goals/projects
- Preserve `created_by` field for audit trail
- Soft delete (status = inactive) rather than hard delete

---

## Future Enhancements (Post Phase 3)

### Strategic Plan Completeness
- [ ] Organisation-level Vision statement field
- [ ] Organisation-level Mission statement field
- [ ] Organisation-level Values section
- [ ] Strategic Plan introduction page (Vision, Mission, Values, then Goals)
- [ ] Field customisation (rename "Goals" to "Aims", etc.)
- [ ] Custom fields per organisation

### User Management & Permissions
- [ ] User permissions per goal/project
- [ ] Activity logging/audit trail
- [ ] Email notifications for project updates
- [ ] User invitation system (org admins invite users)
- [ ] Bulk user operations
- [ ] User import/export

### Reporting & Analytics
- [ ] Advanced reporting per organization
- [ ] Seat usage analytics
- [ ] Custom report builder
- [ ] Data export (PDF, Excel)

### Technical & Integration
- [ ] API access per organization
- [ ] Custom branding per organization
- [ ] Multiple domains per organization
- [ ] Billing integration (if commercial)
- [ ] PDF export of strategic plan

---

## Questions to Resolve

1. **Subdomain handling**: Should user@mail.ramh.org.uk be allowed if domain is ramh.org.uk?
   - **Recommendation**: Extract root domain, allow subdomains

2. **Seat allocation changes**: Can super admin increase seats for existing organization?
   - **Recommendation**: Yes, with audit log

3. **User removal**: What happens to data when user is removed?
   - **Recommendation**: Keep data, mark created_by as NULL or system

4. **Organization deletion**: What happens to data?
   - **Recommendation**: Soft delete, archive data

5. **Multiple organizations per user**: Should users be able to belong to multiple orgs?
   - **Recommendation**: Phase 3 feature

---

## Timeline Estimate

### Phase 2: Multi-Tenancy
- **Step 1-2**: 2-3 days (Database + Auth)
- **Step 3**: 2 days (Super Admin)
- **Step 4**: 1-2 days (Data Isolation)
- **Step 5**: 1 day (Seat Management)
- **Step 6**: 2-3 days (UI Updates)
- **Step 7**: 1 day (Email System)
- **Step 8**: 1 day (Security)
- **Step 9**: 2 days (Testing)
- **Step 10**: 1 day (Documentation)

**Phase 2 Total**: 13-16 days of development

### Phase 3: Organization Admin Management
- **Step 1**: 2 days (User Management Interface)
- **Step 2**: 1 day (Permission System)
- **Step 3**: 1 day (User Management Logic)
- **Step 4**: 1 day (UI Components)
- **Step 5**: 1 day (Security & Validation)
- **Step 6**: 1 day (Testing)

**Phase 3 Total**: 7 days of development

---

## Notes

- Keep existing functionality working during migration
- Create default "System" organization for existing data
- Migration script to assign existing data to default org
- Test thoroughly before deploying to production
- Consider backup strategy before schema changes

---

**Last Updated**: 2025-11-27
**Status**: Planning Phase
**Next Steps**: Review and approve plan, then begin Step 1
