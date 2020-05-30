<?php


class Install {
	private static function tableClicks() {
		return (Flight::has('shortener.db_prefix') ? Flight::get('shortener.db_prefix') : '') . 'clicks';
	}

	private static function tableLinks() {
		return (Flight::has('shortener.db_prefix') ? Flight::get('shortener.db_prefix') : '') . 'links';
	}

	private static function createClicksTable () {
		Flight::db()->create(self::tableClicks(), [
			'id' => [
				'VARCHAR(255)',
				'NOT NULL'
			],
			'date' => [
				'TIMESTAMP',
				'NOT NULL',
				'DEFAULT CURRENT_TIMESTAMP'
			]
		]);
	}

	private static function createLinksTable () {
		Flight::db()->create(self::tableLinks(), [
			'id' => [
				'VARCHAR(' . Flight::get('shortener.id_length') . ')',
				'NOT NULL'
			],
			'link' => [
				'TEXT',
				'NOT NULL'
			],
			'expires' => [
				'TIMESTAMP',
				'NOT NULL'
			],
			'delete_key' => [
				'VARCHAR(' . Flight::get('shortener.delete_key_length') . ')',
				'NOT NULL'
			],
			'reported' => [
				'TINYINT(1)',
				'NOT NULL',
				'DEFAULT 0'
			],
			'created' => [
				'TIMESTAMP',
				'NOT NULL',
				'DEFAULT CURRENT_TIMESTAMP'
			],
			'UNIQUE INDEX <id> (<id>)',
			'UNIQUE INDEX <delete_key> (<delete_key>)',
		]);
	}

	public static function tryInstall() {
		self::createClicksTable();
		self::createLinksTable();
	}
}