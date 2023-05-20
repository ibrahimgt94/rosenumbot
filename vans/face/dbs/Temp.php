<?php

namespace FDB;

use Vans\Core\Facade;

class Temp extends Facade {

  public static function getNameClas(){
    return \DB\Temp::class;
  }

}
