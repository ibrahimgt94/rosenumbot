<?php

namespace Face\DB;

use Vans\Core\Facade;

class Sql extends Facade {

  public static function getNameClas(){
    return \Vans\DB\Sql::class;
  }

}
