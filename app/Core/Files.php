<?php

namespace App\Core;

class Files # [tmp] 2025-07-06 Sunday: work on it
{
	public static function pngFromBase64($base64, $filename, $quality = 9){
		if (!($img = self::fromBase64($base64))) {
			return false;
		}
		imagesavealpha($img, true);
		$ok = imagepng($img, $filename, $quality); 
		imagedestroy($img);
		return $ok ? str_replace(WEB,'',$filename) : false;
	}

	public static function jpgFromBase64($base64, $filename, $quality = 100){
		if (!($img = self::fromBase64($base64))) {
			return false;
		}
		$ok = imagejpeg($img, $filename, $quality);
		imagedestroy($img);
		return $ok ? str_replace(WEB,'',$filename) : false;
	}
	 
	public static function fromBase64($base64){
		if (!($ctn = base64_decode($base64))) {
			return false;
		}
		$img = imagecreatefromstring($ctn);
		return $img ? $img : false;
	}

	public static function png($ctn, $filename, $quality = 9){
		$img = imagecreatefromstring($ctn);
		imagesavealpha($img, true);
		$ok = imagepng($img, $filename, $quality);
		imagedestroy($img);
		return $ok ? str_replace(WEB,'',$filename) : false;
	}

	public static function jpg($ctn, $filename, $quality = 100){
		$img = imagecreatefromstring($ctn);
		$ok = imagejpeg($img, $filename, $quality);
		imagedestroy($img);
		return $ok ? str_replace(WEB,'',$filename) : false;
	}

	public static function path($private = false){
		$folder = $private ? 'private' : 'public';
		$path = ASSETS . $folder . '/' . date('Ym') . '/';
		if (is_dir($path)) {
			return $path;
		}
		if (mkdir($path)) {
			return $path;
		}
		return false;
	}

	public static function save($key = 'file', $path = WEB . 'assets/public/'){
		self::fileControl();
		if (!empty($_FILES[$key]['tmp_name']) AND is_uploaded_file($_FILES[$key]['tmp_name'])) {
			$filepath = $path . $_FILES[$key]['name'];
			$ok = move_uploaded_file($_FILES[$key]['tmp_name'], $filepath);
			return $ok ? str_replace(WEB,'',$filepath) : false;
		}
		return false;
	}

	public static function fileControl($key = 'file'){
		$file = $file ?? $_FILES[$key]['tmp_name']; // worok on it
		$allowed = array_keys(self::mimeMap());
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file);
		finfo_close($finfo);
		if (!in_array($mime, $allowed) OR !in_array($_FILES[$key]['type'], $allowed)) {
			exit('Formato de arquivo inválido! Detalhes: ' . $mime . ' (' . $_FILES[$key]['type'] . ')');
		}
	}

	public static function mimeMap(){
		return [
			'image/jpeg' 		=> 'jpg',
			'image/png' 		=> 'png',
			'image/gif' 		=> 'gif',
			'application/pdf' 	=> 'pdf',
			'text/plain' 		=> 'txt',
			'video/mp4' 		=> 'mp4',
			'video/ogg' 		=> 'ogg',
			'text/plain' 		=> 'txt',
			# Special
			'audio/ogg' => 'ogg',
			'audio/ogg; codecs=opus' => 'ogg',
			# Others
			'application/vnd.ms-excel' => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
			'application/msword' => 'doc',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
			'application/rtf' => 'rtf'
		];
	}
}
