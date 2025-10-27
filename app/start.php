<?php

# Start

# Autoload
include WEB . 'vendor/autoload.php';

# Hold headers
ob_start();

# Routes
include APP . 'web.php';

# Basic Data & Config
include APP . 'config.php';
include APP . 'basics.php';

# Request stream
$web->init();

if (empty($web->stream)) {
	include APP . 'streams/pages/404.php';
	exit;
}

if (!is_file(APP . 'streams/' . $web->stream . '.php')) {
	exit('There is not such stream');
}

# Stream
include APP . 'streams/' . $web->stream . '.php';
