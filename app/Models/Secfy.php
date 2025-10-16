<?php

# Secure Fy

	# [tmp] 2025-07-21 Monday: work on it

function secfy($pswd){
	$secret = random_bytes(37);
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	$crypt_pswd = openssl_encrypt($pswd, 'aes-256-cbc', $secret, 0, $iv);
	
	$sectag = sha1($secret);
	
	$secfile = TMP . 'sectags/' . $sectag;
	
	file_put_contents($secfile, json_encode([bin2hex($secret),bin2hex($iv)]));
	
	return [$sectag, $crypt_pswd];
}

function secfyReverse($sectag,$crypt_pswd){
	
	$secfile = TMP . 'sectags/' . $sectag;
	
	if (!is_file($secfile)) {
		logfy('[Secure Fy] no sectag file');
		return false;
	}
	
	$ctn  = file_get_contents($secfile);
	$data = json_decode($ctn, 1);
	
	if (!$data) {
		logfy('[Secure Fy] no data in sectag file');
		return false;
	}
	
	$secret = empty($data[0]) ? false : hex2bin($data[0]);
	$iv		= empty($data[1]) ? false : hex2bin($data[1]);
	
	if ($secret AND $iv) {
		return openssl_decrypt($crypt_pswd, 'aes-256-cbc', $secret, 0, $iv);
	}
	logfy('[Secure Fy] no secret or iv found');
	return false;
}

function secfyUpdate($sectag,$pswd){
	$secfile = TMP . 'sectags/' . $sectag;
	if (is_file($secfile)) {
		logfy('[Secure Fy] delete old secfile');
		unlink($secfile);
	}
	return secfy($pswd);
}
