# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is **BK Delivery** - a courier tracking and delivery management system built with PHP and MySQL. The system manages courier registration, package pickup, delivery tracking, and administrative oversight for a delivery service in Batam.

## Architecture

The application follows a role-based architecture with three main user types:
- **Administrator** (`/adm/`) - Full system access, courier approval, delivery oversight
- **Kurir (Courier)** (`/kurir/`) - Package pickup and delivery management  
- **Public** (`/tracking/`) - Order tracking interface

### Core Components

**Database Configuration:**
- Main config: `config/conf.php` (uses Trophis namespace with MySQLi)
- Local config: `config/db.php` (direct MySQLi connection with utility functions)
- Schema: `config/db_bk_delivery.sql`

**Authentication & Sessions:**
- Cookie-based sessions using `BK-DELIVERY` cookie with JSON role data
- Roles: `Administrator`, `Kurir`
- Session routing handled in root `index.php`

**Frontend Framework:**
- AdminLTE v3.2.0 theme located in `/theme/`
- Bootstrap 4.6.1, jQuery 3.6.0, SweetAlert2, Select2, Toastr
- Shared theme components in each section's `theme/` folder

**Data Models:**
- `mst_kurir` - Courier registration and profiles
- `dlv_pickup` - Package pickup records  
- `trx_delivery` - Delivery transactions and tracking
- Status tracking: `PROSES`, `PENDING`, `SUKSES`, `CANCEL`

## Development Commands

**Frontend Dependencies:**
```bash
cd theme
npm install                    # Install AdminLTE theme dependencies
composer install               # Install PHPSpreadsheet for Excel exports
```

**Database Setup:**
```bash
# Import the database schema
mysql -u username -p database_name < config/db_bk_delivery.sql
```

## Key File Structure

```
/adm/           - Administrator interface
/kurir/         - Courier interface  
/config/        - Database and configuration
/theme/         - AdminLTE theme assets
/login/         - Authentication system
/register/      - User registration
/tracking/      - Public tracking interface
```

## Database Functions

The system includes several utility functions in `config/db.php`:
- `your_filter($koneksi, $value)` - Input sanitization with htmlspecialchars and mysqli_real_escape_string
- `generateAlphaNumericCode($length)` - Random code generation for tracking numbers
- `compressImage($source, $destination, $quality)` - Image compression for courier profile photos

## Security Considerations

- All database queries use mysqli_real_escape_string for input sanitization
- HTML output is filtered with htmlspecialchars  
- Direct SQL queries are used throughout (no ORM)
- File uploads for courier photos stored in `theme/dist/img/kurir/`
- Session data stored in cookies as JSON

## Development Notes

- Uses direct MySQLi connections, no framework or ORM
- AJAX endpoints in `proses/` subdirectories for each module
- Pagination implemented manually with URL parameters
- All dates use Asia/Jakarta timezone
- Export functionality uses PHPSpreadsheet for Excel files
- SweetAlert2 used for confirmations and notifications