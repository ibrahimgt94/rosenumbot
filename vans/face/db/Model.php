<?php

namespace Face\DB;

use Vans\Core\Facade;

class Model extends Facade {

  public static function getNameClas(){
    return \Vans\DB\Model::class;
  }

}
