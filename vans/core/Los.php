<?php

namespace Vans\Core;

class Los {

  private static $json, $data;

  public function __get($key){
    return $this->get(strtolower(
     implode('.', preg_split('/(?=[A-Z])/', $key))
   ));
  }

  public function getUpdate($cog){
    self::$json = $this->decode((array) $cog);
    self::$data = $this->proc(self::$json);
    file_put_contents("../log/tg2.ups", json_encode(self::$data));
    
  }

  private function decode($cog){
    return json_decode($this->replace(
      array_keys($cog), array_values($cog)));
  }

  public function getJson(){
    return self::$json;
  }

  public function match($key, $val){
    return (self::$data[$key] == $val);
  }

  public function has($key){
    return isset(self::$data[$key]);
  }

  private function proc($ups){
    if(! is_object($ups)){ return; }
    foreach($ups as $k1 => $v1){
      if(is_object($v1)){
        foreach($v1 as $k2 => $v2){
          if(is_object($v2)){
            foreach($v2 as $k3 => $v3){
              if(is_object($v3)){
                foreach($v3 as $k4 => $v4){
                  if(is_array($v4)){
                    foreach($v4 as $k5 => $v5){
                      if(is_array($v5)){
                        foreach($v5 as $k6 => $v6){
                          if(is_object($v6)){
                            foreach($v6 as $k7 => $v7){
                              $key["{$k1}.{$k2}.{$k3}.{$k4}.{$k5}.{$k6}.{$k7}"] = $v7;
                            } # f7
                          }else{
                            $key["{$k1}.{$k2}.{$k3}.{$k4}.{$k5}.{$k6}"] = $v6;
                          } # if 7
                        } # f6
                      }else{
                        $key["{$k1}.{$k2}.{$k3}.{$k4}.{$k5}"] = $v5;
                      } # if 5
                    } # f5
                  }else{
                    $key["{$k1}.{$k2}.{$k3}.{$k4}"] = $v4;
                  } # if 4
                } # f4
              }else{
                $key["{$k1}.{$k2}.{$k3}"] = $v3;
              } # if 3
            } # f3
          }else{
            if(is_array($v2)){
              foreach($v2 as $b1 => $n1){
                if(is_object($n1)){
                  foreach($n1 as $b2 => $n2){
                    $key["{$k1}.{$k2}.{$b1}.{$b2}"] = $n2;
                  }
                }else{
                  $key["{$k1}.{$k2}.{$b1}"] = $n1;
                }
              } # bf 1
            }else{
              $key["{$k1}.{$k2}"] = $v2;
            } # b1
          } # f2
        } # f 2
      }else{
        $key["query.{$k1}"] = $v1;
      } # if 1
    } # f1
    return $key;
  }

  private function getUps(){
//     return file_put_contents("../log/tg.ups", 'php://input');
    return file_get_contents('php://input');
  }

  private function replace($key, $val){
    return preg_replace($key, $val, $this->getUps());
  }

  public function get($key){
    return self::$data[$key] ?? null;
  }

}
