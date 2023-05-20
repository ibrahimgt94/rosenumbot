<?php

namespace Vans\Core;

class Facade {

  public function __call($method, $parm){
    return self::call(static::getNameClas(), $method, $parm);
  }

  public static function __callstatic($method, $parm){
    return self::call(static::getNameClas(), $method, $parm);
  }

  private static function call($clas, $method, $parm){
    return call_user_func_array([new $clas, $method], $parm);
  }

}
