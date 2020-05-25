<?php


class Report {
	public static function page() {
		Flight::csrf()->regenerateToken();

		Flight::view()->set('titlePostfix', 'Report Link');

		Flight::render('report');
	}

	public static function form() {
		$error = false;

		if (!Flight::csrf()->validateToken())
			$error = 'CSRF Token is invalid! Please reload the page and try again!';

		if (!$error && (!isset(Flight::request()->data->id) || empty(Flight::request()->data->id)))
			$error = 'An ID is required!';

		if (!$error) {
			$id = Flight::request()->data->id;

			$response = Link::report($id);

			$success = $response['success'];

			if ($success) {
				Flight::view()->set('titlePostfix', 'Link Reported');

				return Flight::render('success/report');
			} else {
				$error = isset($response['error']) ? $response['error'] : 'An unknown error occurred.';
			}
		}

		Flight::view()->set('error', $error);
		Flight::view()->set('id', isset(Flight::request()->data->id) ? Flight::request()->data->id : null);

		self::page();
	}
}