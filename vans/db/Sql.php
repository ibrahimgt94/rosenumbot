<?php

namespace Vans\DB;

use PDO;
use Exception;
use PDOStatement;
use PDOException;
use Vans\Core\Logger;

class Sql {

  use Select, Schema;

  private $binds = [], $whs, $sql = [], $sqlTwo = [],
    $joins, $fields, $wheres, $table,
    $limit, $offset, $orderby;

  private static $cog;

  public function setCog($cog){
    self::$cog = $cog;
  }

  private function link($cog){
    return "mysql:host={$cog->host};dbname={$cog->database};port={$cog->port}";
  }

  private function connect($cog){
    try{ $stm = new PDO ($this->link($cog),
        $cog->username, $cog->password);
      $stm->exec("set names {$cog->charset}");
      return $stm; }catch(PDOException $e){ exit($this->debug($e)); }
  }

  private function debug($err){
    Logger::write("db.con", [
      "line" => $err->getLine(), "file" => $err->getFile(),
      "msg" => $err->getMessage() ], true);
  }

  private function logger($stm, $sql){
    Logger::write("db.sql", ["sql" => $sql, "parm" => $this->binds], true);
    Logger::write("db.err", $stm->errorInfo(), true);
  }

  private function query($sql){
    $stm = $this->connect(self::$cog)->prepare($sql);
    $stm->setFetchMode(PDO::FETCH_OBJ);
    $this->binding($stm);
    $stm->execute();
    $this->logger($stm, $sql);
    return $stm;
  }

  private function bind($key, $val){
    $this->binds[$key] = $val;
  }

  private function binding($stm){
    foreach($this->binds as $key => $val)
      $stm->bindValue($key, $val, is_int($val)
        ? PDO::PARAM_INT : PDO::PARAM_STR);
  }

  private function randDigit(){
    $rand = ":s".substr(md5(microtime()), 0, 4);
    return array_key_exists($rand, $this->binds)
      ? $this->randDigit() : $rand;
  }

  private function selQuery(){
    return $this->query($this->select());
  }

  public function get(){
    return $this->selQuery()->fetchAll();
  }

  public function first(){
    return $this->selQuery()->fetch();
  }

  public function count(){
    return $this->selQuery()->RowCount();
  }

  private function pushSql($key){
    array_push($this->sql, $key);
  }

  private function pushSqlTwo($key){
    array_push($this->sqlTwo, $key);
  }

  private function checkType($type){
    return ($this->whs->type == $type) ? true : false;
  }

  private function checkBase($col){
    if($this->checkType("base")){
      $this->pushSqlTwo("{$col} {$this->whs->opr} {$this->whs->rand}");
    }
  }

  private function checkWhereIn($col){
    if($this->checkType("whereIn")){
      $this->pushSqlTwo("{$col} ". ($this->whs->not ? 'NOT IN' : 'IN') ." (".implode(', ', $this->whs->rand).")");
    }
  }

  private function checkLike($col){
    if($this->checkType("like")){
      $this->pushSqlTwo("{$col} ". ($this->whs->not ? 'NOT LIKE' : 'LIKE') ." {$this->whs->rand}");
    }
  }

  private function checkBetween($col){
    if($this->checkType("between")){
      $this->pushSqlTwo("{$col} ". ($this->whs->not ? 'NOT BETWEEN' : 'BETWEEN') ."
        {$this->whs->rand->min} AND {$this->whs->rand->max}");
    }
  }

  private function checkColumn(){
    return (strpos($this->whs->col, '.') != false)
      ? '`'.implode('`.`', explode('.', $this->whs->col)).'`'
      : "`{$this->whs->col}`";
  }

  private function checkOprType(){
    if(! is_null($this->whs->bol))
      return ($this->whs->bol == "and") ? 'and' : 'or';
  }

  private function wheres($where = null){
    if(empty($where)) return null;
    $this->pushSqlTwo('where');
    foreach($where as $key => $whs){
      $this->whs = (object) $whs;
      $this->pushSqlTwo(($key != 0) ? $this->checkOprType() : null);
      $col = $this->checkColumn();
      $this->checkBetween($col);
      $this->checkLike($col);
      $this->checkWhereIn($col);
      $this->checkBase($col); }
      // print_r($this->sqlTwo);
    return implode(' ', $this->sqlTwo);
  }

  private function reWhere(){
    return $this->wheres($this->wheres);
  }

}
