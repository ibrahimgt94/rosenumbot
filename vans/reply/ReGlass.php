<?php

namespace Vans\Reply;

class ReGlass {

  private $btns = [];

  private static $part;

  private static $langs = [];

  private static $uris = [];

  private function json($one, $two, $three){
    return json_encode([ "one" => $one, "two" => $two, "three" => $three]);
  }

  private function data($text, $one, $two, $three = null){
    return [ "text" => $text, "callback_data" => $this->json($one, $two, $three) ];
  }

  private function url($text, $url){
    return [ "text" => $text, "url" => urlencode(urldecode($url)) ];
  }

  public function part($part){
    self::$part = $part;
    return $this;
  }

  public function rows(...$rows){
    array_push($this->btns, $this->_rows($rows));
    return $this;
  }

  private function _rows($rows){
    $langs = $this->getLang();
    foreach($rows as $rowe){
      $row = $this->rowZero($rowe);
      $btns[] = $this->hasLngToLangs($row,
        self::$part.":{$row}", $langs, $this->imploadParm(explode(';', $rowe)));
    }
    return $btns;
  }

  private function hasLngToLangs($row, $lng, $langs, $parm){
    return (in_array($lng, array_keys($langs)))
      ? $this->data($langs[$lng], self::$part, $row, $parm)
      : $this->data($row, self::$part, $row, $parm);
  }


  public function urls(...$urls){
    array_push($this->btns, $this->_Urls($urls));
    return $this;
  }

  private function _urls($urls){
    foreach($urls as $url){
      $upart = $this->urlParts($url);
      $uparm = $this->urlParms($upart->parm);
      $btns[] = $this->checkRuleToUris($upart->rule,
        $uparm->key, $uparm->val);
    } # foreach 1
    return $btns;
  }

  private function urlParms($rule){
    foreach(explode(',', $rule) as $data){
        $vals = explode(':', $data);
        $key[] = "{{$vals[0]}}";
        $val[] = $vals[1];
      } # foreach
    return (object) ["key" => $key, "val" => $val];
  }

  private function urlParts($url){
     [$rule, $parm] = explode(";", $url);
     return (object) ["rule" => $rule, "parm" => $parm];
  }

  private function checkRuleToUris($rule, $key, $val){
    if(in_array($rule, array_keys(self::$uris))){
      $rule = $this->getUris($rule);
      return $this->url($rule->text,
        $this->replaceKeyUrl($key, $val, $rule->url));
    }
  }

  public function each($rows){
    foreach($rows as $row){
      array_push($this->btns, $this->_rows($row));
    } # row
    return $this;
  }

  private function getUris($rule){
    return (object) self::$uris[$rule];
  }

  private function replaceKeyUrl($key, $val, $url){
    return str_replace($key, $val, $url);
  }

  public function payment($row, $url){
    array_push($this->btns, [ $this->_rows([$row])[0],
      $this->_urls([$url])[0] ]);
    return $this;
  }

  public function setLang($name, $langs){
    foreach($langs as $key => $val)
      self::$langs["{$name}:{$key}"] = $val;
  }

  public function getLang(){
    return self::$langs;
  }

  public function imploadParm($row){
    return implode(",", array_slice($row, 1));
  }

  private function rowZero($row){
    return explode(";", $row)[0];
  }

  public function uri($uris){
    self::$uris = $uris;
  }

  public function __toString(){
    return json_encode(['inline_keyboard' => $this->btns]);
  }

}
