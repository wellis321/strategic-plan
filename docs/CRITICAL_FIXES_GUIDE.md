# Critical Security Fixes - Implementation Guide

This guide provides step-by-step instructions to fix the critical security issues identified in the production readiness assessment.

---

## Fix 1: Add Authentication to API Endpoints

### File: `api/goals.php`

**Add after line 3:**
```php
require_once __DIR__ . '/../config/bootstrap.php';

// Authentication check
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Authentication required'], 401);
    exit;
}
```

### File: `api/projects.php`

**Add after line 3:**
```php
require_once __DIR__ . '/../config/bootstrap.php';

// Authentication check
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Authentication required'], 401);
    exit;
}
```

---

## Fix 2: Add Organization-Level Authorization

### File: `api/goals.php`

**Replace GET handler (lines 10-22):**
```php
case 'GET':
    $currentUser = getCurrentUser();
    $organizationId = $currentUser['organization_id'];

    $id = getQueryParam('id');
    if ($id) {
        $goal = $goalModel->getById($id);
        if (!$goal) {
            jsonResponse(['success' => false, 'error' => 'Goal not found'], 404);
        }
        // Verify goal belongs to user's organization
        if ($goal['organization_id'] != $organizationId) {
            jsonResponse(['success' => false, 'error' => 'Access denied'], 403);
        }
        jsonResponse(['success' => true, 'data' => $goal]);
    } else {
        $goals = $goalModel->getAll(['organization_id' => $organizationId]);
        jsonResponse(['success' => true, 'data' => $goals]);
    }
    break;
```

**Update POST handler (after line 31):**
```php
case 'POST':
    $postData = json_decode(file_get_contents('php://input'), true) ?? getPostData();

    if (!validateCsrfToken($postData['csrf_token'] ?? '')) {
        jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
    }

    $currentUser = getCurrentUser();
    $organizationId = $currentUser['organization_id'];

    $data = sanitizeInput($postData);
    // Force organization_id from current user, ignore any from request
    $data['organization_id'] = $organizationId;

    $errors = $goalModel->validate($data);
    // ... rest of code
```

**Update PUT handler (after line 56):**
```php
case 'PUT':
    $id = getQueryParam('id');
    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Goal ID required'], 400);
    }

    // Verify goal exists and belongs to user's organization
    $currentUser = getCurrentUser();
    $organizationId = $currentUser['organization_id'];
    $existingGoal = $goalModel->getById($id);
    if (!$existingGoal || $existingGoal['organization_id'] != $organizationId) {
        jsonResponse(['success' => false, 'error' => 'Goal not found or access denied'], 403);
    }

    $putData = json_decode(file_get_contents('php://input'), true);

    if (!validateCsrfToken($putData['csrf_token'] ?? '')) {
        jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
    }

    $data = sanitizeInput($putData);
    $data['id'] = $id;
    // Ensure organization_id cannot be changed
    $data['organization_id'] = $organizationId;

    $errors = $goalModel->validate($data);
    // ... rest of code
```

**Update DELETE handler (after line 71):**
```php
case 'DELETE':
    $id = getQueryParam('id');
    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Goal ID required'], 400);
    }

    // Verify goal exists and belongs to user's organization
    $currentUser = getCurrentUser();
    $organizationId = $currentUser['organization_id'];
    $goal = $goalModel->getById($id);
    if (!$goal || $goal['organization_id'] != $organizationId) {
        jsonResponse(['success' => false, 'error' => 'Goal not found or access denied'], 403);
    }

    $deleteData = json_decode(file_get_contents('php://input'), true);

    if (!validateCsrfToken($deleteData['csrf_token'] ?? '')) {
        jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
    }

    $goalModel->delete($id);
    jsonResponse(['success' => true, 'message' => 'Goal deleted successfully']);
    break;
```

### File: `api/projects.php`

**Apply similar changes:**
- GET handler: Filter by `organization_id`
- POST handler: Set `organization_id` from current user
- PUT handler: Verify ownership before update
- DELETE handler: Verify ownership before delete

---

## Fix 3: Secure Session Configuration

### File: `config/bootstrap.php`

**Add after line 11 (after session_start()):**
```php
// Start session after configuration is loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configure secure session cookies
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => '', // Set to your domain in production if needed
    'secure' => (APP_ENV === 'production'), // HTTPS only in production
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict' // CSRF protection
]);
```

---

## Fix 4: HTTPS Enforcement

### File: `.htaccess`

**Add at the very top (before RewriteEngine):**
```apache
# Force HTTPS in production
RewriteCond %{HTTPS} off
RewriteCond %{HTTP_HOST} !^localhost [NC]
RewriteCond %{HTTP_HOST} !^127\.0\.0\.1 [NC]
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

RewriteEngine On
```

**Note:** This allows localhost to work over HTTP for development, but forces HTTPS for production domains.

