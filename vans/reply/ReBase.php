<?php

namespace Vans\Reply;

class ReBase {

  private static $langs = [];

  private $button = [];

  public function getLang(){
    return self::$langs;
  }

  public function setLang($langs){
    foreach($langs as $key => $val)
      self::$langs[$key] = $val;
  }

  public function rows(...$rows){
    foreach($rows as $row)
      $button[] = $this->checkRowLang($row);
    array_push($this->button, $button);
    return $this;
  }

  private function checkRowLang($row){
    return (in_array($row, array_keys(self::$langs)))
      ? ['text' => self::$langs[$row]] : ['text' => $row];
  }

  public function __toString(){
    return json_encode(['keyboard' => $this->button,
     'resize_keyboard' => true]);
  }

}
