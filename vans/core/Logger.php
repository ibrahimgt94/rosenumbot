<?php

namespace Vans\Core;

class Logger {

  private $append = false;//"FILE_APPEND";

  private static $path = "../log";

  public static function write(string $file, $err, $json = false){
    file_put_contents(self::$path."/{$file}", ($json) ? json_encode($err) : $err);
  }

}
