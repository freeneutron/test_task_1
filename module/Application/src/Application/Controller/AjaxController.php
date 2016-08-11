<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Model;

class AjaxController extends AbstractActionController{
  public function saveAction(){
    $param= $this->params()->fromPost('data');
    if(is_string($param)&& $param[0]== '{'){
      $data= json_decode($param,1);
      $data['state_array']= json_encode($data['state_array']);
      $db= new Model\Db(1);
      if(count($db->get('desk',array(
        'where'=>array(
          'name'=>$data['name']
        ),
      )))&& !$data['loaded']){
        return new JsonModel(array('res'=>'0','data'=>$data));
      }else{
        $db->del('desk',array(
          'where'=>array(
            'name'=>$data['name']
          ),
        ));
        $db->add('desk',$data);
        return new JsonModel(array('res'=>'1','data'=>$data));
      }
    }
  }
  public function loadAction(){
    $name= $this->params()->fromPost('name');
    $db= new Model\Db(1);
    if($name){
      $res= $db->get('desk',array(
        'where'=>array(
          'name'=>$name
        ),
      ));
      $res= $res[0];
      $res['state_array']= json_decode($res['state_array'],1);
    }else{
      $res= $db->get('desk',array(
        'field'=>array(
          'name',
          'time_create',
          'time_last_modify',
          'timer',
        ),
      ));
    }
    return new JsonModel($res);
  }
  public function deleteAction(){
    $name= $this->params()->fromPost('name');
    if($name){
      $db= new Model\Db(1);
      $db->del('desk',array(
        'where'=>array(
          'name'=>$name
        ),
      ));
      return new JsonModel(array('res'=>'1'));
    }
  }
}
