# Copilot Instructions for TrueCare Donation Portal

## Project Overview
TrueCare is a PHP-based donation platform connecting donors with government-verified orphanages in Kenya. The system emphasizes transparency, real-time tracking, and secure payments.

## Architecture & Key Components
- **Entry Points**: `index.php`, `login.php`, `register.php` handle public access and authentication.
- **Core Logic**: Located in `src/`:
  - `src/auth/` manages user authentication, dashboards, and donation history.
  - `src/admin/` provides admin tools for user and orphanage verification.
  - `src/campaigns/` handles campaign creation, listing, and details.
  - `src/donations/` manages donation flows and success pages.
- **Includes**: Shared logic and layout in `includes/` (`header.php`, `footer.php`, `auth.php`, etc.).
- **Database**: SQL schema in `database/truecare_portal.sql`. Uses MySQL, configured via `includes/config.php`.
- **Assets**: Static files in `assets/` (CSS, JS, images).

## Developer Workflows
- **Local Development**: Place code in a local Apache/MySQL/PHP stack (e.g., XAMPP, WAMP). No build step required.
- **Database Setup**: Import `database/truecare_portal.sql` into MySQL. Update credentials in `includes/config.php`.
- **Debugging**: Use `error_log()` or direct output for debugging. No integrated test suite detected.
- **Authentication**: Session-based, logic in `includes/auth.php` and `src/auth/login_process.php`.

## Project-Specific Patterns
- **Role-Based Dashboards**: Dashboard content is split by user role in `src/auth/dashboard_parts/`.
- **Verification Flows**: Admin verification for orphanages and users in `src/admin/verify_orphanages.php` and related files.
- **Campaigns & Donations**: Campaigns are managed in `src/campaigns/`, donations in `src/donations/`.
- **Includes Usage**: All pages use `includes/header.php` and `includes/footer.php` for layout consistency.

## Integration Points
- **Payment Gateways**: M-Pesa, PayPal, and card payments (integration code may be in `src/donations/` or `src/campaigns/`).
- **External APIs**: Check for API usage in campaign and donation flows.

## Conventions
- **File Naming**: Lowercase, underscores for multi-word files.
- **Session Management**: PHP sessions for authentication and user state.
- **No Frameworks**: Project uses raw PHP, not Laravel/Symfony.

## Examples
- To add a new dashboard feature, extend the relevant file in `src/auth/dashboard_parts/` and update `src/auth/dashboard.php`.
- For new admin tools, add to `src/admin/` and link from `admin.php`.

---
_If any section is unclear or missing, please provide feedback for improvement._
