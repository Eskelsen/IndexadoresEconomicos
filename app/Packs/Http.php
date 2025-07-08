<?php

namespace App\Packs;

class http
{
	public function response($msgdata, $code = 200){
		$response_key = ($code<400) ? 'response' : 'error';
		$response[$response_key] = $msgdata;
		$code = $this->http($code);
		header("HTTP/1.0 $code");
		exit(jsonPretty($response));
	}

	public function http($in){
		$n[200] = '200 OK';
		$n[206] = '206 Partial Content';
		$n[207] = '207 Multi-Status';
		$n[304] = '304 Not Modified';
		$n[400] = '400 Bad Request';
		$n[401] = '401 Unauthorized';
		$n[403] = '403 Forbidden';
		$n[404] = '404 Not Found';
		$n[500] = '500 Internal Server Error';
		$n[501] = '501 Not Implemented';
		$n[502] = '502 Bad Gateway';
		$n[503] = '503 Service Unavailable';
		return $n[$in] ?? $n[500];
	}

	public function goodStatus($in){
		$out = filter_var($in, FILTER_VALIDATE_URL) ? get_headers($in) : 'This is not a valid URL.';
		if (!empty($out[0]) AND (strpos($out[0],' 20')!==false OR strpos($out[0],' 30')!==false)) {
			return true;
		}
		echo is_array($out) ? $out[0] : $out;
		return false;
	}

	public function getHttpResponseCode($in){
		$out = get_headers($in);
		return substr($out[0], 9, 3);
	}

	public function dispatcher($url, $data = '', $method = 'GET', $type = true){
		
		$curl = curl_init();
		$data = is_string($data) ? $data : json_encode($data);
		
		$application = ($type) ? 'application/json' : 'application/x-www-form-urlencoded; charset=utf-8';
		
		$headers = [
			'Content-Type: ' 		 . $application,
			'Content-Length: ' 		 . strlen($data)
		];
		
		curl_setopt_array($curl, [
			CURLOPT_URL             => $url,
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_ENCODING        => '',
			CURLOPT_MAXREDIRS       => 10,
			CURLOPT_TIMEOUT         => 30,
			CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST   => $method,
			CURLOPT_POSTFIELDS      => $data,
			CURLOPT_HTTPHEADER      => $headers
		]);
		
		$response = curl_exec($curl);

		$e = curl_error($curl);
		
		curl_close($curl);
		
		if ($e) {
			exit(json_encode(['status' => false, 'message' => $e]));
		}
		
		return $response;
	}
}
	