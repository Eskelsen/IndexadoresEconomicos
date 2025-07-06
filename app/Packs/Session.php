<?php

namespace App\Packs;

class Session # [tmp] 2025-07-06 Sunday: work on it
{
	// O PHP espera que o handler tenha os seguintes métodos públicos:

	// open($savePath, $sessionName)
	// close()
	// read($id)
	// write($id, $data)
	// destroy($id)
	// gc($maxLifetime)

	public function session_on_db($name){
		
		global $conex;
		
		if (empty($conex)) {
			return false;
		}
		
		ini_set('session.serialize_handler', 'php_serialize');
		session_name($name);
		session_set_save_handler(
			'session_open',
			'session_close',
			'session_read',
			'session_write',
			'session_quit',
			'session_del',
			'session_renew'
		);
		session_start();
		if (session_expired(session_id())) { // tmp
			session_regenerate_id();
		}
	}

	public function session_open(){
		return true;
	}

	public function session_close($session = 0){
		// return (del('mf_sessions','session=?',[$session])) ? true : false; // rw
		return true;
	}

	public function session_read($session, $internal = false){
		$values = selectRow('mf_sessions','*','WHERE session=? AND active=1',[$session]); // tmp
		$id   = $values['id'] ?? false;
		$data = empty($values['data']) ? 'a:0:{}' : $values['data'];
		return ($internal) ? ($id ? unserialize($data) : false) : $data;
	}

	public function session_expired($session){
		return selectRow('mf_sessions','*','WHERE session=? AND active=0',[$session]); // tmp
	}

	public function session_write($session, $data){
		$values['access'] 	= time();
		$values['data']		= $data;
		$current_data = session_read($session, true); // tmp
		if ($current_data!==false) {
			if (empty($current_data['usr']['id'])) {
				$new_data = unserialize($data);
				$values['user_id'] = $new_data['usr']['id'] ?? null;
			}
			return (update('mf_sessions', array_filter($values),'session=?',[$session])!==false) ? true : false; // tmp
		}
		if (session_expired($session)) { // tmp
			return true;
		}
		$values['session']	= $session;
		$values['expires']	= time() + 30*24*60*60;
		$values['active']	= 1;
		return (insert('mf_sessions',$values)>=1) ? true : false; // tmp
	}

	public function session_quit($session){
		$values['active'] = 0;
		return (update('mf_sessions', $values,'session=?',[$session])!==false) ? true : false; // tmp
	}

	public function session_del($max){
		$old = time() - $max;
		return (del('mf_sessions','expires<?',[$old])) ? true : false; // tmp
	}

	public function session_renew(){
		$session = sha1(uniqid('', true));
		if (selectRow('mf_sessions','*','WHERE session=?',[$session])) { // tmp
			return session_renew();
		}
		return session_write($session, '') ? $session : false; // tmp
	}
}
