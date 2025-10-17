<?php

# Start

# Autoload
include WEB . 'vendor/autoload.php';
// spl_autoload_register(function ($class) {
    // $prefix = 'App\\';
    // $base_dir = __DIR__ . '/src/App/';

    // if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
		// return;
	// }

    // $relative_class = substr($class, strlen($prefix));
    // $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	
    // if (file_exists($file)) {
		// require $file;
	// }
// });

# Hold headers
ob_start();

# Routes & Basic Data
include APP . 'web.php';
include APP . 'config.php';
include APP . 'basics.php';

# Request stream
$result = $web->init();

var_dump($result);

$stream	= $result->mid ?? false;
$acl	= $result->acl ?? false;

[$stream, $acl] = ($stream) ? [$stream,$acl] : ['streams/pages/404', true];

# Middle Streams
// include STREAMS . 'lock.php'; // remove
// include STREAMS . 'sessions.php'; // remove

// $access_id = access($in, $stream); // remove more than others

# Stream
include APP . $stream . '.php';

// To change almost everything here

// exit('The work starts here.');

// include VIEWS . 'default.html';
