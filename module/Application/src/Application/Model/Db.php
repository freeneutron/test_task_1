<?php

namespace Application\Model;
use Zend\Db\Adapter\Adapter;

class Db{
  private static $meta= array();
  static $adapter;

  public function __construct($init= null){
    if($init){
      Db::init();
    }
  }
  public function get($table,array $option){
    $S= new Service;
    $sql= join(' ',array(
      'SELECT * FROM (',
      'SELECT',
      isset($option['field'])? join(',',array_map('self::quot',(Array)$option['field'])).', `'.$table.'_id`': '*',
      'FROM',
      self::quot($table),
      'WHERE',
      isset($option['where'])? self::expr($table,$option['where']): '1',
      'ORDER BY',
      self::quot($table.'_id'),
      'DESC',
      isset($option['limit'])? 'LIMIT '.$option['limit']: '',
      ') ORDER BY',
      self::quot($table.'_id'),
    ));
    // $S::dump($sql,1);
    $res1= self::$adapter->query($sql)->execute();
    $res= array();
    foreach($res1 as $row){
      $res[]= $row;
    }
    return $res;
  }
  public function del($table,$option){
    $S= new Service;
    $sql= join(' ',array(
      'DELETE FROM',
      self::quot($table),
      'WHERE',
      isset($option['where'])? self::expr($table,$option['where']): '1',
    ));
    // $S::dump($sql,1);
    $res1= self::$adapter->query($sql)->execute();
    $res= array();
    foreach($res1 as $row){
      $res[]= $row;
    }
    return $res;
  }
  public function add($table,$option){
    $S= new Service;
    if(!isset($option[0])){
      $option= [$option];
    }
    $name_list= [];
    $value_list= [];
    $quot_list= [];
    $meta_table_field= self::$meta['table'][$table]['field'];
    for($i=0; $i<count($option); $i++){
      if($i==0){
        foreach($option[$i] as $name=>$field){
          if(isset($meta_table_field[$name])){
            $name_list[]= $name;
            $quot_list[]= $meta_table_field[$name]['quot'];
          }
        }
      }
      $value_list_item= array();
      for($j=0; $j<count($name_list); $j++){
        $value= $option[$i][$name_list[$j]];
        $value= str_replace("'","''",$value);
        $value= $quot_list[$j]? "'".$value."'": $value;
        $value_list_item[]= $value;
      }
      $value_list[]= join(', ',$value_list_item);
      // if(i==16)break
    }
    $sql_part_1= join(' ',array(
      // 'INSERT OR REPLACE INTO',
      'INSERT OR IGNORE INTO',
      self::quot($table),
      '(',join(',',array_map('self::quot',$name_list)),')',
      'VALUES'
    ));
    $sql= array();
    for($i=0; $i<count($value_list); $i++){
      $sql[]= $sql_part_1.' ('.$value_list[$i].');';
    }
    $sql= join('',$sql);
    // $S::dump($sql,1);
    $res= self::$adapter->query($sql)->execute();
  }
  static function init_meta($config){
    $table= array();
    foreach($config as $name=>$table1){
      $field= array();
      $index= array();
      $name= isset($table1[$name])? $table1[$name]: $name;
      $table[$name]= array(
        'name'=> $name,
        'field'=> &$field,
        'index'=> &$index
      );
      if(isset($table1['field'])&& is_array($table1['field'])){
        foreach($table1['field'] as $name=>$field1){
          if(!$field1|| is_string($field1)){
            $type= $field1? $field1: '';
            $field1= array('type'=>$type);
          }
          if(is_array($field1)){
            $name= isset($field1['name'])&& $field1['name']? $field1['name']: $name;
            $type= isset($field1['type'])&& $field1['type']? $field1['type']: 'CHAR';
            $quote_type= preg_match("/^(CHAR|VCHAR|TEXT)/i",$type)? true: false;
            $field[$name]= array(
              'name'=> $name,
              'type'=> $type,
              'quot'=> $quote_type
            );
          }
        }
      }
      if(isset($table1['index'])&& is_array($table1['index'])){
        foreach($table1['index'] as $name=>$index1){
          if(!$index1|| is_string($index1)){
            $type= $index1? $index1: '';
            $index1= array('type'=>$type);
          }
          if(is_array($index1)){
            $name= isset($index1['name'])&& $index1['name']? $index1['name']: $name;
            $type= isset($index1['type'])&& $index1['type']? $index1['type']: '';
            $field2= isset($index1['field'])? $index1['field']: $name;
            if(is_string($field2)){
              $field2= array($field2);
            }
            $index[$name]= array(
              'name'=> $name,
              'type'=> $type,
              'field'=> $field2
            );
          }
        }
      }
      unset($field);
      unset($index);
    }
    // $S= new Service;
    // $S::dump($table,1);
    return self::$meta['table']= $table;
  }
  static function init(){
    $S= new Service;
    // self::init_meta(Config::get()['db']['table']);
    self::$adapter= new Adapter(array(
      'driver' => 'Pdo_Sqlite',
      'database' => './data/data.sqlite'
    ));

    $res= self::$adapter->query("SELECT name FROM sqlite_master WHERE type='table';")->execute();
    $table_exists= array();
    $table_drop= array();
    $meta= self::$meta['table'];
    foreach($res as $row){
      $name= $row['name'];
      if(!isset($meta[$name])){
        $table_drop[]= $name;
      }else{
        $table_exists[$name]= 1;
      }
    }
    self::drop($table_drop);
    foreach($meta as $name=>$table){
      if(!isset($table_exists[$name])&& $name[0]!='-'&& $name[0]!='_'){
        self::create($table);
      }
    }
    // $S::dump($table_exists,1);
  }
  static function create($option){
    $S= new Service;
    $sql_table= join(' ',array(
      'CREATE','TABLE','IF NOT EXISTS',
      self::quot($option['name']),
      '(',self::quot($option['name'].'_id'), 'INTEGER PRIMARY KEY AUTOINCREMENT,'
      ,self::column_def($option['field']),')',
    ));
    // $S::dump($sql_table,1);
    $res= self::$adapter->query($sql_table)->execute();
    if(isset($option['index'])&& count($option['index'])){
      foreach($option['index'] as $name=>$index){
        $sql_index= join(' ',array(
          'CREATE',
          is_string($index['type'])&& strtolower($index['type'])== 'unique'? $index['type']: '',
          'INDEX',
          'IF NOT EXISTS',
          self::quot($index['name']),
          'ON',
          self::quot($option['name']),
          '(',join(', ',array_map('self::quot',$index['field'])),')'
        ));
        // $S::dump($sql_index,1);
        $res= self::$adapter->query($sql_index)->execute();
      }
    }
  }
  static function drop($option){
    $S= new Service;
    if(!$option)return;
    $system_table= array('sqlite_sequence'=>1);
    if(!$option[0]){
      $option= array($option);
    }
    for($i=count($option); $i--;){
      if(isset($system_table[$option[$i]])){
        array_splice($option,$i,1);
      }
    }
    if(count($option)> 0){
      $sql= array();
      for($i=0; $i<count($option); $i++){
        $sql[]= join(' ',array(
          'DROP','TABLE','IF EXISTS',
          $option[$i]
        ));
      }
      $sql= join('; ',$sql);
      $res= self::$adapter->query($sql)->execute();
      // $S::dump($res,1);
    }
  }
  static function quot($name){
    return preg_replace('/[a-zA-Z0-9_]+/','`$0`',$name);
  }
  static function column_def($option){
    $sql= array();
    foreach($option as $name=>$field){
      $sql[]= join(' ',array(
        self::quot($field['name']),
        $field['type']
      ));
    }
    return join(', ',$sql);
  }
  function where($table,$option){
    $sql= self::expr($table,$option);
    return $sql? 'WHERE '. $sql: 'WHERE 1';
  }
  static function expr($table,$option){
    $sql= array();
    if(is_array($option)){
      foreach($option as $name=>$value){
        $value= $name!=$table.'_id'&& self::$meta['table'][$table]['field'][$name]['quot']? "'".$value."'": $value;
        $sql[]= self::quot($name).'='.$value;
      }
    }
    return join(',',$sql);
  }
}

Db::init_meta(Config::get()['db']['table']);
