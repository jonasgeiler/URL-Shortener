<?php
require __DIR__ . '/../flight/Flight.php';

require __DIR__ . '/../bootstrap/dependencies.php';
require __DIR__ . '/../bootstrap/routes.php';

Flight::path(__DIR__ . '/../app/controllers');
Flight::path(__DIR__ . '/../app/models');

Flight::start();