<?php

# Start

# Hold headers
ob_start();

	include APP . 'config.php'; // tmp

// tmp, to remember
spl_autoload_register(function ($class) { // to change
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/App/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) require $file;
});


# Hub, Routes & Basics
// include APP . 'hub.php';
// include APP . 'routes.php';
// include APP . 'basics.php';
// include APP . 'conex.php';

# Request stream
// $in = request();

// $result = matcher($in,$web);

$stream	= $result[0] ?? false;
$acl	= $result[1] ?? false;

[$stream, $acl] = ($stream) ? [$stream,$acl] : ['streams/pages/404', true];

# Middle Streams
// include STREAMS . 'lock.php';
// include STREAMS . 'sessions.php';

// $access_id = access($in, $stream);

# Stream
// include APP . $stream . '.php';

// To change almost everything here

// exit('The work starts here.');

include VIEWS . 'default.html';
