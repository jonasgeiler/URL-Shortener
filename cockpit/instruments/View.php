<?php

/*!
 * Modified Copy of North Template
 * https://github.com/northphp/template
 *
 * Copyright (C), Fredrik Forsmo
 * Released under the MIT license
 */


class View {
	public $path;

	public $componentsPath;

	public $extension = '.php';

	private $component = [
		'data' => [],
		'file' => '',
	];

	private $vars = [];

	private $functions = [];

	private $sections = [];

	private $layout = '';

	public function __construct () {
		$this->path = Flight::get('cockpit.views.path') ?: './views';
		$this->componentsPath = Flight::get('cockpit.views.components_path') ?: './views/components';
		$this->extension = Flight::get('cockpit.views.extension') ?: '.php';

		$this->addFunction('e', [$this, 'e']);
		$this->addFunction('escape', [$this, 'e']);
	}

	public function get ($key) {
		return isset($this->vars[$key]) ? $this->vars[$key] : null;
	}

	public function set ($key, $value = null) {
		if (is_array($key) || is_object($key)) {
			foreach ($key as $k => $v) {
				$this->vars[$k] = $v;
			}
		} else {
			$this->vars[$key] = $value;
		}
	}

	public function has ($key) {
		return isset($this->vars[$key]);
	}

	public function clear ($key = null) {
		if (is_null($key)) {
			$this->vars = [];
		} else {
			unset($this->vars[$key]);
		}
	}

	public function __call ($name, $arguments) {
		if (isset($this->functions[$name]))
			return $this->functions[$name](...$arguments);

		return Flight::$name(...$arguments);
	}

	public function addFunction ($name, $callback) {
		$this->functions[$name] = $callback;
	}

	public function exists ($file) {
		return file_exists($this->path($file));
	}

	public function path ($file) {
		$ext = $this->extension;

		if (!empty($ext) && (substr($file, -1 * strlen($ext)) != $ext))
			$file .= $ext;

		if (substr($file, 0, 1) == '/' || substr($file, 0, strlen($this->path)) == $this->path)
			return $file;

		return $this->path . '/' . $file;
	}

	public function render ($file, $data = null) {
		$template = $this->path($file);

		if (!file_exists($template))
			throw new Exception("Template file not found: $template.");

		if (!empty($data) && is_array($data))
			$this->vars = array_merge($this->vars, $data);

		extract($this->vars);

		include $template;

		$content = $this->layout;

		foreach ($this->sections as $key => $row) {
			$content = str_replace('<?=' . $key . '?>', $row['text'], $content);
		}

		echo $content;

		$this->layout = '';
		$this->sections = [];
	}

	private function view ($file, array $data = []) {
		$template = $this->path($file);

		if (!file_exists($template))
			throw new Exception("Template file not found: $template.");

		if (!empty($data) && is_array($data))
			$this->vars = array_merge($this->vars, $data);

		extract($this->vars);
		ob_start();

		include $template;

		return ob_get_clean();
	}

	public function component ($file, array $data = []) {
		$file = $this->path($this->componentsPath . '/' . $file);

		$this->component['file'] = $file;
		$this->component['data'] = $data;

		ob_start();
	}

	public function endcomponent () {
		$slot = ob_get_clean();

		$data = $this->component['data'];
		$file = $this->component['file'];

		$data = array_merge($data, [
			'slot' => $slot,
		]);

		$this->insert($file, $data);
	}

	public function block ($name) {
		$args = array_slice(func_get_args(), 1);

		$this->parent = false;

		if (!isset($this->sections[$name])) {
			$this->yield($name);
			$this->parent = true;
		}

		$this->block = $name;
		$this->sections[$name]['text'] .= implode('', $args);

		if (empty($args))
			ob_start();
	}

	public function endblock () {
		if ($this->parent) {
			if (!isset($this->sections[$this->block]))
				$this->sections[$this->block] = ['parent' => '', 'text' => ''];

			$this->sections[$this->block]['parent'] .= ob_get_clean();

			return;
		}

		$this->sections[$this->block]['text'] .= ob_get_clean();
	}

	public function e ($value, $flags = ENT_COMPAT | ENT_HTML401, $encoding = 'UTF-8') {
		return htmlspecialchars($value, $flags, $encoding);
	}

	public function layout ($template, array $data = []) {
		$this->layout = $this->view($template, $data);
	}

	public function batch ($value, $functions) {
		foreach (explode('|', $functions) as $function) {
			if (is_callable($function)) {
				$value = $function($value);
			} else if (isset($this->functions[$function])) {
				$value = $this->$function($value);
			} else {
				throw new Exception("The function used in a batch call could not be found: $function");
			}
		}

		return $value;
	}

	public function fetch ($file, array $data = []) {
		return (new self())->view($file, $data);
	}

	public function insert ($file, array $data = []) {
		echo $this->fetch($file, $data);
	}

	public function parent () {
		if (!isset($this->sections[$this->block]))
			return;

		if (!is_string($this->sections[$this->block]['parent']))
			return;

		echo $this->sections[$this->block]['parent'];
	}

	public function yield ($name) {
		if (!isset($this->sections[$name]))
			$this->sections[$name] = ['parent' => '', 'text' => ''];

		echo '<?=' . $name . '?>';
	}
}