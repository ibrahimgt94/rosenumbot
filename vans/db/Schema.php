<?php

namespace Vans\DB;

trait Schema {

  public function select(){
    $this->pushSql('select');
    $this->pushSql(is_null($this->fields) ? '*' : $this->fields);
    $this->pushSql("from {$this->table}");
    $this->reJoin();
    $this->pushSql($this->reWhere());
    $this->cheOrderby();
    $this->cheLimit();
    $this->cheOffset();
    return implode(' ', $this->sql);
  }

  private function cheOrderby(){
    if(! is_null($this->orderby)){
      $this->pushSql("order by");
      foreach($this->orderby as $key => $orderby){
        $this->pushSql(($key != 0) ? ',' : null);
        $this->pushSql("{$orderby['col']} {$orderby['dir']}");
      }
    }
  }

  private function cheLimit(){
    if(! is_null($this->limit))
      $this->pushSql($this->limit);
  }

  private function cheOffset(){
    if(! is_null($this->limit))
      $this->pushSql($this->offset);
  }

  private function reJoin(){
    if(! is_null($this->joins))
      $this->pushSql(implode(' ', $this->joins));
  }

  public function insert($insert){
    $this->pushSql("insert into {$this->table}");
    foreach($insert as $key => $val){
      $rand = $this->randDigit();
      $keys[] = "`{$key}`";
      $vals[] = $rand;
      $this->bind($rand, $val); }
    $keys = implode(', ', $keys);
    $vals = implode(', ', $vals);
    $this->pushSql("({$keys}) values ({$vals})");
    $this->pushSql($this->reWhere());
    return $this->reQuery();
  }

  public function update($update){
    $this->pushSql("update {$this->table}");
    $this->reJoin();
    $this->pushSql('set');
    foreach($update as $key => $val){
      $rand = $this->randDigit();
      $keys[] = "`{$key}` = {$rand}";
      $this->bind($rand, $val); }
    if(count($keys) >= 1)
      $this->pushSql(implode(', ', $keys));
    $this->pushSql($this->reWhere());
    return $this->reQuery();
  }

  public function reQuery(){
    return ($this->query(
      implode(' ', $this->sql)
    )->rowCount() >= 1) ? true : false;
  }

  public function delete(){
    $this->pushSql("delete from {$this->table}");
    $this->reJoin();
    $this->pushSql($this->reWhere());
    return $this->reQuery();
  }

}
