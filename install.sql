CREATE TABLE `srt_clicks` (
	`id` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;


CREATE TABLE `srt_links` (
	`id` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`link` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`expires` TIMESTAMP NULL,
	`delete_key` VARCHAR(20) NOT NULL COLLATE 'utf8_general_ci',
	`reported` TINYINT(1) NOT NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `id` (`id`) USING BTREE,
	UNIQUE INDEX `delete_key` (`delete_key`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
