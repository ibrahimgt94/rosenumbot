<?php

namespace Vans\DB;

use Face\DB\Sql;

abstract class Model {

  use Relation;

  private $stm;

  public function __construct(){
    $this->stm = Sql::field($this->allow)
      ->table($this->table ?? $this->getTable());
  }

  public function getTable(){
    return end(explode("\\", get_called_class()));
  }

  public function find($id, $col = "id"){
    return $this->stm->where($col, $id);
  }

  public function field(...$field){
    return $this->stm->field($field);
  }

  public function join($table, $key, $opr, $val){
    return $this->stm->join($table, $key, $opr, $val);
  }

  public function where($col, $vals, $opr = "="){
    return $this->stm->where($col, $vals, $opr);
  }

  public function orWhere($col, $vals, $opr = "="){
    return $this->stm->orWhere($col, $vals, $opr);
  }

  public function whereIn($col, $vals, $opr = "="){
    return $this->stm->whereIn($col, $vals, $opr);
  }

  public function orWhereIN($col, $vals){
    return $this->stm->orWhereIN($col, $vals);
  }

  public function orWhereNotIN($col, $vals){
    return $this->stm->orWhereNotIN($col, $vals);
  }

  public function between($col, $min, $max){
    return $this->stm->between($col, $min, $max);
  }

  public function orBetween($col, $min, $max){
    return $this->stm->orBetween($col, $min, $max);
  }

  public function notBetween($col, $min, $max){
    return $this->stm->notBetween($col, $min, $max);
  }

  public function orNotBetween($col, $min, $max){
    return $this->stm->orNotBetween($col, $min, $max);
  }

  public function like($col, $vals, $opr = "="){
    return $this->stm->like($col, $vals, $opr);
  }

  public function notLike($col, $vals){
    return $this->stm->notLike($col, $vals);
  }

  public function orNotLike($col, $vals){
    return $this->stm->orNotLike($col, $vals);
  }

  public function limit($limit){
    return $this->stm->limit($limit);
  }

  public function offset($offset){
    return $this->stm->offset($offset);
  }

  public function orderBy($col, $dir){
    return $this->stm->orderBy($col, $dir);
  }

  public function get(){
    return $this->stm->get();
  }

  public function first(){
    return $this->stm->first();
  }

  public function count(){
    return $this->stm->count();
  }

  public function delete(){
    return $this->stm->delete();
  }

  public function update(array $update){
    return $this->stm->update($update);
  }

  public function create(array $insert){
    return $this->stm->insert($insert);
  }

}
