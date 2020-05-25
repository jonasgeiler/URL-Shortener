<?php

require __DIR__ . '/../cockpit/Cockpit.php';

Cockpit::useInstruments(['auth', 'csrf', 'db', 'view', 'utils']);