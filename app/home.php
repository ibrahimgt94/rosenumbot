<?php

namespace App;

use FDB\User;
use FDB\Pay;
use FDB\Info;
use Face\Cog;
use Face\View;
use Face\Rent;
use Face\Tg as Tele;
use Face\Reply\ReBase;
use Face\Reply\ReGlass;
use Aps\RentNum;

class home {

  use RentNum;

  private $parentPlus = 200;

  private function channelId(){
    return str_replace("@", "", Cog::get("tele", "channel"));
  }

  private function supportId(){
    return Cog::get("tele", "supportId");
  }

  private function ReGlassUser(){
    return ReGlass::part("getMony")->rows("inquiry")->part("home")->rows("subset", "price")
      ->rows("profile", "charge")->urls("channel;ceId:{$this->channelId()}")
      ->rows("help", "support")->rows("lern");
  }

  private function ReGlassAdmin(){
    return ReGlass::part("getMony")->rows("inquiry")->part("home")->rows("subset", "price")
      ->rows("profile", "charge")->urls("channel;ceId:{$this->channelId()}")
      ->rows("help", "support")->part("panel")->rows("admin");
  }

  private function checkHasAdmin($userId){
    return (Cog::get("tele", "admin") == $userId) ? true : false;
  }

  public function boot($los){
    Tele::sendMsg($los->msg->from->id, View::page("home.boot"),
      ($this->checkHasAdmin($los->msg->from->id)) ? $this->ReGlassAdmin() : $this->ReGlassUser());
  }

  public function backHome($los){
    Tele::editeMsgTwo(View::page("home.boot")->with("ceId", $this->channelId()),
      ($this->checkHasAdmin($los->cals->from->id)) ? $this->ReGlassAdmin() : $this->ReGlassUser())->answer();
  }

  public function support(){
    Tele::editeMsgTwo(View::page("home.support")->with("supportId", $this->supportId()),
      ReGlass::part("home")->rows("_back"))->answer();
  }

  public function price($los){
    Tele::editeMsgTwo(View::page("home.price"),
    ReGlass::part("home")->rows("program")->rows("_back"))->answer();
  }

  public function subset($los){
    Tele::editeMsgTwo(View::page("home.subset")
      ->with("robat", str_replace("@", "", Cog::get("tele", "robat")))
      ->with("userId", "rg{$los->cals->from->id}"),
      ReGlass::part("home")->rows("_back"))->answer();
  }

  public function help(){
    Tele::editeMsgTwo(View::page("home.help"),
      ReGlass::part("home")->rows("Q2", "Q1")->rows("_back"))->answer();
  }

  public function Q1(){
    Tele::editeMsgTwo(View::page("home.Q1"),
      ReGlass::part("home")->rows("_backHelp"))->answer();
  }

  public function Q2(){
    Tele::editeMsgTwo(View::page("home.Q2"),
      ReGlass::part("home")->rows("_backHelp"))->answer();
  }

  public function profile($los){
    Tele::editeMsgTwo(View::page("home.profile")->with("user",
      User::field("*")->join("info", "userId", "=", "id")
      ->where("user.id", $los->cals->from->id)->first()),
      ReGlass::part("home")->rows("_back"))->answer();
  }

  public function charge($los, $parm){
    Pay::where("code", $parm[0])->delete();
    Tele::editeMsgTwo(View::page("home.charge"),
      ReGlass::part("home")->rows("P2", "P1")->rows("P4", "P3")
      ->rows("P6", "P5")->rows("_back"))->answer();
  }

  public function payment($los, $parm, $amount){
    $payCode = $this->generateCode();
    $this->createPay($los->cals->from->id, $amount, $payCode);
    $this->updateInfoMsgId($los, $payCode);
    Tele::editeMsgTwo(View::page("home.payment"),
      ReGlass::part("home")
        ->payment("_backCharge;{$payCode}", "paay;code:{$payCode}"))->answer();
  }

  public function program($los){
    $this->resetTemps($los);
    Tele::editeMsgTwo(View::page("home.program"),
      ReGlass::part("home")->rows("ap_wa", "ap_vi", "ap_tg")
      ->rows("ap_wb", "ap_go", "ap_fb")->rows("ap_tw", "ap_qw", "ap_ig")
      ->rows("ap_mm", "ap_me", "ap_mb")->rows("ap_mt", "ap_kt", "ap_tn")
      ->rows("ap_nf", "ap_im", "ap_ds")->rows("ap_lf", "ap_am", "ap_bl")
      ->rows("ap_fu", "ap_ts", "ap_ot")
      ->rows("_back"))->answer();
  }

