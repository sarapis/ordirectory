<?php
namespace App\Custom;

class Db 
{
	var $link;
	private $server, $user, $pass, $db, $lastQueryTs;
	public $rowsAffected;
	const CONNECTION_RECHEK_TIMEOUT = 10;
	
	///////////////////////////////////////////////
    function __construct($server, $user, $pass, $db) 
	{
		$this->server = $server;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
		$this->connect();
	}

	private function connect() 
	{
		try {
			$this->link = new PDO("mysql:host={$this->server};dbname={$this->db};charset=UTF8", $this->user, $this->pass);
			$this->q("SET CHARACTER SET utf8");
		}  catch (PDOException $e) {
			$this->dieErrMsg("PDO Connection create {$server}.{$db}", $e->getMessage());
		}
		$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
    public function ping() 
	{
        try {
            $this->link->query('SELECT 1');
        } catch (PDOException $e) {
            $this->connect();            // Don't catch exception here, so that re-connect fail will throw exception
        } 
        return true;
    }
	
	///////////////////////////////////////////////
	function select($query)
	{
		$res = $this->q($query);
		$newRes = [];
		if (isset($res[0]['id']))
		{
			foreach ($res as $row)
				$newRes[$row['id']] = $row;
			return $newRes;
		}
		else
			return $res;
	}

	///////////////////////////////////////////////
	function compressedSelect($query)
	{
		$resArr = $this->q($query);
		foreach ($resArr as $strIdx=>$strArr){
			if (count($strArr) <= 1) {
				$resArr[$strIdx] = array_pop($strArr);
			}
		}
		if (count($resArr) <= 1) {
			$resArr = array_pop($resArr);
		}
		return $resArr;
	}

	
	///////////////////////////////////////////////	
	function insert($table, $arr)
	{
		if (count($arr) == 0) return;
		$q = sprintf("INSERT INTO %s %s VALUES %s", $table, $this->flist($arr), $this->vlist($arr));
		return $this->qi($q);
	}


	///////////////////////////////////////////////	
	function insert_ignore($table, $arr){
		if (count($arr) == 0) return;
		$q = sprintf("INSERT IGNORE INTO %s %s VALUES %s", $table, $this->flist($arr), $this->vlist($arr));
		return $this->qi($q);
	}


	///////////////////////////////////////////////	
	function update($table, $criteria, $arr){
		if (count($arr) == 0) 
			return;
		$arr1 = [];
		$wrappedArr = $this->wrap([$arr]);
		//print_r($wrappedArr);
		$arr = array_pop($wrappedArr);
		foreach($arr as $field_name=>$field_val){
			$arr1[] = sprintf('%s=%s', $field_name, $field_val);
		}
		$set = implode(', ', $arr1);
		$q = sprintf('UPDATE %s SET %s WHERE %s', $table, $set, $criteria);
		
		return $this->qi($q);
	}


	///////////////////////////////////////////////	
	function replace($table, $arr){
		if (!count($arr)) return;
		$q = sprintf('REPLACE INTO %s %s VALUES %s', $table, $this->flist($arr), $this->vlist($arr));
		return $this->qi($q);
	}


	///////////////////////////////////////////////
	//private 
	private function wrap($arr) {
		foreach ($arr as $i=>$arr_str) {
			foreach ($arr_str as $k=>$el)
			{
				if (is_string($el))
					$arr[$i][$k] = "'" . addslashes($el) . "'";
				if (is_bool($el))
					$arr[$i][$k] = ($el) ? "TRUE" : "FALSE";
				if (is_null($el))
					$arr[$i][$k] = "NULL";
				
			}
		}
		return $arr;
	}
	
	private function flist($arr) {
		$keys = array_keys(current($arr));
		sort($keys);
		return "(" . implode(", ", $keys) . ")";
	}
	
	private function vlist($arr) {
		$v_arr = array();
		foreach ($this->wrap($arr) as $arr_str){
			ksort($arr_str);
			$v_arr[] = "(" . implode(", ", $arr_str) . ")";
		}
		return implode(", ", $v_arr);
	}
	
	///////////////////////////////////////////////
	function q($query)
	{

		if (microtime(true) - $this->lastQueryTs > self::CONNECTION_RECHEK_TIMEOUT) 		
			$this->ping();
		
		try {
			$resObj = $this->link->query($query);
			if (!$resObj) 
			{
				$this->rowsAffected = false;
				$errInfo = $this->link->errorInfo();
				$this->log('DB error (empty query result)', ['query'=>$query, 'errorInfo'=>$errInfo]);
				if (preg_match("~MySQL server has gone away~si", $errInfo[2]))  
				{
					$this->connect();
					return $this->q($query);
				} else {
					$this->dieErrMsg($query, $errMsg);
				}
			}
		}  catch (PDOException $e) {
			//$this->log('DB error (PDOException)', ['query'=>$query, 'PDOException'=>(array)$e, 'errorInfo'=>$this->link->errorInfo()]);
			$this->log('DB error (PDOException)', ['query'=>$query, 'PDOException'=>$e->getMessage(), 'errorInfo'=>$this->link->errorInfo()]);
			$errMsg = $e->getMessage();
			if (preg_match("~MySQL server has gone away~si", $errMsg))
			{
				$this->connect();
				return $this->q($query);
			} else {
				$this->dieErrMsg($query, $errMsg);
			}	
		} 
		
		if (is_object($resObj))
		{
			$this->rowsAffected = $resObj->rowCount();
			try {
				$res = $resObj->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				$errMsg = $e->getMessage();
				if (!preg_match("~SQLSTATE\[HY000\]: General error~si", $errMsg))  {
					$this->log('DB error (PDOException)', ['query'=>$query, 'PDOException'=>(array)$e, 'errorInfo'=>$this->link->errorInfo()]);
					$this->dieErrMsg($query, $errMsg);
				}
				$res = [];
			}
			$resObj->closeCursor();
		}
		else
		{
			$res = [];
			$this->rowsAffected = false;
		}
		$this->lastQueryTs = microtime(true);
		return $res;
	}

	function qi($query)
	{
		$res = $this->q($query);
		return $this->rowsAffected;
	}
	
	function dieErrMsg($q, $msg=''){
		$txt = ($msg) ? $msg : implode (',', $this->link->errorInfo());
		echo "<div class='bad' style='clear:both;'>MySQL error:{$txt}<br/>Query: {$q}</div>";
		//die();
	}
	
	function qq($query){
		foreach (mb_split(";#\s*\r\n\s*", $query) as $q) 		if ($q = trim($q)) 		$r = $this->q($q);
		return $r;
	}

	function transStart(){
		return $this->link->beginTransaction();
	}

	function transCommit(){
		return $this->link->commit();
	}

	function transRollback(){
		return $this->link->rollBack();
	}

	function log($title, $msg)
	{
		if (is_array($msg))
			$msg = print_r($msg, true);
		echo "{$title}|{$msg}\n";
	}
	
	///////////////////////////////////////////////
	function __destruct(){
	 	$this->link = NULL;
	}
	
}