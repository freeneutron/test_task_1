<?php

namespace Application\Model;
use Zend\Config\Reader;

class Config{
  private static $config= array();
  static function get(){
    return self::$config;
  }
  static function init(){
    // $S= new Service;
    $path= __DIR__.'/../../Config';
    foreach(scandir($path) as $name){
      if($name=='.'|| $name=='..')continue;
      $ext= pathinfo($name,PATHINFO_EXTENSION);
      switch($ext){
        case'yaml':
          if(!isset($yaml_reader)){
            $yaml_reader= new Yaml;
          }
          // $S::dump($name,1);
          $config= $yaml_reader->loadFile("$path/$name");
          $name= pathinfo($name,PATHINFO_FILENAME);
          self::$config[$name]= $config;
          $this[$name]= $config;
          break;
      }
    }
  }
}
Config::init();
