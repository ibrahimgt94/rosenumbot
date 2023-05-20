<?php

namespace FDB;

use Vans\Core\Facade;

class User extends Facade {

  public static function getNameClas(){
    return \DB\User::class;
  }

}
