-- Create balance_history table.
CREATE TABLE IF NOT EXISTS `balance_history` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `value`      DECIMAL(13, 4)   DEFAULT NULL,
  `account_id` INT(11)          DEFAULT NULL,
  `order_id`   INT(11)          DEFAULT NULL,
  `site_id`    INT(11)          DEFAULT NULL,
  `partner_id` INT(11)          DEFAULT NULL,
  `ref`        INT(19)          DEFAULT NULL,
  `created_at` DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX_ACCOUNT_ID` (`account_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Create balance table.
CREATE TABLE IF NOT EXISTS `balance` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11)          DEFAULT NULL,
  `value`      DECIMAL(13, 4)   DEFAULT NULL,
  `updated_at` DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;