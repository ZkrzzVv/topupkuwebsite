USE `topupku`;

-- OPTIONAL / DESTRUCTIVE:
-- Backup database first.
-- This removes all transaction and product records so the catalog starts empty.
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `transactions`;
TRUNCATE TABLE `products`;
SET FOREIGN_KEY_CHECKS = 1;
