<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
		<link rel="manifest" href="/site.webmanifest">

		<title><?= $titlePrefix ?> | URL-Shortener, by Skayo</title>

		<link rel="stylesheet" href="/css/site.css">
		<link rel="stylesheet" href="/css/background.css">
	</head>
	<body class="login uk-cover-container uk-background-secondary uk-flex uk-flex-center uk-flex-middle uk-height-viewport uk-overflow-hidden uk-light" uk-height-viewport>
		<div class="uk-position-cover uk-overlay-primary"></div>

		<div class="<?= isset($largeWidth) && $largeWidth ? 'uk-width-xxlarge' : 'uk-width-large' ?> uk-padding-small uk-position-z-index uk-animation-fade">
			<?php $this->yield('content') ?>
		</div>


		<script src="https://cdn.jsdelivr.net/npm/uikit@latest/dist/js/uikit.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/uikit@latest/dist/js/uikit-icons.min.js"></script>
		<?php $this->yield('scripts') ?>
		<script async src="https://analytics.skayo.dev/ackee.js" data-ackee-server="https://analytics.skayo.dev" data-ackee-domain-id="ddf0ddc1-3c67-4246-be96-9455396262f8"></script>
	</body>
</html>