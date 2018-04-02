CREATE TABLE IF NOT EXISTS `#__gift_item` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`sku` VARCHAR(255)  NOT NULL ,
`manufacturer` INT(11)  NOT NULL ,
`quantity` VARCHAR(255)  NOT NULL ,
`price` VARCHAR(255)  NOT NULL ,
`detail` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

