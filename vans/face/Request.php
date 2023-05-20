<?php

namespace Face;

use Vans\Core\Facade;

class Request extends Facade {

  public static function getNameClas(){
    return \Vans\Core\Request::class;
  }

}