---

## Fix 5: Secure File Upload Permissions

### File: `pages/organization/settings.php`

**Change line 32:**
```php
// OLD:
mkdir($uploadDir, 0775, true);

// NEW:
mkdir($uploadDir, 0755, true);
```

**Add after line 38 (after move_uploaded_file):**
```php
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    $errors[$field] = "Unable to save {$label}. Please try again.";
    return $existingPath;
}

// Set secure file permissions (non-executable)
chmod($destination, 0644);
```

### File: `pages/top-sections/new.php`

**Change line 65:**
```php
// OLD:
mkdir($uploadDir, 0775, true);

// NEW:
mkdir($uploadDir, 0755, true);
```

**Add after line 69 (after move_uploaded_file):**
```php
if (move_uploaded_file($file['tmp_name'], $destination)) {
    chmod($destination, 0644); // Set secure permissions
    $imagePath = 'static/uploads/organizations/' . $organizationId . '/top-sections/' . $filename;
}
```

### File: `pages/top-sections/edit.php`

**Change line 67:**
```php
// OLD:
mkdir($uploadDir, 0775, true);

// NEW:
mkdir($uploadDir, 0755, true);
```

**Add after line 78 (after move_uploaded_file):**
```php
if (move_uploaded_file($file['tmp_name'], $destination)) {
    chmod($destination, 0644); // Set secure permissions
    $imagePath = 'static/uploads/organizations/' . $organizationId . '/top-sections/' . $filename;
}
```

---

## Fix 6: Add Missing Security Headers

### File: `.htaccess`

**Update the mod_headers section (lines 9-13):**
```apache
# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.quilljs.com https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.quilljs.com https://cdn.tailwindcss.com; img-src 'self' data:; font-src 'self' data:;"
</IfModule>
```

**Note:** Adjust Content-Security-Policy based on your actual CDN usage. The above assumes Quill.js and Tailwind CSS from CDN.

---

## Fix 7: Fix CORS Configuration

### File: `index.php`

**Change line 50-52:**
```php
// OLD:
if ($ext === 'js') {
    header('Access-Control-Allow-Origin: *');
}

// NEW:
// Remove CORS header or restrict to specific domain
// if ($ext === 'js' && APP_ENV === 'production') {
//     header('Access-Control-Allow-Origin: https://yourdomain.com');
// }
```

### File: `router.php`

**Change line 53-55:**
```php
// OLD:
if ($ext === 'js') {
    header('Access-Control-Allow-Origin: *');
}

// NEW:
// Remove CORS header or restrict to specific domain
// if ($ext === 'js' && APP_ENV === 'production') {
//     header('Access-Control-Allow-Origin: https://yourdomain.com');
// }
```

---

## Fix 8: Create .env.example File

### Create file: `.env.example`

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=strategic_plan
DB_USER=your_db_user
DB_PASS=your_db_password
DB_PORT=3306

# Application Configuration
APP_NAME="Strategic Plan Manager"
APP_URL=http://localhost:8000
APP_ENV=development

# Design System Configuration
DESIGN_SYSTEM=tailwind

# Session Configuration
SESSION_LIFETIME=7200

# Email Configuration
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
SMTP_USER=your_email@outlook.com
SMTP_PASS=your_app_password
SMTP_FROM_EMAIL=your_email@outlook.com
SMTP_FROM_NAME="Strategic Plan Manager"
CONTACT_EMAIL=your_email@outlook.com
```

---

## Testing Checklist

After implementing fixes, test:

- [ ] API endpoints return 401 when not logged in
- [ ] API endpoints only return data from user's organization
- [ ] Cannot modify other organizations' data via API
- [ ] Session cookies have HttpOnly, Secure, SameSite flags
- [ ] HTTPS redirect works (test with non-localhost domain)
- [ ] File uploads have correct permissions (0644)
- [ ] Security headers present in response
- [ ] CORS headers removed or restricted
- [ ] `.env.example` file created

---

## Quick Test Script

Create `test_security.php` in project root (remove after testing):

```php
<?php
require_once 'config/bootstrap.php';

echo "Session Cookie Params:\n";
print_r(session_get_cookie_params());

echo "\n\nSecurity Headers Test:\n";
echo "Run: curl -I https://yourdomain.com\n";
echo "Check for: X-Content-Type-Options, X-Frame-Options, Strict-Transport-Security\n";

echo "\n\nAPI Authentication Test:\n";
echo "Test without login:\n";
echo "curl http://yourdomain.com/api/goals\n";
echo "Should return 401\n";
```

---

## Notes

1. **Test in development first** before deploying to production
2. **Backup your database** before making changes
3. **Test each fix individually** to ensure nothing breaks
4. **Update APP_ENV to production** only after all fixes are tested
5. **Remove test files** before going live

---

**After implementing these fixes, your application will be significantly more secure and ready for production deployment.**
