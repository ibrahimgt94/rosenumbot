<?php

namespace Vans\DB;

trait Select {

  public function field(...$field){
    $this->fields = (is_array($field[0]))
      ? ($field[0][0] == '*') ? '*' : $this->impload($field[0])
      : (($field[0] == '*') ? '*' : $this->impload($field));
    return $this;
  }

  public function impload($field){
    return implode(', ', $field);
  }

  public function table($table){
    $this->table = "`{$table}`";
    return $this;
  }

  public function join($table, $key, $opr, $val){
    $this->joins[] = "inner join `{$table}` on `{$table}`.`{$key}` {$opr} {$this->table}.`{$val}`";
    return $this;
  }

  public function where($col, $vals, $opr = "=", $bol = "and"){
    $type = "base";
    $rand = $this->randDigit();
    $this->wheres[] = compact('type', 'col', 'vals', 'opr', 'bol', 'rand');
    $this->bind($rand, $vals);
    return $this;
  }

  public function orWhere($col, $vals, $opr = "=", $bol = "and"){
    $type = "base";
    $rand = $this->randDigit();
    $this->wheres[] = compact('type', 'col', 'vals', 'opr', 'bol', 'rand');
    $this->bind($rand, $vals);
    return $this;
  }

  public function whereIn($col, $vals, $opr = "=", $bol = "and", $not = false){
    $type = "whereIn";
    foreach($vals as $val)
      $this->bind($this->randDigit(), $val);
    $this->wheres[] = compact('type', 'col', 'vals', 'opr', 'bol', 'not', 'rand');
    return $this;
  }

  public function orWhereIn($col, $vals){
    return $this->whereIn($col, $vals, "=", "or");
  }

  public function whereNotIN($col, $vals){
    return $this->whereIn($col, $vals, "=" , "and", true);
  }

  public function orWhereNotIN($col, $vals){
    return $this->whereIn($col, $vals, "=", "or", true);
  }

  public function between($col, $min, $max, $bol = "and", $not = false){
    $type = 'between';
    $rand['min'] = $this->randDigit();
    $rand['max'] = $this->randDigit();
    $this->wheres[] = compact('type', 'col', 'min', 'max', 'bol', 'not', 'rand');
    $this->bind($rand['min'], $min);
    $this->bind($rand['max'], $max);
    return $this;
  }

  public function orBetween(string $col, int $min, int $max){
    return $this->between($col, $min, $max, "or");
  }

  public function notBetween(string $col, int $min, int $max){
    return $this->between($col, $min, $max, "and", true);
  }

  public function orNotBetween(string $col, int $min, int $max){
    return $this->between($col, $min, $max, "or", true);
  }

  public function like($col, $vals, $opr = '=', $bol = 'and', $not = false){
   $type = 'like';
   $rand = $this->randDigit();
   $this->wheres[] = compact('type', 'col', 'vals', 'opr', 'bol', 'not', 'rand');
   $this->bind($rand, $vals);
   return $this;
  }

  public function orLike($col, $vals){
    return $this->like($col, $vals, "or");
  }

  public function notLike($col, $vals){
    return $this->like($col, $vals, "and", true);
  }

  public function orNotLike($col, $vals){
    return $this->like($col, $vals, "or", true);
  }

  public function limit($limit){
    $rand = $this->randDigit();
    $this->limit = "limit {$rand}";
    $this->bind($rand, $limit);
    return $this;
  }

  public function offset($offset){
    $rand = $this->randDigit();
    $this->offset = "offset {$rand}";
    $this->bind($rand, $offset);
    return $this;
  }

  public function orderBy($col, $dir){
    $this->orderby[] = compact('col', 'dir');
    return $this;
  }

}
