CREATE TABLE IF NOT EXISTS `User Currency Favorites` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `currency_id` INT NOT NULL,
    `base_currency` VARCHAR(10) NOT NULL,
    `unit` VARCHAR(10) NOT NULL,
    `XAU` DECIMAL(15, 5) NOT NULL,
    `XAG` DECIMAL(15, 5) NOT NULL,
    `PA` DECIMAL(15, 5) NOT NULL,
    `PL` DECIMAL(15, 5) NOT NULL,
    `GBP` DECIMAL(15, 5) NOT NULL,
    `EUR` DECIMAL(15, 5) NOT NULL,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_api` tinyint(1) DEFAULT '1',
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`), -- Assuming there is a Users table
    FOREIGN KEY (`currency_id`) REFERENCES `Currency`(`id`) -- Assuming there is a Currency table
);
