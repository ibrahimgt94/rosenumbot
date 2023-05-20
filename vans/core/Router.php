<?php

namespace Vans\Core;

use Loader;
use FDB\User;
use Face\Fire;
use Face\Reply\ReBase;

class Router {

  private $maps = [];

  private static $step = "nls";

  public function checkMap($loader, $los){
    ($los->has("cals.id"))
      ? $loader->reqFile("map.glass")
      : ($los->has("inline"))
        ? $loader->reqFile("map.line")
        : self::checkBase($loader,  $los->getJson());
  }

  private function checkBase($loader, $los){
    Fire::checkUser($los);
    $this->setStep(User::getStep($los->msg->from->id));
    ($this->getStep() == "nls")
      ? $loader->reqFile("map.base")
      : $this->checkData($loader, $los);
  }

  private function checkData($loader, $los){
    (in_array($los->msg->text, $this->mergeLang(ReBase::getLang(), $this->getMap()))
      OR (strpos($los->msg->text, '/start ', 0) !== false))
        ? $loader->reqFile("map.base") : $loader->reqFile("map.data");
  }

  private function mergeLang($langs, $maps){
    return array_unique(array_merge(array_keys($langs),
      array_values($langs), array_values($maps)));
  }

  private function setStep($step){
    self::$step = $step;
  }

  public function getStep(){
    return self::$step;
  }

  public function addMap($map){
    array_push($this->maps, $map);
  }

  private function getMap(){
    return $this->maps;
  }

}
