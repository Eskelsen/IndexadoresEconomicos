<?php

# Identifiers

	# [tmp] 2025-07-21 Monday: work on it

function identifier(){
    return hash('fnv1a32',time() . rand(0,10000));
}

function uniqueHash($t,$f,$alg = 'fnv1a32'){
    $hash = hash($alg,uniqid());
    $exists = field($t,$f,"WHERE $f=?",[$hash]);
    return ($exists) ? uniqueHash($t,$f,$alg) : $hash;
}

function uniqueCode($t,$f,$alg = 'fnv1a32'){
    $hash = strtoupper(hash($alg,uniqid()));
    $exists = field($t,$f,"WHERE $f='$hash'");
    return ($exists) ? uniqueCode($t,$f,$alg) : $hash;
}

function locatorGenerator($size = 6, $type = 2){
	if (!$size) {
		return false;
	}
    $range[0] = array_merge(range(0,9),range('A','Z'));
    $range[1] = range('A','Z');
    $range[2] = range(0,9);
	$string = $range[$type] ?? $range[2];
	$max = count($string) - 1;
    for ($i=0;$i<$size;$i++) {
        $n[] = $string[rand(0,$max)];
    }
    return empty($n) ? false : implode('',$n);
}
