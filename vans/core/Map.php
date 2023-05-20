<?php

namespace Vans\Core;

use Face\Los;
use Face\Router;
use Face\Reply\ReBase;

class Map {

  private $target;

  private static $part;

  private $los;

  private $space = "\\App";

  public function __construct(){
    $this->los = Los::getJson();
  }

  public function part($part){
    self::$part = $part;
  }

  public function base($rule, $targ){
    Router::addMap($rule);
    $text = $this->los->msg->text;
    $two = $this->hasLang(ReBase::getLang(), $text);
    $this->matchFunc($rule, $two, $text, $targ);
  }

  private function hasLang($lang, $text){
    return (in_array($text, $lang)) ? $this->flip($lang, $text) : $text;
  }

  private function matchFunc($rule, $two, $text, $targ){
    return ($two == $rule) ? $this->call($this->getPart(), $targ, $text) : 'null';
  }

  private function flip($lang, $text){
    return array_flip($lang)[$text];
  }

  protected function mory($targ){
    $pes = explode('@', $targ);
    return (object) [ "targ" => $pes[0], "data" => $pes[1] ];
  }

  public function glass($rule, $targ){
    Router::addMap($rule);
    $mory = $this->mory($targ);
    $this->checkParm($rule, $mory->targ, $this->parms(), $mory->data);
  }

  private function parms(){
    return json_decode($this->los->cals->data);
  }

  private function checkParm($rule, $targ, $parm, $mory = null){
    ($parm->one == $this->getPart() AND $parm->two == $rule)
     ? $this->call($this->getPart(), $targ, $parm->three, $mory) : null;
  }

  public function data($step, $targ){
    (Router::getStep() == $step)
      ? $this->call($this->getPart(), $targ, $this->los->msg->text ) : null;
  }

  public function inline(){

  }

  private function getPart(){
    return self::$part;
  }

  private function getSpace($clas){
    $space = "{$this->space}\\{$clas}";
    return new $space;
  }

  private function call($cals, $func, $parm = null, $mory = null){
    exit(call_user_func_array([$this->getSpace($cals), $func],
      [ Los::getJson(), explode(",", $parm), $mory ]));
  }

}
