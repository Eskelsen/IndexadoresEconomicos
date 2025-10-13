<?php

# Identifiers

namespace App\Core;

class Identifiers
{
	private $db;
	
	public function __construct($db)
	{
		$this->db = $db; # [tmp] 2025-10-13 Monday: work on it
	}
	
	public function identifier(){
		return hash('fnv1a32',time() . rand(0,10000));
	}

	public function uniqueHash($t,$f,$alg = 'fnv1a32'){
		$hash = hash($alg,uniqid());
		$exists = $this->db->field($t,$f,"WHERE $f=?",[$hash]);
		return ($exists) ? $this->uniqueHash($t,$f,$alg) : $hash;
	}

	public function uniqueCode($t,$f,$alg = 'fnv1a32'){
		$hash = strtoupper(hash($alg,uniqid()));
		$exists = $this->db->field($t,$f,"WHERE $f='$hash'");
		return ($exists) ? $this->uniqueCode($t,$f,$alg) : $hash;
	}

	public function locatorGenerator($size = 6, $type = 2){
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
}
