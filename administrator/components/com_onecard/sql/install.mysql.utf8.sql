CREATE TABLE IF NOT EXISTS `#__onecard_brand` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`title` VARCHAR(225) NOT NULL ,
`phone` VARCHAR(225) NOT NULL ,
`address` VARCHAR(225) NOT NULL ,
`description` TEXT NOT NULL ,
`ncc` VARCHAR(225) NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_code` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`code` VARCHAR(225) NOT NULL ,
`barcode` VARCHAR(225) NOT NULL ,
`expired` datetime NOT NULL ,
`voucher` VARCHAR(225) NOT NULL ,
`status` VARCHAR(225) NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_contract` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`title` VARCHAR(225) NOT NULL ,
`date_issue` datetime NOT NULL ,
`description` TEXT NOT NULL ,
`ncc` VARCHAR(225) NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_event` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`title` VARCHAR(225) NOT NULL ,
`description` TEXT NOT NULL ,
`partner` VARCHAR(225) NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_export_voucher` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`event` VARCHAR(225) NOT NULL ,
`expired` datetime NOT NULL ,
`voucher` VARCHAR(225) NOT NULL ,
`price` REAL NOT NULL ,
`created` datetime NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_ncc` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`created` datetime NOT NULL ,
`title` VARCHAR(225) NOT NULL ,
`address` VARCHAR(225) NOT NULL ,
`phone` VARCHAR(225) NOT NULL ,
`description` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_order` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`date` datetime NOT NULL ,
`user_id` TEXT NOT NULL ,
`subtotal` REAL NOT NULL ,
`total` REAL NOT NULL ,
`onecard_point` REAL NOT NULL ,
`vpoint` REAL NOT NULL ,
`vpoint_client` REAL NOT NULL ,
`coupon_code` VARCHAR(225) NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_order_voucher` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`order_id` VARCHAR(225) NOT NULL ,
`voucher_id` VARCHAR(225) NOT NULL ,
`quantity` REAL NOT NULL ,
`price` REAL NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_partner` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`title` VARCHAR(225) NOT NULL ,
`description` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__onecard_voucher` (
`id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
`state` TINYINT(1) NOT NULL ,
`ordering` INT(11) NOT NULL ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`created_by` VARCHAR(225) NOT NULL ,
`modified_by` VARCHAR(225) NOT NULL ,
`title` VARCHAR(225) NOT NULL ,
`value` REAL NOT NULL ,
`input_price` REAL NOT NULL ,
`expired` datetime NOT NULL ,
`description` TEXT NOT NULL ,
`brand` VARCHAR(225) NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

