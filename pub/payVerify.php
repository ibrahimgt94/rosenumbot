<?php

require_once("cogPay.php");

use FDB\User;
use FDB\Pay;
use FDB\Info;
use Face\Pay as PayIr;
use Face\Cog;
use Face\Tg as Tele;
use Face\View;
use Face\Reply\ReGlass;

$get = (object) $_GET;

if(! isset($get->status) And ! isset($get->token)){
  exit("error; status And token is not empty");
}

# validation

$payIr = PayIr::verify($get->token);

$cogTgRobatId = str_replace("@", "", Cog::get("tele", "robat"));

if($payIr->status == 0){

  $dbPay = Pay::where("token", $get->token)->join("info", "userId", "=", "userId")->first();

  Pay::where("token", $get->token)->update([
    "date" => time(),
    "message" => $get->errorMessage,
    "status" => $get->errorCode
  ]);

  Info::where("userId", $dbPay->userId)
    ->update(["pay" => "nls"]);

  header("Location: https://telegram.me/{$cogTgRobatId}");

  exit(Tele::EditeMsg($dbPay->userId, $dbPay->msgId, View::page("home.chargeErr"),
    ReGlass::part("home")->rows("P2", "P1")->rows("P4", "P3")
    ->rows("P6", "P5")->rows("_back"))->answer());

}

$dbPay = Pay::where("token", $get->token)->join("info", "userId", "=", "userId")->first();

if(Pay::where("transId", $payIr->transId)->count() >= 1){
  exit("error; transId is exists");
}

Pay::where("token", $get->token)->update([
  "date" => time(),
  "message" => $payIr->message,
  "transId" => $payIr->transId,
  "status" => $payIr->status
]);


$amountTwo = array_combine(["M20000", "M40000", "M60000", "M80000", "M100000", "M200000"],
    ["2000", "4000", "6000", "8000", "10000", "20000"]);


$amount3 = (in_array("M{$payIr->amount}", array_keys($amountTwo))) ? "M{$payIr->amount}" : "2000";


$cashTwo = (User::where("id", $dbPay->userId)->first()->cash + $amountTwo[$amount3]);

User::where("id", $dbPay->userId)->update(["cash" => $cashTwo]);

Info::where("userId", $dbPay->userId)->update(["pay" => "nls"]);

header("Location: https://telegram.me/{$cogTgRobatId}");

exit(Tele::EditeMsg($dbPay->userId, $dbPay->msgId, View::page("home.chargeSuc")
  ->with("amount", $amountTwo[$amount3])->with("cash", $cashTwo)->with("transId", $payIr->transId),
  ReGlass::part("home")->rows("_back"))->answer());
