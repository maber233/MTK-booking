-- Fix user activation configuration for email support
-- This ensures users can request activation emails

-- Set user activation to 'email' mode
INSERT INTO bs_options (`key`, `value`, `locale`) 
VALUES ('service.user.activation', 'email', NULL)
ON DUPLICATE KEY UPDATE `value` = 'email';

-- Also ensure that some other essential options are set with reasonable defaults
INSERT INTO bs_options (`key`, `value`, `locale`) 
VALUES 
  ('service.user.registration', 'true', NULL),
  ('client.name.short', 'MTK', NULL),
  ('client.name.full', 'MTK Booking System', NULL),
  ('service.name.full', 'Booking System', NULL),
  ('client.contact.email', 'info@bookings.example.com', NULL),
  ('service.website', 'https://mtk-booking-production.up.railway.app', NULL)
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);
