<?php

namespace Vans\Core;

use Face\Request as Req;

class Pay {

  private static $token;

  public function setToken($token){
    self::$token = $token;
  }

  public function send($amount, $phone){
    return Req::pay("https://pay.ir/pg/send", [
      "api" => self::$token,
      "redirect" => urlencode("https://rosenumber.ir/dark/pub/payVerify.php"),
      "mobile" => $phone, "amount" => $amount ]);
  }

  public function verify($token){
    return Req::pay("https://pay.ir/pg/verify", [
      "api" => self::$token, "token" =>  $token]);
  }

}
