<?php

namespace Vans\Core;

use Face\Request as Req;

class Rent {

  private static $token;

  private $uri = "https://sms-activate.ru/stubs/handler_api.php";

  public function setToken($token){
    self::$token = $token;
  }

  private function reqGet($func, $parm = null, $json = null, $getNum = null){
    $result = Req::get("{$this->uri}?", "api_key=".self::$token."&action={$func}&{$parm}");
    return $this->reqPars($result, $json , $getNum);
  }

  private function reqPost($func, $parm = null, $json = null, $getNum = null){
    $result = Req::post("{$this->uri}?", "api_key=".self::$token."&action={$func}&{$parm}");
    return $this->reqPars($result, $json, $getNum);
  }

  private function reqPars($result, $json, $getNum){
    if($json) return json_decode($result, true);
    $result = explode(":", $result);
    if($getNum == 1) return ["id" => $result[1], "number" => $result[2] ];
    if($getNum == 2) return ["status" => $result[0], "code" => $result[1] ];
    if($getNum == 3) return ["status" => $result[0]];
    return $result[1];
  }

  public function getBalance(){
    return $this->reqGet("getBalance");
  }

  public function getNumber($service, $country = null, $forward = 0, $operator = null, $ref = null){
    $service = ($operator And ($country == 0 Or $country == 1 Or $country == 2)) ? $operator : $service;
    return (object) $this->reqPost("getNumber",
      "service={$service}&country={$country}&forward={$forward}&ref={$ref}", null, 1);
  }

  public function getNumStatus($country = null, $operator = null){
    $service = ($operator And ($country == 0 Or $country == 1 Or $country == 2))
      ? $operator : $service;
    $response = [];
    $result = $this->reqGet("getNumbersStatus",
      "service={$service}&country={$country}", true);
    foreach ($result as $services => $count){
        $services = trim($services, "_01");
        $response[$services] = $count; }
    unset($result);
    return (object) $response;
  }

  public function setStatus($id, $status, $forward = 0){
    return $this->reqPost("setStatus", "id={$id}&status={$status}&forward&={$forward}", null, 3);
  }

  public function getStatus($id){
    return (object) $this->reqGet("getStatus", "id={$id}", null, 2);
  }

  public function getPrices($country = null, $service = null){
    $service = (! is_null($service)) ? "service={$service}" : "";
    $country = (! is_null($country)) ? "country={$country}" : "";
    return $this->reqGet("getPrices", "{$country}&{$service}", true);
  }

}
