<?php

namespace View;

class panel {

  public function boot($data){
    return "\xF0\x9F\x8D\x9F پنل ادمین \n\n".
      "Total All Charge User : {$data->fos->cash} Toman \n\n".
      "Count All User : {$data->fos->userCount} User \n\n".
      "Rent Api Cash : {$data->fos->rentCash} Rubel \n\n".
      "Total Pays : {$data->fos->pays} Pay";
  }

  public function users(){
    return "\xF0\x9F\x8D\x9F راهنمای ربات \n
    \xF0\x9F\x91\x88 \t شما با استفاده از این ربات هوشمند شماره مجازی کشور های مختلف را به صورت ارزان خریداری می کنید. \n
    \xF0\x9F\x91\x88 \t تمام روند خرید و دریافت شماره و ثبت نام در برنامه مورد نظر کاملا  اتوماتیک انجام می شود. \n";
  }

  public function info($data){
    return "\xF0\x9F\x8D\x9F پنل ادمین \n\n".
    "UserId : {$data->user->id} \n\n Name : {$data->user->name} {$data->user->family} \n\n".
    "Username : {$data->user->username} \n\n Cash : ".number_format($data->user->cash, 0)." Toman \n\n".
    "Subset : {$data->user->subset} Nafar \n\n Nums : {$data->user->nums} Sim Card";
  }

  public function priceInfo($data){
    return "\xF0\x9F\x8D\x9F پنل ادمین \n\n".
    "UserId : {$data->info->userId} \n\n Cash : {$data->info->amount} \n\n".
    "App : {$data->info->app} \n\nCuntry : {$data->info->cuntry} \n\n".
    "Code : {$data->info->code} \n\nNumber : {$data->info->number} \n\n".
    "Time : {$data->info->date}";
  }

}
