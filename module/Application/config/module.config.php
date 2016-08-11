<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
          'all' => array(
              'type' => 'segment',
              'options' => array(
                  'route'    => '/[:controller[/:action]]',
                  'constraints' => array(
                       'controller' => '[a-zA-Z0-9_-]+',
                       'action' => '[a-zA-Z0-9_-]+',
                  ),
                  'defaults' => array(
                    '__NAMESPACE__' => 'Application\Controller',
                    'controller' => 'Application\Controller\Index',
                    'action'     => 'index',
                  ),
              ),
          ),
          'index' => array(
              'type' => 'Zend\Mvc\Router\Http\Literal',
              'options' => array(
                  'route'    => '/',
                  'defaults' => array(
                      'controller' => 'Application\Controller\Index',
                      'action'     => 'index',
                  ),
              ),
          ),
          'draw' => array(
              'type' => 'Zend\Mvc\Router\Http\Literal',
              'options' => array(
                  'route'    => '/draw',
                  'defaults' => array(
                      'controller' => 'Application\Controller\Index',
                      'action'     => 'draw',
                  ),
              ),
          ),
          'desk_list' => array(
              'type' => 'Zend\Mvc\Router\Http\Literal',
              'options' => array(
                  'route'    => '/desk_list',
                  'defaults' => array(
                      'controller' => 'Application\Controller\Index',
                      'action'     => 'desk_list',
                  ),
              ),
          ),
          'contact' => array(
              'type' => 'Zend\Mvc\Router\Http\Literal',
              'options' => array(
                  'route'    => '/contact',
                  'defaults' => array(
                      'controller' => 'Application\Controller\Index',
                      'action'     => 'contact',
                  ),
              ),
          ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'ru_RU',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'navigation'=> array(
      'default'=> array(
        array(
          'label'=> 'Главная',
          'route'=> 'index',
        ),
        array(
          'label'=> 'Рисовать',
          'route'=> 'draw',
        ),
        array(
          'label'=> 'Список сохраненных досок',
          'route'=>'desk_list',
        ),
      ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Ajax' => Controller\AjaxController::class,
            'Application\Controller\Index' => Controller\IndexController::class
        ),
    ),
    'view_manager' => array(
        'strategies' => array('ViewJsonStrategy'),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
