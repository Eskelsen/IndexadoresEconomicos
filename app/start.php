<?php

# Start

# Hold headers
ob_start();

# Hub, Routes & Basics
include APP . 'hub.php';
include APP . 'routes.php';
include APP . 'basics.php';
include APP . 'conex.php';

# Request stream
$in = request();

$result = matcher($in,$web);

$stream	= $result[0] ?? false;
$acl	= $result[1] ?? false;

[$stream, $acl] = ($stream) ? [$stream,$acl] : ['streams/pages/404', true];

# Middle Streams
include STREAMS . 'lock.php';
include STREAMS . 'sessions.php';

$access_id = access($in, $stream);

# Stream
include APP . $stream . '.php';

// To change almost everything here
