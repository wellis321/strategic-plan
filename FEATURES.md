# Strategic Plan Manager - Features & Roadmap

## ✅ Completed Features

### Core Application
- [x] PHP/MySQL application structure
- [x] Database schema for goals and projects
- [x] CRUD operations for goals
- [x] CRUD operations for projects
- [x] Project milestones tracking
- [x] Progress reporting
- [x] Dashboard with summaries
- [x] Scottish Government Design System (SGDS) integration
- [x] Switchable design systems (SGDS/Tailwind)
- [x] Responsive design
- [x] Local development setup
- [x] Hostinger deployment ready
- [x] URL routing system
- [x] Static file serving

---

## 🚧 In Progress

None currently.

---

## 📋 Planned Features

### Phase 2: Multi-Tenancy & User Management

#### Super Admin Features
- [ ] Super admin login/authentication
- [ ] Create organizations
- [ ] Manage organization details
- [ ] Allocate seats per domain
- [ ] View organization usage statistics
- [ ] Activate/deactivate organizations
- [ ] View all users across organizations

#### Organization Management
- [ ] Organization registration (by super admin)
- [ ] Domain-based organization identification
- [ ] Organization settings page
- [ ] Organization status management

#### User Authentication & Registration
- [ ] User registration form
- [ ] Domain validation (email must match organization domain)
- [ ] Email verification system
- [ ] Verification email sending
- [ ] Verification token management
- [ ] Login system
- [ ] Logout functionality
- [ ] Password reset
- [ ] Remember me functionality
- [ ] Session management

#### Seat Management
- [ ] Seat allocation per domain
- [ ] Seat counting (verified users only)
- [ ] Registration blocking when seats full
- [ ] Seat release on user deactivation
- [ ] Seat usage display
- [ ] Seat limit warnings

#### Multi-Tenant Data Isolation
- [ ] Organization-scoped data queries
- [ ] User can only access their organization's data
- [ ] Organization context in all pages
- [ ] Data migration for existing content

### Phase 3: Organization Admin Management

#### Organization Admin Strategic Plan Management
- [ ] Full CRUD access to goals and projects
- [ ] Create and edit strategic goals
- [ ] Create and edit projects
- [ ] Link projects to goals
- [ ] Manage project milestones
- [ ] Update project progress
- [ ] View organization reports

#### Organization Admin User Management
- [ ] View all users in organization
- [ ] Promote users to admin role
- [ ] Demote admins to user role
- [ ] Remove users from organization
- [ ] Activate/deactivate users
- [ ] View user details and activity
- [ ] Search and filter users
- [ ] Seat usage monitoring

#### User Interface
- [ ] Registration page
- [ ] Login page
- [ ] Email verification page
- [ ] Password reset pages
- [ ] User profile page
- [ ] Organization dashboard
- [ ] Super admin dashboard
- [ ] Protected routes middleware

#### Email System
- [ ] SMTP configuration
- [ ] Email templates
- [ ] Verification emails
- [ ] Password reset emails
- [ ] Welcome emails

---

## 🔮 Future Enhancements

### User Permissions
- [ ] Role-based access control (RBAC)
- [ ] Project-level permissions
- [ ] Goal-level permissions
- [ ] Custom permission sets

### Advanced Features
- [ ] Activity logging/audit trail
- [ ] Email notifications for updates
- [ ] Advanced reporting per organization
- [ ] Data export (CSV/PDF)
- [ ] Custom branding per organization
- [ ] API access per organization
- [ ] Webhook support

### Analytics & Reporting
- [ ] Organization usage analytics
- [ ] Seat utilization reports
- [ ] Project completion rates
- [ ] Goal achievement tracking
- [ ] Custom report builder

### Integration
- [ ] Calendar integration
- [ ] Email integration
- [ ] Third-party authentication (OAuth)
- [ ] SSO support

---

## 📊 Feature Status Legend

- ✅ **Completed** - Feature is implemented and tested
- 🚧 **In Progress** - Currently being developed
- 📋 **Planned** - Scheduled for development
- 🔮 **Future** - Under consideration for future releases

---

## Quick Reference

- **Project Plan**: See `PROJECT_PLAN.md` for detailed implementation plan
- **Deployment**: See `DEPLOYMENT.md` for setup instructions
- **Overview**: See `overview.txt` for project background

---

**Last Updated**: 2025-11-27
**Current Phase**: Phase 2 (Multi-Tenancy) - In Progress
**Next Phase**: Phase 3 (Organization Admin Management)
