<?php

namespace App\Packs; # [tmp] 2025-07-05 Saturday: work on it

class Acl
{
	public function acl($in){
		return $this->xcl($in,'usr','access');
	}

	public function rcl($in){
		return $this->xcl($in,'acc','role'); # owner,manager,analyst,auditor
	}

	public function xcl($in,$lvl,$key){
		$xcl = array_filter(explode(',',$in));
		if (empty($xcl) OR $in=='*') {
			return true;
		}
		if (empty($_SESSION[$lvl][$key])) {
			return false;
		}
		if ('master'==$_SESSION[$lvl][$key]) {
			return true;
		}
		return (bool) in_array($_SESSION[$lvl][$key],$xcl);
	}

	public function logged(){
		return !empty($_SESSION['usr']);
	}

	public function login($in){
		if (!is_array($in)) {
			return false;
		}
		$_SESSION['usr'] = [];
		$_SESSION['usr']['id'] 		= $in['id'] ?? null;
		$_SESSION['usr']['name'] 	= $in['name'] ?? null;
		$_SESSION['usr']['access'] 	= $in['access'] ?? null;
		$_SESSION['usr']['phone'] 	= $in['phone'] ?? null;
		$_SESSION['usr']['email']	= $in['email'] ?? null;
		$_SESSION['usr']['public'] 	= $in['public'] ?? null;
		$_SESSION['usr']['active'] 	= $in['active'] ?? null;
		$_SESSION['usr'] = array_filter($_SESSION['usr']);
		
		$_SESSION['acc']['id'] 	 = $_SESSION['usr']['id'];
		$_SESSION['acc']['role'] = $_SESSION['usr']['access']=='user' ? 'owner' : $_SESSION['usr']['access'];
		
		$acc_id = $_SESSION['acc']['id'] ?? 0;
		putenv("ACC_ID=$acc_id");
	}

	public function user($key = false){
		if ($key) {
			return $_SESSION['usr'][$key] ?? null;
		}
		return $_SESSION['usr'] ?? null;
	}

	public function userName(){
		$name = $_SESSION['usr']['name'] ?? 'parceiro';
		return explode(' ', $name)[0];
	}

	public function account($key = false){
		if ($key) {
			return $_SESSION['acc'][$key] ?? null;
		}
		return $_SESSION['acc'] ?? null;
	}

	public function listCtrlLabel($role = true){
		
		$value = ($role) ? $this->account('role') : $this->user('access');
		
		$roles['master'] 	= 'Master';
		$roles['user']   	= 'Usuário';
		$roles['auditor']   = 'Auditor';
		$roles['analyst']   = 'Analista';
		$roles['support']   = 'Suporte';
		$roles['editor']   	= 'Editor';
		$roles['owner']   	= 'Proprietário';
		$roles['client']   	= 'Cliente';
		$roles['manager']	= 'Administrador';
		
		return $roles[$value] ?? 'Indefinido';
	}

	public function userSet($key, $value){
		$_SESSION['usr'][$key] = $value;
	}

	public function accountSet($key, $value){
		$_SESSION['acc'][$key] = $value;
	}

	public function session($key = false){
		if ($key) {
			return $_SESSION[$key] ?? null;
		}
		return $_SESSION ?? null;
	}

	public function sessionSet($key, $value){
		$_SESSION[$key] = $value;
	}

	public function access($in, $stream = null){
		$vs = [
			'time' 		=> time(),
			'date' 		=> date('Y-m-d H:i:s'),
			'stream' 	=> $stream,
			'method' 	=> $_SERVER['REQUEST_METHOD'] ?? null,
			'source' 	=> $_GET['utm_source'] ?? null,
			'user_id'	=> $_SESSION['usr']['id'] ?? null,
			'ip' 		=> ip(), // coupling
			'request' 	=> $in,
			'query' 	=> parse_url($_SERVER['REQUEST_URI'])['query'] ?? null
		];
		$vs = array_filter($vs);
		return insert('mf_access', $vs); // coupling
	}

	public function attempt($value){
		global $access_id;
		update('mf_access', ['attempt' => $value], 'id=?', [$access_id]); // coupling
	}

	public function attemptsStatus($value, $limit = 5, $api = false){
		$lapse = time() - 3*60*60;
		$ip = ip(); // coupling
		$attempts = selectCount('mf_access','id','WHERE time>=? AND ip=? AND attempt=?',[$lapse,$ip,$value]); // coupling
		if ($attempts>=$limit) {
			logfy('[PACKS/ACL] Recurso bloqueado por uso abusivo: ' . $ip); // coupling
			if ($api) {
				response('Recurso bloqueado por uso abusivo.', 403);
			}
			global $c;
			include VIEWS . 'attempts.php';
		}
		return $attempts;
	}

	public function excessiveAccess($min = 3, $requests = 120){
		$lapse = time() - 60 * $min;
		$ip = ip(); // coupling
		$excess_access = selectCount('mf_access','id','WHERE time>=? AND ip=?',[$lapse, $ip]); // coupling
		if ($excess_access>=$requests) {
			global $c;
			logfy('[PACKS/ACL] Recurso bloqueado por uso abusivo: ' . $ip); // coupling
			include VIEWS . 'excessive.php';
		}
	}

	public function alertMsg($msg,$color = null,$disappear = false){
		global $acc_id;
		$_SESSION['alerts'][] = [$msg,$color,$disappear];
	}
}