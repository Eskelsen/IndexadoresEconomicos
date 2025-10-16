<?php

# Microframework System

function cli($msg, $savelog = false){
	if (PHP_SAPI!='cli') {
		return;
	}
	cc($msg); # [tmp] unknown: ToChange (inverter, o cc que vai chamar o cli)
	if ($savelog) {
		logfy($msg);
	}
}

function cc($msg, $color = 1){ # [tmp] unknown: ToChange (inverter, o cc que vai chamar o cli)
	// implementar
	echo strip_tags($msg) . PHP_EOL;
}

function backgroundTask($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    } else {
        exec($cmd . " > /dev/null &");  
    }
}

function domainProtection($domain){
	if (DOMAIN==$domain) {
		echo 'Esta é a versão principal da plataforma, deseja continuar? [s/N] ';
		$continue = strtolower(trim(fgets(STDIN)));
		if ($continue!='s') {
			exit("Execução encerrada\n");
		}
	}
}
