<?php

# Salário Mínimo

use App\Core\Http;

$data['value'] = 7.50;
$data['ref'] = 'setembro de 2025';

// Http::response($data);
Http::response($data['value']);
