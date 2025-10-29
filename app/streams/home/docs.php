<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Documentação da API de Indexadores Econômicos">
    <meta name="author" content="Daniel Eskelsen">
    <title>Documentação da API de Indexadores Econômicos</title>
	<link rel="icon" href="https://microframeworks.com/ups/mf-icon-sm.png">

    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/starter-template/">
	<link href="https://unotify.mfwks.com/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
	  
	.icon-list li::before {
	  display: block;
	  flex-shrink: 0;
	  width: 1.5em;
	  height: 1.5em;
	  margin-right: .5rem;
	  content: "";
	  background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23212529' viewBox='0 0 16 16'%3E%3Cpath d='M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z'/%3E%3C/svg%3E") no-repeat center center / 100% auto;
	}

	.bold {
		font-weight: bold;
	}

    </style>
  </head>
  <body id="topo">
    
<div class="col-lg-8 mx-auto p-4 py-md-5">
  <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
	<a class="navbar-brand" href=".">
		<img src="https://microframeworks.com/ups/mf-icon-sm.png" width="40">
		<span style="vertical-align: middle;font-weight:bold;font-size:24px;padding-left:4px;color:#555">Microframeworks</span>
	</a>
  </header>

  <main>

    <h1>Indexadores Econômicos</h1>
    <p class="fs-5 col-md-8">Repositório com APIs públicas para consulta de indexadores econômicos brasileiros (IGP-M, IPCA, salário mínimo e outros), atualizados a partir de fontes oficiais.</p>

	<p><small class="bold">Última atualização: <code><?= date('H:i \h d/m/Y', filemtime(__FILE__)); ?></code></small></p>

<hr class="mb-4">

<h3 id="usoelimites">Objetivo</h3>
<p class="fs-6 pt-2">Disponibilizar o aumento percentual dos índices no acumulado nos últimos 12 meses ou ano corrente (salário mínimo).</p>

<h3 id="ai">Endpoints</h3>
<p class="card card-body">
	<a href="https://api.mfwks.com/indexadores/ipca" target="_blank">https://api.mfwks.com/indexadores/ipca</a>
	<a href="https://api.mfwks.com/indexadores/igpm" target="_blank">https://api.mfwks.com/indexadores/igpm</a>
	<a href="https://api.mfwks.com/indexadores/salario-minimo" target="_blank">https://api.mfwks.com/indexadores/salario-minimo</a>
</p>

<hr class="mb-5">
  </main>
  <footer class="pt-5 my-5 text-muted border-top text-center">
    Microframeworks &middot; São Paulo &middot; &copy; <?= date('Y'); ?>
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

</body>
</html>