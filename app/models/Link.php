<?php


class Link {
	private static function tableClicks() {
		return (Flight::has('shortener.db_prefix') ? Flight::get('shortener.db_prefix') : '') . 'clicks'; 
	}

	private static function tableLinks() {
		return (Flight::has('shortener.db_prefix') ? Flight::get('shortener.db_prefix') : '') . 'links';
	}
	
	private static function checkID ($id) {
		if (in_array($id, Flight::get('shortener.reserved_ids')))
			return true;

		$result = Flight::db()->select(self::tableLinks(), 'id', [
			'id' => $id,
		]);

		if (count($result) > 0)
			return true;

		return false;
	}

	private static function generateID () {
		do {
			$id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, Flight::get('shortener.id_length'));
		} while (self::checkID($id));

		return $id;
	}

	public static function checkDeleteKey ($deleteKey) {
		$result = Flight::db()->select(self::tableLinks(), 'delete_key', [
			'delete_key' => $deleteKey,
		]);

		if (count($result) > 0)
			return true;

		return false;
	}

	private static function generateDeleteKey () {
		do {
			$deleteKey = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, Flight::get('shortener.delete_key_length'));
		} while (self::checkDeleteKey($deleteKey));

		return $deleteKey;
	}


	public static function shorten ($link, $customID = false, $expireHours = 0) {
		$expireDate = '2038-01-01 00:00:00';

		if ($expireHours !== 0) {
			$expireTimestamp = time() + (60 * 60 * $expireHours);
			$expireDate = date('Y-m-d H:i:s', $expireTimestamp);
		}

		if (!$customID) {
			$id = self::generateID();
		} else {
			if (!self::checkID($customID)) {
				$id = $customID;
			} else {
				return ['success' => false, 'error' => 'Custom ID already exists or is reserved! Please choose another one!'];
			}
		}

		$deleteKey = self::generateDeleteKey();

		Flight::db()->insert(self::tableLinks(), [
			'id'         => $id,
			'link'       => $link,
			'expires'    => $expireDate,
			'delete_key' => $deleteKey,
		]);

		return ['success' => true, 'id' => $id, 'deleteKey' => $deleteKey];
	}

	public static function delete ($deleteKey) {
		if (!self::checkDeleteKey($deleteKey))
			return ['success' => false, 'error' => 'Delete Key doesn\'t exist!'];

		$result = Flight::db()->select(self::tableLinks(), 'id', [
			'delete_key' => $deleteKey,
		]);

		$id = false;
		if (count($result) > 0)
			$id = $result[0];

		Flight::db()->delete(self::tableLinks(), [
			'delete_key' => $deleteKey,
		]);

		if ($id) {
			Flight::db()->delete(self::tableClicks(), [
				'id' => $id,
			]);
		}

		return ['success' => true];
	}

	public static function report ($id) {
		if (!self::checkID($id))
			return ['success' => false, 'error' => 'ID doesn\'t exist!'];

		Flight::db()->update(self::tableLinks(), [
			'reported' => 1,
		], [
			'id' => $id,
		]);

		return ['success' => true];
	}

	public static function getRedirectLink ($id) {
		if (!self::checkID($id))
			return ['success' => false, 'error' => 'ID doesn\'t exist!'];

		$result = Flight::db()->select(self::tableLinks(), [
			'link',
			'expires',
			'created',
		], [
			'id' => $id,
		]);

		if (count($result) > 0) {
			$row = $result[0];

			return ['success' => true, 'link' => $row['link'], 'created' => $row['created']];
		}

		return ['success' => false, 'error' => 'A database error occurred! Please try again later.'];
	}

	public static function count () {
		return Flight::db()->count(self::tableLinks());
	}

	public static function recordClick ($id) {
		Flight::db()->insert(self::tableClicks(), [
			'id' => $id,
		]);

		return ['success' => true];
	}

	public static function getTotalClicks ($id) {
		if (!self::checkID($id)) {
			return 0;
		}

		$result = Flight::db()->select(self::tableClicks(), 'date', [
			'id' => $id,
		]);

		if (count($result) > 0) {
			$timestamps = [];
			foreach ($result as $date) {
				$timestamps[] = strtotime($date);
			}

			sort($timestamps);

			$totalClicks = 0;
			$clicksOverTime = [];
			foreach ($timestamps as $timestamp) {
				$month = date('F Y', $timestamp);

				$foundMonth = false;
				foreach ($clicksOverTime as $index => $click) {
					if ($click['month'] === $month) {
						$foundMonth = true;
						$clicksOverTime[$index]['clicks']++;
						$totalClicks++;
					}
				}

				if (!$foundMonth) {
					$clicksOverTime[] = [
						'month'  => $month,
						'clicks' => 1,
					];
					$totalClicks++;
				}
			}

			return $totalClicks;
		}

		return 0;
	}

	public static function getReportedLinks () {
		return Flight::db()->select(self::tableLinks(), [
			'id',
			'link',
			'expires',
			'created',
		], [
			'reported' => 1,
		]);
	}

	public static function approveReportedLink ($id) {
		if (!self::checkID($id))
			return ['success' => false, 'error' => 'ID doesn\'t exist!'];

		Flight::db()->update(self::tableLinks(), [
			'reported' => 0,
		], [
			'id' => $id,
		]);

		return ['success' => true];
	}

	public static function deleteReportedLink ($id) {
		if (!self::checkID($id))
			return ['success' => false, 'error' => 'ID doesn\'t exist!'];


		$result = Flight::db()->select(self::tableLinks(), 'delete_key', [
			'id' => $id,
		]);

		if (count($result) > 0) {
			return self::delete($result[0]);
		}

		return ['success' => false];
	}

	public static function removeExpired() {
		$result = Flight::db()->delete(self::tableLinks(), [
			'expires[<]' => Flight::dbRaw(Flight::get('cockpit.db')['database_type'] == 'sqlite' ? "datetime('now', 'localtime')" : 'NOW()')
		]);

		return ['success' => true, 'affectedRows' => $result->rowCount()];
	}

	public static function getTitle($id) {
		if (!self::checkID($id))
			return ['success' => false, 'error' => 'ID doesn\'t exist!'];

		$result = Flight::db()->select(self::tableLinks(), 'title', [
			'id' => $id,
		]);

		if (count($result) > 0)
			return ['success' => true, 'title' => $result[0]];

		return ['success' => false];
	}

	public static function setTitle($id, $title) {
		if (!self::checkID($id))
			return ['success' => false, 'error' => 'ID doesn\'t exist!'];

		if (strlen($title) > 255)
			$title = trim(substr($title, 0, 252)) . '...';

		Flight::db()->update(self::tableLinks(), [
			'title' => $title,
		], [
			'id' => $id,
		]);

		return ['success' => true];
	}
}