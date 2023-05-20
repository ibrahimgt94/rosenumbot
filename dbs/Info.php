<?php

namespace DB;

use Vans\DB\Model;

class Info extends Model {

  protected $allow = ["*"];

  protected $table = "info";

  public function setApp($fromId, $app){
    $this->where("userId", $fromId)->update([
      "app" => $app ]);
  }

  public function resetAppCountry($fromId){
    $this->where("userId", $fromId)->update([
      "app" => "nls", "country" => "nls", "rentId" => 0, "number" => 0 ]);
  }

  public function setCountry($fromId, $country){
    $this->where("userId", $fromId)->update([
      "country" => $country ]);
  }

  public function setRentId($fromId, $rentId){
    $this->where("userId", $fromId)->update([
      "rentId" => $rentId ]);
  }

  public function setNumber($fromId, $number){
    $this->where("userId", $fromId)->update([
      "number" => $number ]);
  }

  public function resetNumber($fromId){
    $this->setNumber($fromId, "0");
  }

  public function getApp($fromId){
    return $this->where("userId", $fromId)->first()->app;
  }

  public function gets($fromId){
    return $this->where("userId", $fromId)->first();
  }

  public function getRent($fromId){
    return $this->where("userId", $fromId)->first()->rentId;
  }

  public function getNumber($fromId){
    return $this->where("userId", $fromId)->first()->number;
  }

}
