<?php

namespace App;

use FDB\User;
use FDB\Temp;
use FDB\Info;
use Face\Cog;
use Face\View;
use Face\Rent;
use Face\Tg as Tele;
use Face\Reply\ReBase;
use Face\Reply\ReGlass;
use Aps\RentNum;

class getMony {

  use RentNum;

  public function inquiry($los){
    Tele::editeMsgTwo(View::page("home.inquiry"),
      ReGlass::part("getMony")->rows("ap_wa", "ap_vi", "ap_tg")
      ->rows("ap_wb", "ap_go", "ap_fb")->rows("ap_tw", "ap_qw", "ap_ig")
      ->rows("ap_mm", "ap_me", "ap_mb")->rows("ap_mt", "ap_kt", "ap_tn")
      ->rows("ap_nf", "ap_im", "ap_ds")->rows("ap_lf", "ap_am", "ap_bl")
      ->rows("ap_fu", "ap_ts", "ap_ot")
      ->part("home")->rows("_back"))->answer();
  }

  public function country($los, $param, $app){
    $app = (empty($app)) ? $param[1] : $app;
    $page = ($param[0] == "") ? 0 : $param[0];

    if((Temp::where("key", "date")->first()->val <= time()) or (Temp::where("key", "app")->first()->val != $app)){
      Temp::where("key", "date")->update(["val"=> (time() + 600)]);
      Temp::where("key", "app")->update(["val"=>$app]);
      $rows = array_map(function($row) use ($app) {
        $rowTwo = (object) Rent::getPrices($row, $app)[$row][$app];
        return ["cu_{$row}", number_format($rowTwo->count, 0), number_format(($rowTwo->cost * $this->ruble), 0)." T"];
      }, array_keys($this->getCountry()));
      Temp::where("key", "getMony")->update(["val" => json_encode($rows)]);
    }

    $rows = json_decode(Temp::where("key", "getMony")->first()->val);

    $countRow = ceil(count($rows) / 8);
    $rows = array_chunk($rows, 8);
    if($page == 0){
      $next = 1;
      $prev = 0;
    }elseif($page == 1){
      $next = 2;
      $prev = 0;
    }elseif($page == 2){
      $next = 3;
      $prev = 1;
    }elseif($page == 3){
      $next = 4;
      $prev = 2;
    }elseif($page == 4){
      $next = 4;
      $prev = 3;
    }

    Tele::editeMsgTwo(View::page("home.inquiry").$page,
      ReGlass::part("getMony")->rows("country", "count", "cost")
      ->each($rows[$page])->rows("prev;{$prev},{$app}", "next;{$next},{$app}")
      ->rows("_backInquiry"))->answer();
  }

}
