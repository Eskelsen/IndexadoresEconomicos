<?php

namespace App\Core;

use PDO;

class Data
{
	public $conex;
	public $table;
	
	public function __construct($instance = false)
	{
		global $database;
		if ($instance) {
			$database = $instance;
		}
		extract($database);
		try {
			$options = [
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_EMULATE_PREPARES   => false,
				PDO::ATTR_PERSISTENT		 => true,
				PDO::ATTR_TIMEOUT            => 60
			];
			$this->conex = new PDO("$driver:host=$host;dbname=$db", $user, $psw, $options);
			$this->conex->exec('SET NAMES utf8mb4');
			$this->conex->exec('SET time_zone = "-03:00";');
		} catch (PDOException $e) {
			error_log('Falha ao conectar com o banco de dados! Erro: ' . $e->getMessage());
			$this->conex = false;
			return false;
		}
		return $this->conex;
	}
	
	public function tab()
	{
		$ok = $this->query("SHOW TABLES LIKE '{$this->table}';");
		if (!is_array($ok) OR empty($this->table)) {
			return false;
		}
		$stmt = $this->sqlExec("DESCRIBE {$this->table}");
		$data = $stmt ? $stmt->fetchAll() : false;
		return is_array($data) ? array_column($data,'Field') : false;
	}

	public function tableFields()
	{
		if ($f = $this->tab()) {
			return implode(',',$f);
		}
		return false;
	}

	public function quote($in)
	{
		return ($this->conex) ? $this->conex->quote($in) : false;
	}

	public function insert($vs)
	{
		$f = implode(',',array_keys($vs));
		$v = array_values($vs);
		$h      = implode(',',array_fill(0,count($v),'?'));
		$stmt   = $this->sqlExec("INSERT INTO {$this->table} ($f) VALUES ($h);", $v);
		return $this->conex && $stmt ? $this->conex->lastInsertId() : false;
	}

	public function field($f,$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM {$this->table} $cond;",$v);
		return $stmt ? $stmt->fetchColumn() : false;
	}

	public function label($key,$value,$cond = null,$v = false)
	{
		if (!$cols = $this->selectAll("$key,$value",$cond,$v)) {
			return null;
		}
		foreach ($cols as $row) {
			$n[$row[$key]] = $row[$value];
		}
		return $n ?? null;
	}

	public function selectRow($f = '*',$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM {$this->table} $cond;",$v);
		return $stmt ? $stmt->fetch() : false;
	}

	public function selectColumn($f,$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM {$this->table} $cond;",$v);
		$data = $stmt ? $stmt->fetchAll() : false;
		return is_array($data) ? array_column($data,$f) : false;
	}

	public function selectAll($f = '*',$cond = null,$v = false)
	{
		$stmt = $this->sqlExec("SELECT $f FROM {$this->table} $cond;",$v);
		return $stmt ? $stmt->fetchAll() : false;
	}

	public function selectCount($f = '*',$cond = null,$v = false)
	{
		return $this->selectThing($f,'COUNT',$cond,$v);
	}

	public function selectSum($f = '*',$cond = null,$v = false)
	{
		return $this->selectThing($f,'SUM',$cond,$v);
	}

	public function selectThing($f,$op,$cond = null,$v = false)
	{
		$field = "$op($f)";
		$stmt = $this->sqlExec("SELECT $field FROM {$this->table} $cond;",$v);
		$n = $stmt ? $stmt->fetch() : false;
		return (isset($n[$field])) ? $n[$field] : 0;
	}

	public function update($a,$c,$cvs = [])
	{
		[$f,$fvs] = $this->parameterfy($a);
		$vs = array_merge($fvs,$cvs);
		$stmt = $this->sqlExec("UPDATE {$this->table} SET $f WHERE $c;",$vs);
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

	public function increment($f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE {$this->table} SET $f=$f+1 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function decrement($f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE {$this->table} SET $f=$f-1 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function activate($f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE {$this->table} SET $f=1 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function deactivate($f,$c,$v = [])
	{
		$stmt = $this->sqlExec("UPDATE {$this->table} SET $f=0 WHERE $c;",$v);
		return $stmt ? $stmt->rowCount() : false;
	}

	public function del($c,$v = [])
	{
		$stmt = $this->sqlExec("DELETE FROM {$this->table} WHERE $c;",$v);
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
