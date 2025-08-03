#!/bin/bash

echo "🚀 EP-3 Booking System - Quick Setup"
echo "====================================="

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p data/cache data/log data/session public/docs-client/upload public/imgs-client/upload

# Set permissions (for Unix-like systems)
echo "🔒 Setting permissions..."
chmod 755 data/cache data/log data/session public/docs-client/upload public/imgs-client/upload

# Copy configuration files
echo "⚙️  Setting up configuration files..."

if [ ! -f "config/init.php" ]; then
    cp config/init.php.dist config/init.php
    echo "✅ Created config/init.php"
fi

if [ ! -f "config/autoload/local.php" ]; then
    cp config/autoload/local.php.dist config/autoload/local.php
    echo "✅ Created config/autoload/local.php"
    echo "⚠️  Please edit config/autoload/local.php with your database credentials"
fi

if [ ! -f "public/.htaccess" ]; then
    cp public/.htaccess_original public/.htaccess
    echo "✅ Created public/.htaccess"
fi

# Install dependencies
echo "📦 Installing dependencies..."
if command -v composer &> /dev/null; then
    composer install --ignore-platform-reqs
    echo "✅ Dependencies installed"
else
    echo "⚠️  Composer not found. Please install dependencies manually:"
    echo "   composer install --ignore-platform-reqs"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Edit config/autoload/local.php with your database credentials"
echo "2. Visit your-domain.com/setup.php to initialize the database"
echo "3. Delete public/setup.php after setup is complete"
echo ""
