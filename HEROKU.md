# Heroku Deployment Guide

## Prerequisites
- Heroku CLI installed
- Git repository

## Steps

1. **Login to Heroku:**
   ```bash
   heroku login
   ```

2. **Create Heroku app:**
   ```bash
   heroku create your-booking-system-name
   ```

3. **Add MySQL database:**
   ```bash
   heroku addons:create cleardb:ignite
   ```

4. **Get database URL:**
   ```bash
   heroku config:get CLEARDB_DATABASE_URL
   ```

5. **Set PHP version in composer.json:**
   ```json
   {
     "require": {
       "php": "^8.1.0"
     }
   }
   ```

6. **Deploy:**
   ```bash
   git add .
   git commit -m "Prepare for Heroku deployment"
   git push heroku master
   ```

7. **Setup database:**
   - Visit `your-app.herokuapp.com/setup.php`
   - Complete setup wizard
   - Remove setup.php: `heroku run rm public/setup.php`

## Environment Variables

Set these in Heroku dashboard or via CLI:
```bash
heroku config:set DATABASE_HOST=your-host
heroku config:set DATABASE_NAME=your-db-name
heroku config:set DATABASE_USER=your-user
heroku config:set DATABASE_PASSWORD=your-password
```
