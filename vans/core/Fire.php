<?php

namespace Vans\Core;

use FDB\User;
use FDB\Info;
use Face\Cog;
use Face\Tg as Tele;
use Face\Reply\ReBase;

class Fire {

  private $part = "\\App\\home";

  private $func = "boot";

  private $preCash = "200";

  public function checkUser($los){
    $part = $this->getPart($los->msg->text);
    if(User::has($los->msg->from->id)){
      if($this->checks($part)){
        if($part->userId == $this->getParent($part->parId)){
          $this->sendMsgTryAgain($part->userId);
        }else{
          $this->goBoot($this->part, $this->func);
        } # if
      } # if
    }else{
      if($this->checks($part)){
        $parId = $this->getParent($part->parId);
        if(User::has($parId)){
          $this->plusCash($parId);
          $this->plusSubset($parId);
          $this->regeUser($los, $parId);
          $this->regeInfo($los);
          $this->goBoot($this->part, $this->func);
        }else{
          $this->regeUser($los, 0);
          $this->regeInfo($los);
          $this->goBoot($this->part, $this->func);
        } # if
      }else{
        if($los->msg->text == '/start'){
          $this->regeUser($los, 0);
          $this->regeInfo($los);
        } # if
      } # if
    } # if
  } # check user

  private function checks($part){
    return ($part->comand == '/start') AND (strpos($part->parId, 'rg') !== false);
  }

  private function regeUser($los, $parId = 0){
    return User::create([
      "id" => $los->msg->from->id,
      "name" => $los->msg->from->fname ?? "null",
      "family" => $los->msg->from->lname ?? "null",
      "username" => $los->msg->from->username ?? "null",
      "parent" => $parId,
    ]);
  }

  private function regeInfo($los){
    return Info::create(["userId" => $los->msg->from->id]);
  }

  private function plusCash($userId){
    User::where("id", $userId)->update([
      "cash" => ((User::where("id", $userId)->first()->cash) + $this->preCash) ]);
  }

  private function plusSubset($userId){
    return User::where("id", $userId)->update([
      'subset' => (User::where("id", $userId)->first()->subset + 1) ]);
  }

  private function getPart($part){
    [$comand, $parId] = explode(' ', $part);
    return (object) [ 'comand' => $comand, 'parId' => $parId ];
  }

  private function getParent($parId){
    preg_match('/([0-9]{2,10})/', $parId, $parent);
    return (int) $parent[0];
  }

  private function goBoot($part, $func){
    exit(call_user_func_array([new $part, $func], [Los::getJson()]));
  }

  private function sendMsgTryAgain($userId){
    exit( Tele::sendMsg($userId, "Try Again", ReBase::rows("tryAgain")) );
  }

}
