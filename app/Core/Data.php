<?php

namespace App\Core;

use PDO;

class Data
{
	public $conex;
	
	public function __construct()
	{
		try {
			$options = [
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_EMULATE_PREPARES   => false,
				PDO::ATTR_PERSISTENT		 => true,
				PDO::ATTR_TIMEOUT            => 60
			];
			$this->conex = new PDO("mysql:host=".HOST.";dbname=".DB, USER, PSW, $options);
			$this->conex->exec('SET NAMES utf8mb4');
			$this->conex->exec('SET time_zone = "-03:00";');
		} catch (PDOException $e) {
			error_log('Falha ao conectar com o banco de dados! Erro: ' . $e->getMessage());
			$this->conex = false;
			return false;
		}
		return $this->conex;
	}
	
	public function tab($t)
	{
		$ok = query("SHOW TABLES LIKE '$t';");
		if (!is_array($ok) OR empty($t)) {
			return false;
		}
		$stmt = $this->sqlExec("DESCRIBE $t");
		$data = $stmt ? $stmt->fetchAll() : false;
		return is_array($data) ? array_column($data,'Field') : false;
	}

	public function db($db)
	{
		$tmp_dbs = query('SHOW DATABASES;');
		$lista_dbs = is_array($tmp_dbs) ? array_column($tmp_dbs, 'Database') : false;
		return is_array($lista_dbs) ? in_array($db, $lista_dbs) : false;
	}

	public function tableFields($t)
	{
		if ($f = tab($t)) {
			return implode(',',$f);
		}
		return false;
	}

	public function quote($in)
	{
		return ($this->conex) ? $this->conex->quote($in) : false;
	}

	public function insert($t,$vs)
	{
		$f = implode(',',array_keys($vs));
		$v = array_values($vs);
		$h      = implode(',',array_fill(0,count($v),'?'));
		$stmt   = $this->sqlExec("INSERT INTO $t ($f) VALUES ($h);", $v);
		return $this->conex && $stmt ? $this->conex->lastInsertId() : false;
	}

	public function field($t,$f,$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM $t $cond;",$v);
		return $stmt ? $stmt->fetchColumn() : false;
	}

	public function label($tab,$key,$value,$cond = null,$v = false)
	{
		if (!$cols = selectAll($tab,"$key,$value",$cond,$v)) {
			return null;
		}
		foreach ($cols as $row) {
			$n[$row[$key]] = $row[$value];
		}
		return $n ?? null;
	}

	public function selectRow($t,$f = '*',$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM $t $cond;",$v);
		return $stmt ? $stmt->fetch() : false;
	}

	public function selectColumn($t,$f,$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM $t $cond;",$v);
		$data = $stmt ? $stmt->fetchAll() : false;
		return is_array($data) ? array_column($data,$f) : false;
	}

	public function selectAll($t,$f = '*',$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM $t $cond;",$v);
		return $stmt ? $stmt->fetchAll() : false;
	}

	public function selectCount($t,$f = '*',$cond = null,$v = false)
	{
		return selectThing($t,$f,'COUNT',$cond,$v);
	}

	public function selectSum($t,$f = '*',$cond = null,$v = false)
	{
		return selectThing($t,$f,'SUM',$cond,$v);
	}

	public function selectThing($t,$f,$op,$cond = null,$v = false)
	{
		$field = "$op($f)";
		$stmt = $this->sqlExec("SELECT $field FROM $t $cond;",$v);
		$n = $stmt ? $stmt->fetch() : false;
		return (isset($n[$field])) ? $n[$field] : 0;
	}

	public function update($t,$a,$c,$cvs = [])
	{
		[$f,$fvs] = parameterfy($a);
		$vs = array_merge($fvs,$cvs);
		$stmt = $this->sqlExec("UPDATE $t SET $f WHERE $c;",$vs);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function parameterfy($array)
	{
		foreach ($array as $field => $value) {
			$sets[] = "$field=?";
			$values[] = ($value) ? trim("$value",' \'') : $value;
		}
		return [implode(',',$sets),$values];
	}

	public function increment($t,$f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE $t SET $f=$f+1 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function decrement($t,$f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE $t SET $f=$f-1 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function activate($t,$f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE $t SET $f=1 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function deactivate($t,$f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE $t SET $f=0 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function del($t,$c,$v = [])
	{
		$stmt = $this->sqlExec("DELETE FROM $t WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function query($q)
	{
		if ($stmt = $this->sqlExec($q)) {
			if ($affected = $stmt->rowCount()) {
				return ($affected>1) ? $stmt->fetchAll() : $stmt->fetch();
			}
			return true;
		}
		return false;
	}

	public function sqlExec($sql, $v = false, $recursive = false)
	{
		if (!$this->conex) {
			return false;
		}
		try {
			$stmt = $this->conex->prepare($sql);
			$made = $v ? $stmt->execute($v) : $stmt->execute();
		} catch (PDOException $e) {
			if (($e->getCode()=='HY000') AND ($recursive==false)) {
				$this->conex = reconnect();
				return $this->sqlExec($sql, $v, true);
			}
			error_log('[MicroDB] Falha ao realizar operação com o banco de dados! Erro: ' . $e->getMessage());
		}
		return empty($made) ? false : $stmt;
	}

	public function reconnect()
	{
		global $start_time;
		$execution_time = round(microtime(true) - $start_time,2);
		error_log("[MicroDB] reconnect $execution_time", TMP . 'sql.txt');
		return new Data();
	}
}
