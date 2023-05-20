<?php

namespace App;

use FDB\User;
use FDB\Pay;
use FDB\Info;
use FDB\Price;
use Face\Cog;
use Face\View;
use Face\Rent;
use Face\Tg as Tele;
use Face\Reply\ReBase;
use Face\Reply\ReGlass;
use Aps\RentNum;

class panel {

  use RentNum;

  private $limitCount = 10;

  private function getInfo(){
    return [
      "cash" => number_format(Pay::field("SUM(amount) as allCash")->where("status", 1)->first()->allCash, 0),
      "userCount" => User::count(),
      "rentCash" => Rent::getBalance(),
      "pays" => Pay::where("status", 1)->count()
    ];
  }

  public function users($los, $param){

    $page = $this->pageing($param[0]);

    if($page->next > $page->count){
      $page->next = $page->count;
      $msg = "is page last";
    }

    $rows = array_map(function($row){
      return [$row->id, number_format($row->cash, 0)." T", "info;{$row->id}"];
    }, User::limit($this->limitCount)->offset($page->start)->get());

    Tele::editeMsgTwo(View::page("panel.boot")->with("fos", $this->getInfo()),
      ReGlass::part("panel")->rows("reload")->rows("userId", "userCash", "userInfo")
        ->each($rows)->rows("prev;{$page->last}", "search", "next;{$page->next}")->part("home")->rows("_back"))
        ->answer($msg ?? "");
  }

  public function info($los, $param){
    Tele::editeMsgTwo(View::page("panel.info")
      ->with("user", User::join("info", "userId", "=", "id")->where("id", $param[0])->first()),
      $this->plusReGlass($param[0]))->answer();
  }

  private function plusReGlass($plus){
    return ReGlass::part("panel")->rows("mgCash")->rows("P3;{$plus}", "P2;{$plus}", "P1;{$plus}")
    ->rows("P6;{$plus}", "P5;{$plus}", "P4;{$plus}")
    ->rows("_backUser;{$plus}", "charge;{$plus}", "price;{$plus}");
  }

  private function plusReGlassTwo($plus){
    return ReGlass::part("panel")->rows("mgCash")->rows("P3;{$plus}", "P2;{$plus}", "P1;{$plus}")
    ->rows("P6;{$plus}", "P5;{$plus}", "P4;{$plus}")
    ->rows("_dele", "charge;{$plus}", "price;{$plus}");
  }

  public function plusCashUser($los, $param, $mony){
    Tele::editeMsgTwo(View::page("home.charge"),
      ReGlass::part("panel")->rows("_backInfo;{$param[0]}", "okMinus;{$param[0]},{$mony}", "okPlus;{$param[0]},{$mony}"))->answer();
  }

  public function cashUserTwo($los, $param, $type){

    if($type == "plus"){
      Pay::create([
        "userId" => $param[0],
        "amount" => $param[1],
        "code" => substr(md5(time()), 0, 8),
        "message" => $type,
        "date" => time(),
        "status" => 2
      ]);
    }

    $cashUps = ($type == "plus") ? (User::where("id", $param[0])->first()->cash + $param[1]) :
      (User::where("id", $param[0])->first()->cash - $param[1]);

    User::where("id", $param[0])->update(["cash" => $cashUps ]);

    Tele::editeMsgTwo(View::page("panel.info")
      ->with("user", User::join("info", "userId", "=", "id")->where("id", $param[0])->first()),
      $this->plusReGlass($param[0]))->answer();
  }

  public function charge($los, $param){
    $rows = array_map(function($row){
      return [number_format($row->amount, 0), ($row->transId) ?? "\xE2\x80\xBC", date("H:i", $row->date),
        (($row->status == 1) ? "\xF0\x9F\x93\x97" : (($row->status == 2) ? "\xF0\x9F\x93\x98" : "\xF0\x9F\x93\x95"))];
    }, Pay::where("userId", $param[0])->get());
    Tele::editeMsgTwo(View::page("panel.users"),
      ReGlass::part("panel")->rows("amount", "transId", "date", "status")
      ->each($rows)->rows("_backInfo;{$param[0]}"))->answer();
  }

  public function price($los, $param){
    $rows = array_map(function($row){
      return [$row->number, ($row->code) ?? "\xE2\x80\xBC", "priceInfo;{$row->userId}"];
    }, Price::where("userId", $param[0])->get());
    Tele::editeMsgTwo(View::page("panel.users"),
      ReGlass::part("panel")->rows("number", "code", "mory")
      ->each($rows)->rows("_backInfo;{$param[0]}"))->answer();
  }

  public function priceInfo($los, $param){

    $price = Price::where("userId", $param[0])->first();

    Tele::editeMsgTwo(View::page("panel.priceInfo")->with("info", [
      "userId" => $price->userId,
      "amount" => $price->amount,
      "app" => $this->getNameApp($price->app),
      "cuntry" => $this->getNameCountry($price->country),
      "code" => $price->code,
      "number" => $price->number,
      "date" => date("Y-m-d , h:i:s", $price->date),
    ]), ReGlass::part("panel")->rows("_backPrice;{$param[0]}"))->answer();
  }

  public function search($los, $param){
    User::setStep($los->cals->from->id, "search");
    Tele::editeMsgTwo("pls send user id", ReGlass::part("panel")->rows("_back"));
  }

  public function search1($los){

    $userId = $los->msg->text;

    if(! is_numeric($userId)){
      exit(Tele::sendMsg($los->msg->from->id, "plus send id user eg. 854123564"));
    }

    if(strlen($userId) < 7 OR strlen($userId) > 12){
      exit(Tele::sendMsg($los->msg->from->id, "plus send id user rang 7 ... 12"));
    }

    if(User::where("id", $userId)->count() == 0){
      exit(Tele::sendMsg($los->msg->from->id, "user not found"));
    }

    User::setStep($los->msg->from->id, "nls");

    Tele::sendMsg($los->msg->from->id, View::page("panel.info")
      ->with("user", User::join("info", "userId", "=", "id")->where("id", (int) $userId)->first()),
      $this->plusReGlass((int) $userId))->answer();

  }

  private function pageing($param){
    $page = ($param[0] == "") ? 1 : $param[0];
    $page = (int) $page;
    $start = ($page - 1) * $this->limitCount; // 0

    $count = ceil(User::count() / $this->limitCount); // 45 / 10 = 5

    $last = ($page == 1) ? 1 : ($page - 1);
    $next = ($page + 1);

    return (object) ["start" => (int) $start, "last" => (int) $last, "next" => (int) $next, "count" => (int) $count];
  }


}
