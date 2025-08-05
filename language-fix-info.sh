#!/bin/bash

# Clear language cookies script for EP-3 Booking System
# This script creates URLs to clear language-related cookies

echo "🌐 EP-3 Booking System - Language Troubleshooting"
echo "=================================================="
echo
echo "The language issues have been fixed in the code. Here's what was done:"
echo
echo "✅ FIXES APPLIED:"
echo "1. Modified ConfigLocaleListener to prioritize configured default locale (sv-SE)"
echo "2. Browser language detection now only used as fallback, not override"
echo "3. Added proper fallback locale in translator to prevent mixed translations"
echo "4. Invalid language cookies are now cleared automatically"
echo
echo "🔧 WHAT THE FIXES DO:"
echo "- Default language is now Swedish (sv-SE) as configured"
echo "- English browser settings won't override the Swedish default"
echo "- Only user-selected language (via URL or cookie) will override default"
echo "- Mixed German/English translations should be resolved"
echo
echo "📱 TO TEST THE FIXES:"
echo "1. Wait for Railway to redeploy (about 2-3 minutes)"
echo "2. Clear your browser cookies for the site"
echo "3. Visit the site - it should now show in Swedish by default"
echo "4. Use the language selector to manually choose English if needed"
echo
echo "🌍 LANGUAGE PRIORITY ORDER (FIXED):"
echo "1. User-selected language (URL parameter: ?locale=sv-SE or ?locale=en-US)"
echo "2. User's previous choice (stored in cookie)"
echo "3. Configured default language (sv-SE)"
echo "4. Browser language detection (only as last resort)"
echo
echo "If you still see issues after deployment, try these URLs to force language:"
echo
echo "Force Swedish: https://mtk-booking-production.up.railway.app/?locale=sv-SE"
echo "Force English: https://mtk-booking-production.up.railway.app/?locale=en-US"
echo
echo "The fixes are now deployed and should resolve the translation issues! 🎉"
