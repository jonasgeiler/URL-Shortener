<?php


class Auth {

	private $realm = '';

	private $users = [];

	private $admins = [];

	public $username = null;

	public $authorized = false;

	public $isAdmin = false;

	public function __construct () {
		$this->realm = Flight::get('cockpit.auth.realm') ?: 'Restricted Area';
		$this->users = Flight::get('cockpit.auth.users') ?: [];
		$this->admins = Flight::get('cockpit.auth.admins') ?: [];

		$this->checkIfAuthorized();
	}

	private function checkIfAuthorized () {
		$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : false;
		$pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : false;

		if ($user && $pass && $this->verify($user, $pass)) {
			$this->authorized = true;
			$this->username = $user;
			$this->isAdmin = in_array($user, $this->admins);
		}
	}

	private function userExists ($user) {
		return isset($this->users[$user]);
	}

	private function verify ($user, $pass) {
		return ($this->userExists($user) && password_verify($pass, $this->users[$user]));
	}

	public function requireAuth () {
		if ($this->authorized)
			return;

		Flight::response()
		      ->clear()
		      ->status(401)
		      ->header('WWW-Authenticate: Basic realm="' . $this->realm . '"')
		      ->write("<html><head><title>401 Unauthorized</title></head><body><h1>Unauthorized</h1><p>This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser doesn't understand how to supply the credentials required.</p></body></html>")
		      ->send();

		exit();
	}
}