<?php


class Cron {
	public static function removeExpired() {
		Flight::auth()->requireAuth();

		$result = Flight::db()->delete('srt_links', [
			'expires[<]' => DB::raw('NOW()')
		]);

		echo 'DONE! AFFECTED ' . $result->rowCount() . ' ROWS.';
	}

	public static function updateBackgroundImages() {
		Flight::auth()->requireAuth();

		$css = '';
		$css .= "body.login{background-image:url('" . self::getUnsplashImage('https://source.unsplash.com/collection/317099/640x700') . "');background-position:center center;background-repeat:no-repeat;background-size:cover;}";
		$css .= "@media screen and (min-width: 640px){body.login{background-image:url('" . self::getUnsplashImage('https://source.unsplash.com/collection/317099/960x700') . "');}}";
		$css .= "@media screen and (min-width: 960px){body.login{background-image:url('" . self::getUnsplashImage('https://source.unsplash.com/collection/317099/1200x900') . "');}}";
		$css .= "@media screen and (min-width: 1200px){body.login{background-image:url('" . self::getUnsplashImage('https://source.unsplash.com/collection/317099/1600x950') . "');}}";
		$css .= "@media screen and (min-width: 1600px){body.login{background-image:url('" . self::getUnsplashImage('https://source.unsplash.com/collection/317099/2000x1050') . "');}}";

		file_put_contents('./css/background.css', $css);

		echo 'DONE! GENERATED CSS:' . PHP_EOL . $css;
	}

	private static function getUnsplashImage($url) {
		$headers = get_headers($url);

		foreach ($headers as $header) {
			if (substr($header, 0, 10) == 'Location: ')
				return substr($header, 10);
		}

		return $url;
	}
}