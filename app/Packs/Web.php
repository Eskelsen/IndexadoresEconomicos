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
	
	public function add($url, $mid = false, $acl = false)
	{
		$this->routes[] = (object) ['url' => $url, 'mid' => $mid, 'acl' => $acl];
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
			if ($args = $this->match($in,$route->url)) {
				if (is_object($args)) {
					$route->args = $args;
				}
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
		
		if (!(preg_match_all('/{([^}]+)}/', $padrao, $fixas))) {
			return true;
		}

		$labels = $fixas[1];

		unset($matches[0]);

		$len = min(count($matches), count($labels));

		return (object) array_combine(array_slice($labels, 0, $len), array_slice($matches, 0, $len));
	}
}
