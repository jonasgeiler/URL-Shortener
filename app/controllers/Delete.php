<?php


class Delete {
	public static function page() {
		Flight::csrf()->regenerateToken();

		Flight::view()->set('titlePostfix', 'Delete Link');

		Flight::render('delete');
	}

	public static function form() {
		$error = false;

		if (!Flight::csrf()->validateToken())
			$error = 'CSRF Token is invalid! Please reload the page and try again!';

		if (!$error && (!isset(Flight::request()->data->delete_key) || empty(Flight::request()->data->delete_key)))
			$error = 'A Delete Key is required!';

		if (!$error && (strlen(Flight::request()->data->delete_key) > Flight::get('shortener.delete_key_length')))
			$error = 'Delete Key is too long! A Delete Key consists of ' . Flight::get('shortener.delete_key_length') . ' characters.';

		if (!$error && (strlen(Flight::request()->data->delete_key) < Flight::get('shortener.delete_key_length')))
			$error = 'Delete Key is too short! A Delete Key consists of ' . Flight::get('shortener.delete_key_length') . ' characters.';

		if (!$error) {
			$deleteKey = Flight::request()->data->delete_key;

			$response = Link::delete($deleteKey);

			$success = $response['success'];

			if ($success) {
				Flight::view()->set('titlePostfix', 'Link Deleted');

				return Flight::render('success/delete');
			} else {
				$error = isset($response['error']) ? $response['error'] : 'An unknown error occurred.';
			}
		}

		Flight::view()->set('error', $error);
		Flight::view()->set('deleteKey', isset(Flight::request()->data->delete_key) ? Flight::request()->data->delete_key : null);

		self::page();
	}
}