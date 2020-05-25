<?php


class Cockpit {

	private static $availableInstruments = [
		'auth'     => 'Auth',
		'csrf'     => 'Csrf',
		'db'       => 'DB',
		'view'     => 'View',
		'utils'    => 'Utils',
	];

	private static $usedInstruments = [];

	public static $configFile = 'config.ini';

	private static $initialized = false;

	public static function init () {
		// Load config
		$config = parse_ini_file(__DIR__ . '/../' . self::$configFile, true, INI_SCANNER_TYPED);

		foreach ($config as $section => $sectionContent) {
			if (empty($sectionContent))
				continue;

			if ($section == 'cockpit.db') {
				Flight::set($section, $sectionContent); // So I don't have to modify the DB class more than needed
				continue;
			}

			foreach ($sectionContent as $key => $value) {
				if (substr($key, -4) == 'path') {
					$value = __DIR__ . '/../' . ltrim($value, './');
				}

				Flight::set("$section.$key", $value);
			}
		}

		// Initialisation complete
		self::$initialized = true;
	}

	public static function useInstruments ($instruments = []) {
		if (!self::$initialized)
			self::init();

		if (!is_array($instruments)) {
			$instruments = [$instruments];
		}

		foreach ($instruments as $instrument) {
			$instrument = strtolower($instrument);

			if (in_array($instrument, self::$usedInstruments))
				continue;

			if (!isset(self::$availableInstruments[$instrument]))
				throw new Exception("No cockpit instrument named '$instrument' found!");

			require __DIR__ . '/instruments/' . self::$availableInstruments[$instrument] . '.php';

			switch ($instrument) {
				case 'utils':
					Utils::init();
					break;

				case 'view':
					Flight::register('view', 'View');
					Flight::map('render', function ($template, $data = null) {
						Flight::view()->render($template, $data);
					});
					break;

				default:
					Flight::register($instrument, self::$availableInstruments[$instrument]);
			}

			self::$usedInstruments[] = $instrument;
		}
	}

}