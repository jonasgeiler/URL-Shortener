<?php

Flight::route('GET /', 'Shorten::page');
Flight::route('POST /', 'Shorten::form');

Flight::route('GET /delete', 'Delete::page');
Flight::route('POST /delete', 'Delete::form');

Flight::route('GET /report', 'Report::page');
Flight::route('POST /report', 'Report::form');

Flight::route('GET /mod', 'Moderation::page');
Flight::route('POST /mod', 'Moderation::form');

Flight::route('/cron/remove-expired', 'Cron::removeExpired');
Flight::route('/cron/update-background-images', 'Cron::updateBackgroundImages');

Flight::route('/@id', 'Redirect::handle');
Flight::route('/@id/stats', 'Stats::page');

Flight::route('POST /api/shorten', 'Shorten::api');

Flight::map('notFound', function () {
	Flight::view()->set('titlePostfix', 'Not Found');
	Flight::render('404');
});