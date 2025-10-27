<?php

# IPCA

use App\Core\Http;

$data['value'] = 5.17;
$data['ref'] = 'setembro de 2025';

// Http::response($data);
Http::response($data['value']);
