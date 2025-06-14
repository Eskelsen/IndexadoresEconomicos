<?php

namespace App\Packs;

class Web
{
	public array $routes;
	
	public function add($in)
	{
		$this->routes[] = $in;
	}
	
	public function match()
	{
		$padrao = '/conta/{conta_id}/instancia/{instancia_id}/configurar/{alfabeto}/action';

		$resultado = '#^' . preg_replace('/{[^}]+}/', '([^/]+)', $padrao) . '$#';

		$url = "/conta/abc/instancia/123/configurar/xyz/action";

		if (preg_match($resultado, $url, $matches)) {
			print_r($matches);
		}

		preg_match_all('/{([^}]+)}/', $padrao, $fixas);

		$labels = $fixas[1];

		print_r($labels);

		unset($matches[0]);

		$len = min(count($matches), count($labels));

		$n = (object) array_combine(array_slice($labels, 0, $len), array_slice($matches, 0, $len));

		print_r($n);

		return;

		$route = '/conta/{conta_id}/instancia/{instancia_id}/configurar/{alfabeto}/action';
		// $url = '/contas/35/instancia/42/configurar/abc/action';
		$url = '/conta/35/instancia/42/configurar/abc/action';

		$params = matchRoute($route, $url);
		print_r($params);
		// Saída: ['conta_id' => '35', 'instancia_id' => '42']
	}
}
