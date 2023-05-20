<?php

namespace DB;

use Vans\DB\Model;
use FDB\Info;

class User extends Model {

  protected $allow = ["*"];

  protected $table = "user";

  public function getStep($userId){
    return Info::where("userId", $userId)->first()->step;
  }

  public function setStep($userId, $step){
    return Info::where("userId", $userId)->update(["step"=> $step]);
  }

  public function has($userId){
    return ($this->where("id", $userId)->count() == 1);
  }

}