  public function country($los, $parm, $app){
    $this->checkAppAndSetApp($los, $app);
    $this->rentSetStatus($los, 8);
    $this->setRentId($los, 0);
    Tele::editeMsgTwo(View::page("home.country"),
      ReGlass::part("home")->rows("cu_0", "cu_1", "cu_2")
      ->rows("cu_3", "cu_4", "cu_5")->rows("cu_6", "cu_7", "cu_10")
      ->rows("cu_12", "cu_13", "cu_14")->rows("cu_15", "cu_16", "cu_17")
      ->rows("cu_19", "cu_20", "cu_21")->rows("cu_22", "cu_23", "cu_32")
      ->rows("cu_33", "cu_35", "cu_36")->rows("cu_39", "cu_40", "cu_43")
      ->rows("cu_46", "cu_48", "cu_50")->rows("cu_52", "cu_54", "cu_55")
      ->rows("cu_56", "cu_57", "cu_62")->rows("cu_73", "cu_78", "cu_86")
      ->rows("_backProgram"))->answer();
  }

  public function getNum($los, $parm, $country){
    $this->checkCountryAndSet($los, $country);
    $info = $this->getInfo($los);
    $reps = $this->replaceAppAndCountry($info);
    $amount = $this->getPrice($reps, $this->ruble);
    $this->checkAmount(User::where("id", $info->userId)->first(), $reps, $amount);
    $gnum = $this->getNumber($reps);
    if($gnum->id == ""){
      exit(Tele::editeMsgTwo(View::page("home.notfoundNumber"),
        ReGlass::part("home")->rows("_backCountryTwo", "tryAgain"))->answer());
    }else{
      $this->setRentId($los, $gnum->id);
      $this->setNumber($los, $gnum->number);
      Tele::editeMsgTwo(View::page("home.getNum")
        ->with("country", $this->getNameCountry($reps->country))->with("app", $this->getNameApp($reps->app))
        ->with("amount", $amount)->with("number", $gnum->number),
        ReGlass::part("home")->rows("_backCountry", "getCode"))->answer();
    }
  }

  public function getCode($los){
    $rentId = $this->getRentId($los);
    $rentSt = $this->getStatus($rentId);
    $info = $this->getInfo($los);
    $reps = $this->replaceAppAndCountry($info);
    $amount = $this->getPrice($reps, $this->ruble);
    $gnum = $this->getNumberInfo($los->cals->from->id);
    if($rentSt->status == "STATUS_WAIT_CODE"){
      $this->getCodeOne($reps, $amount, $gnum);
    }elseif($rentSt->status == "STATUS_OK"){
      $this->infoPlusNum($los, $info);
      $tgCog = Cog::getJson("tele");
      $this->createPrice($info->userId, $reps, $amount, $gnum, $rentSt->code);
      $this->priceAmount($info->userId, $amount);
      $this->parentAmountPlus($info->userId, $this->parentPlus);
      $this->rentSetStatus($los, 6);
      $this->getCodeTwo($reps, $amount, $gnum, $rentSt->code);
      $this->sendMsgToChannel($info->userId, $tgCog->channel, $tgCog->robat, $reps, $amount, $gnum);
    }
  }

  private function getCodeOne($reps, $amount, $number){
    Tele::editeMsgTwo(View::page("home.getCodeOne")->with("country", $this->getNameCountry($reps->country))
      ->with("app", $this->getNameApp($reps->app))->with("amount", number_format($amount, 0))
      ->with("number", $number), ReGlass::part("home")
      ->rows("_backCountry", "getCode"))->answer();
  }

  private function getCodeTwo($reps, $amount, $number, $code){
    Tele::editeMsgTwo(View::page("home.getCodeTwo")->with("country", $this->getNameCountry($reps->country))
      ->with("app", $this->getNameApp($reps->app))->with("amount", number_format($amount, 0))
      ->with("number", $number)->with("code", $code), ReGlass::part("home")
      ->payment("_back", "channelTwo;ceId:{$this->channelId()}"))->answer("ok");
  }
  
  public function lern($los){
      Tele::sendPhoto($los->cals->from->id, urlencode("http://dark.rosenumber.ir/lern1.gif"), "\xE2\x9A\xA0 آموزش شارژ کردن حساب کاربری به صورت گیف");
      Tele::sendPhoto($los->cals->from->id, urlencode("http://dark.rosenumber.ir/lern2.gif"),  "\xE2\x9A\xA0  آموزش خرید شماره مجازی اندونزی به صورت گیف  ");
  }

}
