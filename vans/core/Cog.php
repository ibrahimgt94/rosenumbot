<?php

namespace Vans\Core;

class Cog {

  private static $data;

  public function add($name, $vals){
    self::$data[$name] = $vals;
  }

  public function getJson($key){
    return (object) self::$data[$key];
  }

  public function get($key, $pars){
    return $this->getJson($key)->$pars;
  }

}
