<?php

namespace Vans\Core;

class View {

  private $data;

  private $page;

  private $space = "\\View";

  public function page($page){
    [$clas, $func] = explode(".", $page);
    $this->page = (object) ["clas" => "{$this->space}\\{$clas}", "func" => $func];
    return $this;
  }

  public function with($key, $val){
    $this->data[$key] = json_decode(json_encode($val));
    return $this;
  }

  public function __toString(){
    return call_user_func([
      new $this->page->clas, $this->page->func], (object) $this->data);
  }

}
