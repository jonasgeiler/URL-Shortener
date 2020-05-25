<?php


class Csrf {

	private $sessionKey = '';

	private $tokens = [];

	private $defaultTokenName = '';

	public function __construct () {
		if (session_status() == PHP_SESSION_NONE)
			session_start();

		$this->sessionKey = Flight::get('cockpit.csrf.session_key') ?: 'flight_csrf';
		$this->defaultTokenName = Flight::get('cockpit.csrf.default_token_name') ?: 'default';

		if (isset($_SESSION[$this->sessionKey]) && is_array($_SESSION[$this->sessionKey]))
			$this->tokens = $_SESSION[$this->sessionKey];
	}

	public function getToken ($name = null) {
		$hashName = $this->hashName($name);
		$token = isset($this->tokens[$hashName]) ? $this->tokens[$hashName] : null;

		return $token ?: $this->regenerateToken($name);
	}

	public function getTokenField ($name = null) {
		$token = $this->getToken($name);

		return '<input type="hidden" name="csrftoken" value="' . $token . '" />';
	}

	public function validateToken ($name = null) {
		$token = $this->getToken($name);
		$userToken = isset(Flight::request()->query->csrftoken) ? Flight::request()->query->csrftoken : Flight::request()->data->csrftoken;

		return !is_null($userToken) && $token === $userToken;
	}

	public function regenerateToken ($name = null) {
		$name = $this->hashName($name);
		$this->tokens[$name] = $this->getUniqueToken();

		if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey]))
			$_SESSION[$this->sessionKey] = [];

		$_SESSION[$this->sessionKey][$name] = $this->tokens[$name];

		return $this->tokens[$name];
	}

	public function resetAll () {
		$this->tokens = [];
		unset($_SESSION[$this->sessionKey]);
	}

	private function getUniqueToken () {
		$length = 64;

		if (function_exists('random_bytes'))
			return base64_encode(random_bytes($length));

		if (function_exists('openssl_random_pseudo_bytes'))
			return base64_encode(openssl_random_pseudo_bytes($length));

		if (function_exists('password_hash'))
			return base64_encode(password_hash(uniqid(), PASSWORD_DEFAULT, ['cost' => 4]));

		$token = '';
		for ($i = 0; $i < 4; $i++) {
			$token .= uniqid(rand(0, 10000));
		}

		return base64_encode(str_shuffle($token));
	}

	private function hashName ($name = null) {
		$name = strtolower($name);

		return md5($name ?: $this->defaultTokenName);
	}
}