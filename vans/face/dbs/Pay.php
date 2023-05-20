<?php

namespace FDB;

use Vans\Core\Facade;

class Pay extends Facade {

  public static function getNameClas(){
    return \DB\Pay::class;
  }

}
