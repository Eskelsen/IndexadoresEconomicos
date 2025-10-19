<?php

namespace App\Core;

class Web
{
	public array $routes;
	
	public $url;
	public $stream;
	public $acl;
	
	public function init($in = '')
	{
		$call = $this->request($in);
		var_dump($in,$call);
		$this->search($call);
	}
	
	public function add($url, $stream = false, $acl = false)
	{
		$this->routes[] = (object) ['url' => $url, 'stream' => $stream, 'acl' => $acl];
	}
	
	public function request($in = '')
	{
		$in = trim($in,'/');
		$request = $_SERVER['REQUEST_URI'] ?? '/';
		return preg_replace('/^\/' . $in . '/', '', parse_url($request, PHP_URL_PATH));
	}
	
	public function search($in)
	{
		echo 'search: ' . $in . PHP_EOL;
		if (empty($this->routes)) {
			return false;
		}
		foreach ($this->routes as $route) {
			echo 'route: ' . $route->url . PHP_EOL;
			if ($args = $this->match($in,$route->url)) {
				echo 'match' . PHP_EOL;
				if (is_object($args)) {
					$this->args = $args;
					$this->url = $route->url;
					echo $this->url . PHP_EOL;
					$this->stream = $route->stream;
					echo $this->stream . PHP_EOL;
					$this->acl = $route->acl;
					echo $this->acl . PHP_EOL;
				}
				return $this;
			}
		}
		return false;
	}
	
	public function match($url,$padrao)
	{
		$resultado = '#^' . preg_replace('/{[^}]+}/', '([^/]+)', $padrao) . '$#';

		if (!preg_match($resultado, $url, $matches)) {
			echo 'false 1: ' . $resultado . PHP_EOL;
			return false;
		}
		
		if (!(preg_match_all('/{([^}]+)}/', $padrao, $fixas))) {
			echo 'false 2' . PHP_EOL;
			return true;
		}

		$labels = $fixas[1];

		unset($matches[0]);

		$len = min(count($matches), count($labels));

		return (object) array_combine(array_slice($labels, 0, $len), array_slice($matches, 0, $len));
	}
}
