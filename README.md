# BK Delivery - Courier Tracking System

A comprehensive courier tracking and delivery management system built with PHP and MySQL for delivery services in Batam.

## Features

- **Multi-role Access Control**
  - Administrator dashboard with full system oversight
  - Courier interface for package management
  - Public tracking interface for customers

- **Package Management**
  - Package pickup registration
  - Real-time delivery tracking
  - Status updates (PROSES, PENDING, SUKSES, CANCEL)
  - Excel export functionality

- **Courier Management**
  - Courier registration and approval system
  - Profile management with photo uploads
  - Performance tracking

## System Requirements

- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Composer (for PHPSpreadsheet)
- Node.js (for theme dependencies)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd batamkurir
   ```

2. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u username -p database_name < config/db_bk_delivery.sql
   ```

3. **Configure Database**
   - Copy and configure `config/conf.php` with your database credentials
   - Copy and configure `config/db.php` for direct connections

4. **Install Dependencies**
   ```bash
   # Install theme dependencies
   cd theme
   npm install
   
   # Install PHP dependencies
   composer install
   ```

5. **Set Permissions**
   ```bash
   # Ensure upload directory is writable
   chmod 755 theme/dist/img/kurir/
   ```

## Project Structure

```
├── adm/           # Administrator interface
├── kurir/         # Courier interface
├── tracking/      # Public tracking interface
├── config/        # Database configuration
├── theme/         # AdminLTE theme assets
├── login/         # Authentication system
└── register/      # User registration
```

## User Roles

### Administrator (`/adm/`)
- Full system access and oversight
- Courier approval and management
- Delivery monitoring and reports
- System configuration

### Kurir (Courier) (`/kurir/`)
- Package pickup management
- Delivery status updates
- Profile management
- Route optimization

### Public (`/tracking/`)
- Order tracking by tracking number
- Delivery status checking
- Basic package information

## Database Schema

### Core Tables
- `mst_kurir` - Courier registration and profiles
- `dlv_pickup` - Package pickup records
- `trx_delivery` - Delivery transactions and tracking

### Status Flow
- `PROSES` - Package in transit
- `PENDING` - Awaiting action
- `SUKSES` - Successfully delivered
- `CANCEL` - Cancelled delivery

## Technology Stack

- **Backend**: PHP with MySQLi
- **Frontend**: AdminLTE 3.2.0, Bootstrap 4.6.1, jQuery 3.6.0
- **Database**: MySQL
- **Additional Libraries**: 
  - PHPSpreadsheet (Excel exports)
  - SweetAlert2 (notifications)
  - Select2 (enhanced dropdowns)
  - Toastr (toast notifications)

## Security Features

- Input sanitization with `mysqli_real_escape_string`
- HTML output filtering with `htmlspecialchars`
- Cookie-based session management
- Role-based access control
- Image compression for uploads

## Development

The system uses direct MySQLi connections without frameworks or ORMs. AJAX endpoints are located in `proses/` subdirectories for each module.

### Key Utility Functions
- `your_filter()` - Input sanitization
- `generateAlphaNumericCode()` - Tracking number generation
- `compressImage()` - Image optimization

## License

This project is proprietary software for BK Delivery services.

## Support

For technical support or feature requests, please contact the development team.
