<?php

# load
require_once("../vans/core/Loader.php");

# loder make
$loader = new Loader;

$loader->dirPath(__DIR__."/../");

# get clas
$loader->getClass();

# add map new
$loader->addSpace("App", "app");
$loader->addSpace("APS", "aps");

$loader->addSpace("Face", "vans/face");
$loader->addSpace("Face\DB", "vans/face/db");
$loader->addSpace("FDB", "vans/face/dbs");
$loader->addSpace("Face\Reply", "vans/face/reply");

$loader->addSpace("Vans\DB", "vans/db");
$loader->addSpace("Vans\Core", "vans/core");
$loader->addSpace("Vans\Reply", "vans/reply");

$loader->addSpace("DB", "dbs");

# load config file
$loader->getConfig("tele", "rent", "pay",
  "db", "ftp", "cp", "sms", "los");

# load mory file
$loader->reqFile("pub.mory");

# get tg update
\Face\Los::getUpdate(
  \Face\Cog::getJson("los")
);

# tg set token
\Face\Tg::setToken(
  \Face\Cog::get("tele", "token")
);

\Face\Tg::setMory(
  \Face\Los::getJson()
);

# ***** set hook *****
#
//  $pp = \Face\Tg::setHook("https://rose1.ibr4him.ir/robat.php");
//  print_r($pp);
// die;
# rent set token
\Face\Rent::setToken(
  \Face\Cog::get("rent", "token")
);

# pay set token
\Face\Pay::setToken(
  \Face\Cog::get("pay", "token")
);

# set cog to db
\Face\DB\Sql::setCog(
  \Face\Cog::getJson("db")
);

# tg update wirite to log file
\Vans\Core\Logger::write("tg.up",
  \Face\Los::getJson(), true);

# check map
\Face\Router::checkMap($loader, new \Face\Los);
