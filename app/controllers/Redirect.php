<?php


class Redirect {
	public static function handle($id) {
		$response = Link::getRedirectLink($id);

		$success = $response['success'];

		if ($success) {
			Link::recordClick($id); // For stats

			Flight::redirect($response['link'], 302);
		} else {
			Flight::notFound();
		}
	}
}