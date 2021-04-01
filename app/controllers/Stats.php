<?php


class Stats {
	public static function page ($id) {
		$response = Link::getRedirectLink($id);

		if (!$response['success']) {
			Flight::notFound();
		}

		$redirectLink = $response['link'];

		$createdTime = date('M j, Y \a\t H:i', strtotime($response['created']));

		$gotTitle = false;
		$response = Link::getTitle($id);

		$success = $response['success'];

		if ($success && !empty($response['title'])) {
			$title = $response['title'];
			$gotTitle = true;
		} else {
			$dom = new DOMDocument();

			$rawHTML = @file_get_contents($redirectLink);

			libxml_use_internal_errors(true);
			if (!empty($rawHTML) && $dom->loadHTML($rawHTML)) {
				$titles = $dom->getElementsByTagName('title');
				if ($titles->length > 0) {
					$title = $titles->item(0)->textContent;
					Link::setTitle($id, $title);
					$gotTitle = true;
				} else {
					$title = $redirectLink;
				}
			} else {
				$title = $redirectLink;
			}
		}

		Flight::view()->set('titlePrefix', 'Link Statistics');
		Flight::view()->set('link', $redirectLink);
		Flight::view()->set('createdTime', $createdTime);
		Flight::view()->set('linkTitle', $title);
		Flight::view()->set('gotTitle', $gotTitle);
		Flight::view()->set('totalClicks', Link::getTotalClicks($id));
		Flight::view()->set('shares', self::getShares($id));

		Flight::render('stats');
	}

	private static function getShares ($id) {
		$url = 'https://' . Flight::get('shortener.domain') . "/$id";

		$shares = [];

		$sharedCountData = self::getSharedCountData($url);
		$shares['Facebook'] = $sharedCountData['Facebook']['share_count'];
		$shares['Pinterest'] = $sharedCountData['Pinterest'];
		$shares['Reddit'] = self::getRedditShares($url);
		$shares['VK'] = self::getVkShares($url);
		$shares['Odnoklassniki'] = self::getOdnoklassnikiShares($url);

		$tooltip = '';
		foreach ($shares as $site => $shareCount) {
			$tooltip .= (empty($tooltip) ? '' : '<br>') . "<b>$site</b>: " . number_format($shareCount, 0, ',', '.');
		}

		return [
			'count'   => array_sum(array_values($shares)),
			'tooltip' => $tooltip,
		];
	}

	private static function getSharedCountData ($url) {
		return json_decode(
			file_get_contents('https://api.sharedcount.com/v1.0/?url=' . urlencode($url) . '&apikey=' . Flight::get('sharedcount.api_key')),
			true
		);
	}

	private static function getRedditShares ($url) {
		$data = json_decode(file_get_contents('https://www.reddit.com/api/info.json?url=' . urlencode($url)), true);

		$score = 0;

		foreach ($data['data']['children'] as $child) {
			$score += (int) $child['data']['score'];
		}

		return $score;
	}

	private static function getVkShares ($url) {
		$jsonp = file_get_contents('https://vk.com/share.php?act=count&url=' . urlencode($url));

		if ($jsonp[0] !== '[' && $jsonp[0] !== '{') {
			$jsonp = substr($jsonp, strpos($jsonp, '('));
		}

		$jsonp = trim($jsonp); // remove trailing newlines
		$jsonp = trim($jsonp, '()'); // remove leading and trailing parenthesis

		return (int) trim(explode(',', $jsonp)[1]);
	}

	private static function getOdnoklassnikiShares ($url) {
		$jsonp = file_get_contents('https://connect.ok.ru/dk?st.cmd=extLike&ref=' . urlencode($url));

		if ($jsonp[0] !== '[' && $jsonp[0] !== '{') {
			$jsonp = substr($jsonp, strpos($jsonp, '('));
		}

		$jsonp = trim($jsonp); // remove trailing newlines
		$jsonp = trim($jsonp, '()'); // remove leading and trailing parenthesis

		return (int) trim(explode(',', $jsonp)[1], "'");
	}
}