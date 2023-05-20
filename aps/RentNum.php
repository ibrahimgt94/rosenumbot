<?php

namespace APS;

use FDB\Info;
use FDB\User;
use FDB\Pay;
use Face\Rent;
use FDB\Price;
use Face\Tg as Tele;
use Face\View;
use Face\Reply\ReGlass;


trait RentNum {

  protected $ruble = 600;

  private $countrys = [
  "0" => "🇷🇺 روسیه 🇷🇺",
  "1" => "🇺🇦 اوکراین 🇺🇦",
  "2" => "🇰🇿 قزاقستان 🇰🇿",
  "3" => "🇨🇳 چین 🇨🇳",
  "4" => "🇵🇭 فیلیپین 🇵🇭",
  "5" => "🇲🇲 میانمار 🇲🇲",
  "6" => "🇮🇩 اندونزی 🇮🇩",
  "7" => "🇲🇾 مالزی 🇲🇾",
  "10" => "🇻🇳 ویتنام 🇻🇳",
  "12" => "🇺🇸 آمریکا 🇺🇸",
  "13" => "🇮🇱 اسرائيل 🇮🇱",
  "14" => "🇭🇰 هنگ کنگ 🇭🇰",
  "15" => "🇵🇱 لهستان 🇵🇱",
  "16" => "🇬🇧 انگلستان 🇬🇧",
  "17" => "🇲🇬 ماداگاسکار 🇲🇬",
  "19" => "🇳🇬 نیجریه 🇳🇬",
  "20" => "🇲🇴 ماکائو 🇲🇴",
  "21" => "🇪🇬 مصر 🇪🇬",
  "22" => "🇮🇳 هند 🇮🇳",
  "23" => "🇮🇪 ایرلند 🇮🇪",
  "32" => "🇷🇴 رومانی 🇷🇴",
  "33" => "🇨🇴 کلمبیا 🇨🇴",
  "35" => "🇦🇿 آذربایجان 🇦🇿",
  "36" => "🇨🇦 کانادا 🇨🇦",
  "39" => "🇦🇷 آرژانتین 🇦🇷",
  "40" => "🇺🇿 ازبکستان 🇺🇿",
  "43" => "🇩🇪 آلمان 🇩🇪",
  "46" => "🇸🇪 سوئد 🇸🇪",
  "48" => "🇳🇱 هلند 🇳🇱",
  "50" => "🇦🇹 اتریش 🇦🇹",
  "52" => "🇹🇭 تایلند 🇹🇭",
  "54" => "🇲🇽 مکزیک 🇲🇽",
  "55" => "🇹🇼 تایوان 🇹🇼",
  "56" => "🇪🇸 اسپانیا 🇪🇸",
  "57" => "🇮🇷 ایران 🇮🇷",
  "62" => "🇹🇷 ترکیه 🇹🇷",
  "73" => "🇧🇷 برزیل 🇧🇷",
  "78" => "🇫🇷 فرانسه 🇫🇷",
  "86" => "🇮🇹 ایتالیا 🇮🇹"];

  private $apps = [ "wa" => "\xF0\x9F\x8D\x84 واتساپ",
  "vi" => "\xF0\x9F\x8D\x84 وایبر",
  "tg" => "\xF0\x9F\x8D\x84 تلگرام",
  "wb" => "\xF0\x9F\x8D\x84 وی چت",
  "go" => "\xF0\x9F\x8D\x84 گوگل",
  "fb" => "\xF0\x9F\x8D\x84 فیسبوک",
  "tw" => "\xF0\x9F\x8D\x84 توییتر",
  "qw" => "\xF0\x9F\x8D\x84 ولت کیوی",
  "ig" => "\xF0\x9F\x8D\x84 اینستاگرام",
  "mm" => "\xF0\x9F\x8D\x84 مایکروسافت",
  "me" => "\xF0\x9F\x8D\x84 لاین",
  "mb" => "\xF0\x9F\x8D\x84 یاهو",
  "mt" => "\xF0\x9F\x8D\x84 استیم",
  "kt" => "\xF0\x9F\x8D\x84 کاکائو تاک",
  "tn" => "\xF0\x9F\x8D\x84 لینکدین",
  "nf" => "\xF0\x9F\x8D\x84 نتفلیکس",
  "im" => "\xF0\x9F\x8D\x84 ایمو",
  "ds" => "\xF0\x9F\x8D\x84 دیسکورد",
  "lf" => "\xF0\x9F\x8D\x84 تیک تاک",
  "am" => "\xF0\x9F\x8D\x84 آمازون",
  "bl" => "\xF0\x9F\x8D\x84 بیگو لایو",
  "fu" => "\xF0\x9F\x8D\x84 اسنپ چت",
  "ts" => "\xF0\x9F\x8D\x84 پیپال",
  "ot" => "\xF0\x9F\x8D\x84 دیگر"];

