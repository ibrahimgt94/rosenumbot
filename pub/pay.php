<?php

require_once("cogPay.php");

use FDB\User;
use FDB\Pay;
use FDB\Info;
use Face\Pay as PayIr;
use Face\Cog;

$payCode = $_GET['code'];

if(! ctype_alnum($payCode)){
  exit("Error : pay code is no valid");
}

if(! isset($payCode)){
  exit("Error : pay code is empty;");
}

if(! $payInfo = Pay::where("code", $payCode)->first()){
  exit("Error : pay code is not found;");
}

if(! $user = User::where("id", $payInfo->userId)->first()){
  exit("Error : user is not found");
}

$amountTwo = array_combine(["M2000", "M4000", "M6000", "M8000", "M10000", "M20000"],
[20000, 40000, 60000, 80000, 100000, 200000]);


$amount3 = (in_array("M{$payInfo->amount}", array_keys($amountTwo))) ? "M{$payInfo->amount}" : "M2000";


$payIr = PayIr::send($amountTwo[$amount3], $user->phone);

if(! $payIr->status){
  exit($payIr->errorMessage);
}

$userInfo = Info::where("userId", $user->id)->first();

if(! Pay::where("code", $userInfo->pay)->update(["token" => $payIr->token])){
  exit("error; token is not update");
}

header("Location: https://pay.ir/pg/{$payIr->token}");
