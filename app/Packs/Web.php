<?php

namespace App\Packs;

class Web
{
	public array $routes;
	
	public function init($in = '')
	{
		$call = $this->request($in);
		return $this->search($call);
	}
	
	public function add($in, $mid = false, $acl = false)
	{
		$this->routes[] = [$in, $mid, $acl];
	}
	
	public function request($in = '')
	{
		$in = trim($in,'/');
		$request = $_SERVER['REQUEST_URI'] ?? '/';
		return preg_replace('/^\/' . $in . '/', '', parse_url($request, PHP_URL_PATH));
	}
	
	public function search($in)
	{
		if (empty($this->routes)) {
			return false;
		}
		foreach ($this->routes as $route) {
			if ($this->match($in,$route[0])) {
				print_r('kkk');
				return $route;
			}
		}
		return false;
	}
	
	public function match($url,$padrao)
	{
		$resultado = '#^' . preg_replace('/{[^}]+}/', '([^/]+)', $padrao) . '$#';

		if (!preg_match($resultado, $url, $matches)) {
			return false;
		}
		
		print_r($matches); // The problem laying here
		
		if (!preg_match_all('/{([^}]+)}/', $padrao, $fixas)) {
			// return false;
		}
		
		print_r($fixas);

		$labels = $fixas[1];

		unset($matches[0]);

		$len = min(count($matches), count($labels));

		return (object) array_combine(array_slice($labels, 0, $len), array_slice($matches, 0, $len));
	}
}
