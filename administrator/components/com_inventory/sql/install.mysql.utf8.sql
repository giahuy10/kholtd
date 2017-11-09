CREATE TABLE IF NOT EXISTS `#__inventory_voucher` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`code` VARCHAR(255)  NOT NULL ,
`value` VARCHAR(255)  NOT NULL ,
`merchent` INT(11)  NOT NULL ,
`expired` DATE NOT NULL ,
`event` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

