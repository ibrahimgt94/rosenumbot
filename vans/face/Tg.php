<?php

namespace Face;

use Vans\Core\Facade;

class Tg extends Facade {

  public static function getNameClas(){
    return \Vans\Core\Telegram::class;
  }

}
