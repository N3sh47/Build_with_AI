-- Laundry Management System schema
-- Generated to mirror Laravel migrations

CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `email` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_phone_number_unique` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `laundry_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `order_number` VARCHAR(255) NOT NULL,
  `drop_off_date` DATE NOT NULL,
  `expected_pickup_date` DATE NULL,
  `status` ENUM('received','washing','ironing','ready','picked_up','cancelled') NOT NULL DEFAULT 'received',
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `special_instructions` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `laundry_orders_order_number_unique` (`order_number`),
  KEY `laundry_orders_customer_id_foreign` (`customer_id`),
  CONSTRAINT `laundry_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `laundry_order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `laundry_order_id` BIGINT UNSIGNED NOT NULL,
  `service_name` VARCHAR(255) NOT NULL,
  `garment_description` VARCHAR(255) NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `line_total` DECIMAL(12,2) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `laundry_order_items_laundry_order_id_foreign` (`laundry_order_id`),
  CONSTRAINT `laundry_order_items_laundry_order_id_foreign` FOREIGN KEY (`laundry_order_id`) REFERENCES `laundry_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `laundry_order_statuses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `laundry_order_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('received','washing','ironing','ready','picked_up','cancelled') NOT NULL,
  `notes` TEXT NULL,
  `updated_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `laundry_order_statuses_laundry_order_id_foreign` (`laundry_order_id`),
  KEY `laundry_order_statuses_updated_by_foreign` (`updated_by`),
  CONSTRAINT `laundry_order_statuses_laundry_order_id_foreign` FOREIGN KEY (`laundry_order_id`) REFERENCES `laundry_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `laundry_order_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `laundry_order_id` BIGINT UNSIGNED NOT NULL,
  `method` ENUM('cash','mpesa') NOT NULL DEFAULT 'cash',
  `amount` DECIMAL(12,2) NOT NULL,
  `reference` VARCHAR(255) NULL,
  `mpesa_receipt` VARCHAR(255) NULL,
  `merchant_request_id` VARCHAR(255) NULL,
  `checkout_request_id` VARCHAR(255) NULL,
  `result_code` VARCHAR(255) NULL,
  `result_desc` VARCHAR(255) NULL,
  `paid_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `payments_laundry_order_id_foreign` (`laundry_order_id`),
  CONSTRAINT `payments_laundry_order_id_foreign` FOREIGN KEY (`laundry_order_id`) REFERENCES `laundry_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `expenses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `incurred_on` DATE NOT NULL,
  `recorded_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `expenses_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_user_role_id_user_id_unique` (`role_id`,`user_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
