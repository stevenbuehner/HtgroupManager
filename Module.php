<?php

/**
 * 
 * (c) Steven Bühner <buehner@me.com>

 * @author Steven Bühner
 * @license MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HtgroupManager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use HtgroupManager\Service\HtgroupService;

class Module implements AutoloaderProviderInterface {

	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								// if we're in a namespace deeper than one level we need to fix the \ in the path
								__NAMESPACE__ => __DIR__ . '/src/' . str_replace ( '\\', '/', __NAMESPACE__ ) 
						) 
				) 
		);
	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function onBootstrap(MvcEvent $e) {
		// You may not need to do this if you're doing it elsewhere in your
		// application
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
	}

	public function getServiceConfig() {
		return array (
				'factories' => array (
						'HtgroupManager\Service\HtgroupService' => function ($sm) {
							$config = $sm->get ( 'Config' );
							
							if (! isset ( $config ['HtgroupManager'] ) || ! is_array ( $config ['HtgroupManager'] ) || ! isset ( $config ['HtgroupManager'] ['htgroup'] ) || empty ( $config ['HtgroupManager'] ['htgroup'] )) {
								throw new \Exception ( 'HtgroupManager Config not found' );
							}
							$htgroup_filename = $config ['HtgroupManager'] ['htgroup'];
							
							$groupService = new HtgroupService ( $htgroup_filename );
							
							return $groupService;
						} 
				) 
		);
	}

}