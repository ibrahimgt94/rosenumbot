<?php

namespace Vans\Core;

use Face\Logger as Log;
use Face\Request as Req;

class Telegram {

  private static $calsId;

  private static $calsMsgId;

  private static $calsFromId;

  private static $token;

  private $uri = "https://api.telegram.org";

  public function setToken($token){
    self::$token = $token;
  }

  public function setMory($mory){
    [self::$calsId, self::$calsFromId, self::$calsMsgId] = [
      $mory->cals->id, $mory->cals->from->id, $mory->cals->msg->id];
  }

  private function request(string $func, string $parm = null){
    return json_decode(Req::post("{$this->uri}/bot".self::$token."/{$func}?", $parm));
  }

  public function getHook(){
    return $this->request("getWebhookInfo");
  }

  public function setHook(string $url, int $num = 30){
    return $this->request("setWebhook", "url={$url}&max_connections={$num}");
  }

  public function sendMsg($chatId, string $text, $reply = null){
    return $this->request("sendMessage",
      "chat_id={$chatId}&text={$text}&reply_markup={$reply}&parse_mode=HTML");
  }

  public function forwardMsg($chatId, $fromId, $msgId){
    return $this->request("forwardMessage",
      "chat_id={$chatId}&from_chat_id={$fromId}&message_id={$msgId}");
  }

  public function sendPhoto($chatId, $photo, $caption, $reply = null){
    return $this->request("sendPhoto",
      "chat_id={$chatId}&photo={$photo}&caption={$caption}&reply_markup={$reply}&parse_mode=HTML");
  }

  public function sendAudio($chatId, $audio, $thumb, $caption, $reply){
    return $this->request("sendAudio",
      "chat_id={$chatId}&audio={$audio}&thumb={$thumb}&caption={$caption}&reply_markup={$reply}&parse_mode=HTML");
  }

  public function kick($chatId, $userId){
    return $this->request("kickChatMember", "chat_id={$chatId}&user_id={$userId}");
  }

  public function unban($chatId, $userId){
    return $this->request("unbanChatMember", "chat_id={$chatId}&user_id={$userId}");
  }

  public function getFile($fileId){
    return $this->request("getFile", "file_id={$fileId}");
  }

  public function getMember($chatId, $userId){
    return $this->request("getChatMember", "chat_id={$chatId}&user_id={$userId}"); }

  public function getAdmins($chatId){
    return $this->reques("getChatAdministrators", "chat_id={$chatId}");
  }

  public function getMemberCount(){
    return $this->request("getMembersCount", "chat_id={$chatId}");
  }

  public function answer($text = null, $alert = false){
    return $this->request("answerCallbackQuery",
      "callback_query_id=".self::$calsId."&text={$text}&show_alert={$alert}");
  }

  public function leave($chatId){
    return $this->request("leaveChat", "chat_id={$chatId}");
  }

  public function editeMsg($chatId, $msgId, $text, $reply = null){
    $this->request("editMessageText",
      "chat_id={$chatId}&message_id={$msgId}&text={$text}&reply_markup={$reply}&parse_mode=HTML");
    return $this;
  }

  public function editeMsgTwo($text, $reply = null){
    return $this->editeMsg(self::$calsFromId, self::$calsMsgId, $text, $reply);
  }

  public function deleteMsg($chatId, $msgId){
    return $this->request("deleteMessage", "chat_id={$chatId}&message_id={$msgId}");
  }

}
