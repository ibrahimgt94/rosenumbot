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
$loader->getConfig("tele", "pay", "db");

$loader->reqFile("pub.mory");

# pay set token
\Face\Pay::setToken(
  \Face\Cog::get("pay", "token")
);

# set cog to db
\Face\DB\Sql::setCog(
  \Face\Cog::getJson("db")
);

# tg set token
\Face\Tg::setToken(
  \Face\Cog::get("tele", "token")
);

\Face\Tg::setMory(
  \Face\Los::getJson()
);
