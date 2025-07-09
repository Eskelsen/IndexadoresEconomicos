<?php

namespace App\Packs;

class Form
{
	public function fc_ctrl($lapse = 1){
		$_SESSION['fc']['current']   = identifier(); // tmp
		$_SESSION['fc']['listed']['' . microtime(true)] = $_SESSION['fc']['current'];
		$fc = $_REQUEST['fc'] ?? false;
		if (!$fc) {
			return false;
		}
		$in = in_array($fc,$_SESSION['fc']['listed']);
		if ($in) {
			$key = array_search($fc,$_SESSION['fc']['listed']);
			unset($_SESSION['fc']['listed'][$key]);
			return ((time() - $key)>=$lapse);
		}
		return false;
	}

	public function fc(){
		$fc = session('fc');
		return '<input type="hidden" id="fc" name="fc" value="' . $fc['current'] . '">' . "\n";
	}

	/*
	 * Input Validate Functions
	 *
	*/

	public function validEmail($email){
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		[$username, $domain] = explode('@', $email);
		return checkdnsrr($domain, 'MX');
	}

	/*
	 * Input Filter Functions
	 *
	*/

	public function filterEmail($in){
		$in = is_string($in) ? trim($in) : false;
		return $in ? strtolower(trim($in)) : false;
	}

	public function filterHash($in){
		$in = is_string($in) ? trim($in) : false;
		return $in ? preg_replace("/[^A-Za-z0-9]/",'',$in) : false;
	}

	public function filterName($in){
		$in = is_string($in) ? trim($in) : false;
		return $in ? htmlspecialchars($in, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : false;
	}

	public function filterNumbers($in){
		return empty($in) ? false : preg_replace('/[^0-9]/','',$in);
	}

	public function tagsNormalize($in,$tags = ['b','i','em','p','span','strong','div']){
		$allowed = '<br>';
		$change = false;
		foreach ($tags as $tag) {
			if (tagsCouple($in,$tag)) {
				$allowed .= '<' . $tag . '>';
			} else {
				$change = true;
			}
		}
		return ($change) ? strip_tags($in,$allowed) :  false;
	}

	public function tagsCouple($in,$tag){
		$x = substr_count($in,'<' . $tag . '>');
		$y = substr_count($in,'</' . $tag . '>');
		return $x === $y;
	}

	public function phoneMask($in){
		$len = strlen($in);
		if ($len==12) {
			return '+' . substr($in, 0, 2) . ' (' . substr($in, 2, 2) . ') ' . substr($in, 4, 4) . '-' . substr($in, 8, 4);
		} elseif ($len==13) {
			return '+' . substr($in, 0, 2) . ' (' . substr($in, 2, 2) . ') ' . substr($in, 4, 5) . '-' . substr($in, 9, 5);
		}
		return $in;
	}
}
