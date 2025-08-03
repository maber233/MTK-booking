# EP-3 Booking System - Deployment Guide

## Quick Deploy Options

### Option 1: Railway (Recommended)
1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub
3. Click "Deploy from GitHub repo"
4. Select this repository
5. Add MySQL database from Railway's database tab
6. Set environment variables (see below)

### Option 2: Heroku
1. Install Heroku CLI
2. Run: `heroku create your-app-name`
3. Add ClearDB MySQL: `heroku addons:create cleardb:ignite`
4. Deploy: `git push heroku master`

### Option 3: Traditional PHP Hosting
Upload to any PHP hosting provider that supports:
- PHP 8.1+
- MySQL 5+
- Apache with mod_rewrite

## Required Environment Variables

Set these in your hosting platform:

```
DATABASE_HOST=your-mysql-host
DATABASE_NAME=your-database-name
DATABASE_USER=your-mysql-user
DATABASE_PASSWORD=your-mysql-password
DATABASE_PORT=3306
```

## Post-Deployment Setup

1. Copy configuration files:
   - `config/init.php.dist` → `config/init.php`
   - `config/autoload/local.php.dist` → `config/autoload/local.php`
   - `public/.htaccess_original` → `public/.htaccess`

2. Update database credentials in `config/autoload/local.php`

3. Run setup: visit `your-domain.com/setup.php`

4. Delete setup file after completion

## File Permissions

Ensure write permissions for:
- `data/cache/`
- `data/log/`
- `data/session/`
- `public/docs-client/upload/`
- `public/imgs-client/upload/`

## Security Notes

- Set document root to `/public` directory
- Remove `public/setup.php` after setup
- Clear `data/cache/` after deployment
