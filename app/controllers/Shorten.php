<?php


class Shorten {
	public static function page () {
		Flight::csrf()->regenerateToken();

		Flight::view()->set('titlePrefix', 'Homepage');

		Flight::render('shorten');
	}

	public static function form () {
		$error = false;

		if (!Flight::csrf()->validateToken())
			$error = 'CSRF Token is invalid! Please reload the page and try again!';

		if (!$error && (!isset(Flight::request()->data->link) || empty(Flight::request()->data->link)))
			$error = 'A Link is required!';

		if (!$error && (filter_var(Flight::request()->data->link, FILTER_VALIDATE_URL) === false))
			$error = 'Link must be a valid URL!';

		if (!$error && (isset(Flight::request()->data->custom_id) && preg_match('/[A-Za-z0-9-]+/', Flight::request()->data->custom_id) === false))
			$error = 'Custom ID is invalid! Allowed characters are A-Z, a-z, 0-9 and "-".';

		if (!$error && (isset(Flight::request()->data->custom_id) && strlen(Flight::request()->data->custom_id) > 250))
			$error = 'Custom ID is too long! Maximum length is 250.';

		if (!$error && (isset(Flight::request()->data->expires) && !is_numeric(Flight::request()->data->expires)))
			$error = 'Expire-Time must be a number!';

		if (!$error) {
			$link = Flight::request()->data->link;
			$customID = isset(Flight::request()->data->custom_id) ? Flight::request()->data->custom_id : false;
			$expires = isset(Flight::request()->data->expires) ? intval(Flight::request()->data->expires) : 0;

			$response = Link::shorten($link, $customID, $expires);

			$success = $response['success'];

			if ($success) {
				Flight::view()->set('titlePrefix', 'Your Shortened Link');
				Flight::view()->set('id', $response['id']);
				Flight::view()->set('deleteKey', $response['deleteKey']);
				Flight::view()->set('link', $link);

				return Flight::render('success/shorten.php');
			} else {
				$error = isset($response['error']) ? $response['error'] : 'An unknown error occurred.';
			}
		}

		Flight::view()->set('error', $error);
		Flight::view()->set('link', isset($_POST['link']) ? $_POST['link'] : null);
		Flight::view()->set('customID', isset($_POST['custom_id']) ? $_POST['custom_id'] : null);
		Flight::view()->set('expires', isset($_POST['expires']) ? $_POST['expires'] : null);

		self::page();
	}

	public static function api() {
		Flight::auth()->requireAuth();

		if (!isset(Flight::request()->data->link) || empty(Flight::request()->data->link))
			return Flight::json(['success' => false, 'error' => 'A Link is required!']);

		if (filter_var(Flight::request()->data->link, FILTER_VALIDATE_URL) === false)
			return Flight::json(['success' => false, 'error' => 'Link must be a valid URL!']);

		if (isset(Flight::request()->data->custom_id) && preg_match('/[A-Za-z0-9-]+/', Flight::request()->data->custom_id) === false)
			return Flight::json(['success' => false, 'error' => 'Custom ID is invalid! Allowed characters are A-Z, a-z, 0-9 and "-".']);

		if (isset(Flight::request()->data->custom_id) && strlen(Flight::request()->data->custom_id) > 250)
			return Flight::json(['success' => false, 'error' => 'Custom ID is too long! Maximum length is 250.']);

		if (isset(Flight::request()->data->expires) && !is_numeric(Flight::request()->data->expires))
			return Flight::json(['success' => false, 'error' => 'Expire-Time must be a number!']);

		$link = Flight::request()->data->link;
		$customID = isset(Flight::request()->data->custom_id) ? Flight::request()->data->custom_id : false;
		$expires = isset(Flight::request()->data->expires) ? intval(Flight::request()->data->expires) : 0;

		Flight::json(Link::shorten($link, $customID, $expires));
	}
}