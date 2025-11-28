# Strategic Plan Manager

A PHP/MySQL application for managing strategic plans, goals, and projects within organizations. Originally migrated from a SvelteKit application, this system provides a comprehensive solution for strategic planning and progress tracking.

## Features

- **Strategic Goals Management**: Create and manage organizational strategic goals
- **Project Management**: Track projects linked to strategic goals with timelines, leads, and milestones
- **Progress Tracking**: Monitor project progress with milestone tracking and reporting
- **Flexible Design System**: Switch between Scottish Government Design System (SGDS) and Tailwind CSS
- **Responsive Interface**: Works on desktop and mobile devices
- **Multi-tenant Ready**: Designed to work for any organization
- **Local & Cloud Deployment**: Runs locally or on hosting platforms like Hostinger

## Design Systems

### Scottish Government Design System (SGDS)
The application includes full support for the Scottish Government Design System, providing:
- Consistent government-standard UI components
- Accessibility-compliant design
- Professional appearance for government organizations

### Tailwind CSS
Alternative modern utility-first CSS framework support:
- Rapid UI development
- Highly customizable
- Modern design patterns

### Easy Switching
Switch between design systems by updating the `DESIGN_SYSTEM` environment variable:
- `DESIGN_SYSTEM=sgds` for Scottish Government Design System
- `DESIGN_SYSTEM=tailwind` for Tailwind CSS

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Design Systems**: SGDS, Tailwind CSS
- **Architecture**: MVC pattern with custom routing

## Quick Start

### 1. Clone and Setup
```bash
git clone <repository-url>
cd strategic-plan
cp env.example .env
```

### 2. Configure Database
Edit `.env` with your database credentials:
```env
DB_HOST=localhost
DB_NAME=strategic_plan
DB_USER=root
DB_PASS=your_password
```

### 3. Import Database Schema
```bash
mysql -u root -p strategic_plan < database/schema.sql
```

### 4. Run Locally
```bash
php -S localhost:8000 -t . index.php
```

Visit `http://localhost:8000` to access the application.

## Project Structure

```
strategic-plan/
├── api/                    # API endpoints
├── classes/               # Core PHP classes
│   ├── Database.php       # Database connection and operations
│   ├── Goal.php          # Goal management
│   ├── Project.php       # Project management
│   └── DesignSystem.php  # UI framework abstraction
├── config/               # Configuration files
├── database/             # Database schema and migrations
├── includes/             # Utility functions
├── pages/               # Application pages
│   ├── dashboard.php    # Main dashboard
│   ├── projects/        # Project management pages
│   ├── goals/          # Goal management pages
│   └── reports/        # Reporting pages
├── static/             # Static assets (CSS, JS, images)
├── templates/          # Page templates
├── .env               # Environment configuration
└── index.php          # Application entry point
```

## Core Features

### Dashboard
- Overview of all projects and goals
- Progress summaries and statistics
- Quick access to recent projects
- At-a-glance status indicators

### Project Management
- Create and edit projects
- Link projects to strategic goals
- Track project leads and team members
- Manage project timelines and milestones
- Monitor progress with percentage completion

### Goal Management
- Define strategic goals with descriptions
- Assign responsible directors
- Track goal statements and objectives
- View associated projects per goal

### Reporting
- Project progress reports
- Goal achievement tracking
- Timeline and milestone analysis
- Export capabilities

## Database Schema

### Goals Table
- Strategic goals with numbers, titles, and descriptions
- Responsible directors
- Associated goal statements

### Projects Table
- Project details linked to goals
- Timeline tracking (start/end dates)
- Project groups and meeting frequencies
- Unique project numbers and slugs

### Supporting Tables
- Project leads and team members
- Project purposes and objectives
- Milestones with status tracking
- Progress reports with status updates

## API Endpoints

### Projects API (`/api/projects`)
- `GET` - List all projects with filtering
- `POST` - Create new project
- `PUT` - Update existing project
- `DELETE` - Remove project

### Goals API (`/api/goals`)
- `GET` - List all goals
- `POST` - Create new goal
- `PUT` - Update existing goal
- `DELETE` - Remove goal

## Security Features

- CSRF protection on all forms
- Input sanitization and validation
- SQL injection prevention with prepared statements
- Session management
- Environment-based configuration
- Security headers for production deployment

## Deployment Options

### Local Development
- PHP built-in server
- Apache with mod_rewrite
- Nginx with PHP-FPM

### Production Hosting
- Shared hosting (Hostinger, etc.)
- VPS/Dedicated servers
- Cloud platforms

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions.

## Customisation

### Adding New Design Systems
1. Extend the `DesignSystem` class
2. Add CSS/JS files to `/static`
3. Update environment configuration

### Custom Fields
1. Modify database schema
2. Update model classes
3. Adjust form templates

### Branding
1. Update `APP_NAME` in environment
2. Replace logo and favicon in `/static`
3. Customise CSS for brand colours

## Browser Support

- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source. Please check the license file for details.

## Support

For deployment assistance or customisation needs:
1. Check the [DEPLOYMENT.md](DEPLOYMENT.md) guide
2. Review the troubleshooting section
3. Examine error logs for specific issues

## Migration from SvelteKit

This application was successfully migrated from a SvelteKit/MongoDB stack to PHP/MySQL while maintaining:
- All original functionality
- Scottish Government Design System compatibility
- Data structure and relationships
- User interface consistency

The migration provides better hosting compatibility and easier maintenance for organizations without Node.js expertise.