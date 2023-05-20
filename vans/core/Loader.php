<?php

class Loader {

  private static $prefixes;

  private static $dirPath;

  public function getClass(){
    spl_autoload_register([new self, "load"]);
  }

  public function dirPath($dirPath){
    self::$dirPath = $dirPath;
  }

  private function load($clas){
    $prefix = $clas;
    while (false !== $pos = strrpos($prefix, '\\')) {
      $prefix = substr($clas, 0, $pos + 1);
      $relative = substr($clas, $pos + 1);
      $mapped = $this->mapped($prefix, $relative);
      if ($mapped) { return $mapped; }
      $prefix = rtrim($prefix, '\\');
    } # while
    return false;
  }

  private function mapped($prefix, $relative){
    if(! $this->hasPrefix($prefix)){
      $prefix = strtolower($prefix);
      $this->reqFile(
        $this->replacePath("{$prefix}{$relative}") );
      return true; }
    $prefix = $this->getPrefix($prefix);
    $file = $this->replacePath("{$prefix}/{$relative}");
    if(! $this->reqFile($file)){ return $file; }
    return false;
  }

  public function addSpace($prefix, $dir){
    $prefix = $this->trimPrefix($prefix);
    if (! $this->hasPrefix($prefix))
      $this->pushPrefix($prefix, []);
    $this->pushPrefix($prefix, $this->trimDir($dir));
  }

  private function pushPrefix($prefix, $directory){
    self::$prefixes[$prefix] = $directory;
  }

  private function hasPrefix($prefix){
    return isset(self::$prefixes[$prefix]);
  }

  private function trimDir($dir){
    return rtrim($dir, DIRECTORY_SEPARATOR) . '/';
  }

  private function trimPrefix($prefix){
    return trim($prefix, '\\') . '\\';
  }

  private function getPrefix($prefix){
    return self::$prefixes[$prefix];
  }

  private function replacePath($file){
    return str_replace('\\', '/', $file);
  }

  public function getConfig(...$rows){
    foreach($rows as $row)
      $this->reqFile("cog.{$row}");
  }

  private function path($file){
    return self::$dirPath.str_replace('.', '/', $file).".php";
  }

  private function reqs($file){
    return file_exists($file)
      ? require_once($file) : die("not found: {$file}");
  }

  public function reqFile($file){
    $this->reqs($this->path($file));
  }

}