  public function getCountry(){
    return $this->countrys;
  }

  protected function checkAppAndSetApp($los, $app){
    Info::setApp($los->cals->from->id, ($app == "nls")
      ? Info::getApp($los->cals->from->id) : $app);
  }

  protected function rentSetStatus($los, $val){
    if(Info::getRent($los->cals->from->id) != 0)
      Rent::setStatus(Info::getRent($los->cals->from->id), $val);
  }

  protected function getstatus($rentId){
    return Rent::getStatus($rentId);
  }

  protected function setRentId($los, $val){
    Info::setRentId($los->cals->from->id, $val);
  }

  protected function getRentId($los){
    return Info::getRent($los->cals->from->id);
  }

  protected function resetTemps($los){
    Info::resetAppCountry($los->cals->from->id);
  }

  protected function resetNumber($los){
    Info::resetNumber($los->cals->from->id);
  }

  protected function checkCountryAndSet($los, $country){
    if($country != "nls")
      Info::setCountry($los->cals->from->id, $country);
  }

  protected function getInfo($los){
    return Info::gets($los->cals->from->id);
  }

  protected function replaceAppAndCountry($info){
    return (object) ["app" => str_replace("ap_", "", $info->app),
      "country" => str_replace("cu_", "", $info->country)];
  }

  protected function getNumber($reps){
    return Rent::getNumber($reps->app, $reps->country, 0, "tele");
  }

  protected function setNumber($los, $val){
    Info::setNumber($los->cals->from->id, $val);
  }

  protected function getPrice($reps, $ruble){
    return (Rent::getPrices($reps->country, $reps->app)[$reps->country][$reps->app]["cost"] * $ruble);
  }

  protected function generateCode(){
    return substr(md5(microtime()), 8, 8);
  }

  protected function createPay($userId, $amount, $code){
    Pay::create([ "amount" => $amount,
      "userId" => $userId, "code" => $code, "date" => time() ]);
  }

  protected function updateInfoMsgId($los, $code){
    Info::where("userId", $los->cals->from->id)->update([
      "pay" => $code, "msgId" => $los->cals->msg->id ]);
  }

  protected function getNumberInfo($fromId){
    return info::getNumber($fromId);
  }

  protected function infoPlusNum($los, $info){
    return Info::where("userId", $los->cals->from->id)->update([
      "nums" => ($info->nums + 1) ]);
  }

  protected function createPrice($userId, $reps, $amount, $gnum, $code){
    Price::create([ "userId" => $userId,
      "number" => $gnum, "amount" => $amount,
      "app" => $reps->app, "country" => $reps->country,
      "code" => $code, "date" => time() ]);
  }

  protected function sendMsgToChannel($userId, $channel, $robatId, $reps, $amount, $gnum){
    Tele::sendMsg($channel, View::page("home.sendMsgChannel")
      ->with("userId", substr($userId, 0, 7)."XXX")->with("robatId", $robatId)->with("amount", number_format($amount, 0))
      ->with("country", $this->getNameCountry($reps->country))->with("app", $this->getNameApp($reps->app))->with("gnum", substr($gnum, 0, 7)."XXX"));
  }

  protected function getNameCountry($country){
    return (array_key_exists($country, $this->countrys)) ? $this->countrys[$country] : "null_1";
  }

  protected function getNameApp($app){
    return (array_key_exists($app, $this->apps)) ? $this->apps[$app] : "null_2";
  }

  protected function priceAmount($userId, $amount){
    User::where("id", $userId)->update([
      "cash" => ((User::where("id", $userId)->first()->cash) - $amount) ]);
  }

  protected function checkAmount($user, $reps, $amount){
    if($amount > $user->cash){
      exit(Tele::editeMsgTwo(View::page("home.chargeTwo")->with("country", $this->getNameCountry($reps->country))
      ->with("app", $this->getNameApp($reps->app))->with("amount", number_format($amount, 0))
      ->with("cash", number_format($user->cash, 0)), ReGlass::part("home")
      ->rows("_backCountryTwo", "charge"))->answer());
    }
  }

  protected function parentAmountPlus($userId, $parentPlus){
    $user = User::where("id", $userId)->first();
    if($user->parent != 0){
      User::where("id", $user->parent)->update([
        "cash" => (User::where("id", $user->parent)->first()->cash + $parentPlus) ]);
    }
  }

}
