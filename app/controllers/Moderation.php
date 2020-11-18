<?php


class Moderation {
	public static function page () {
		Flight::auth()->requireAuth();

		if (!Flight::auth()->isAdmin)
			Flight::redirect('/', 302);

		Install::tryInstall();

		Flight::csrf()->regenerateToken();

		Flight::view()->set('titlePrefix', 'Moderation');
		Flight::view()->set('reportedLinks', Link::getReportedLinks());
		Flight::view()->set('linksCreated', Link::count());

		Flight::render('moderation');
	}

	public static function form () {
		Flight::auth()->requireAuth();

		if (!Flight::auth()->isAdmin)
			Flight::redirect('/', 302);

		$error = false;

		if (!Flight::csrf()->validateToken())
			$error = 'CSRF Token is invalid! Please reload the page and try again!';

		if (!$error && (!isset(Flight::request()->data->action) || empty(Flight::request()->data->action)))
			$error = 'Action is required!';

		if (!$error && (isset(Flight::request()->data->selected_ids) && !is_array(Flight::request()->data->selected_ids)))
			$error = 'Selected IDs invalid!';

		if (!$error) {
			$action = Flight::request()->data->action;
			$selectedIDs = isset(Flight::request()->data->selected_ids) ? Flight::request()->data->selected_ids : [];

			foreach ($selectedIDs as $id => $checked) {
				if ($checked != 'on') continue;

				if (strtolower($action) == 'delete selected') {
					Link::deleteReportedLink($id);
				} else if (strtolower($action) == 'approve selected') {
					Link::approveReportedLink($id);
				}
			}
		}

		Flight::view()->set('error', $error);

		self::page();
	}
}