<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'HtgroupManager\Controller\Group' => 'HtgroupManager\Controller\GroupController' 
				) 
		),
		'router' => array (
				'routes' => array (
						'htgroupmanager' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/groupmanagement',
										'defaults' => array (
												'__NAMESPACE__' => 'HtgroupManager\Controller',
												'controller' => 'Group',
												'action' => 'index' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										'user' => array (
												'type' => 'Segment',
												'options' => array (
														// Change this to something specific to your module
														'route' => '/user/[:user]/[:action][/:groupname]',
														'constraints' => array (
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'user' => '[a-zA-Z0-9.!?_-]+',
																'groupname' => '[a-zA-Z0-9.!?_-]+' 
														),
														'defaults' => array (
																'user' => null,
																'action' => 'listGroups',
																'groupname' => null 
														) 
												) 
										),
										'group' => array (
												'type' => 'Segment',
												'options' => array (
														// Change this to something specific to your module
														'route' => '/group/:action[/:groupname]',
														'constraints' => array (
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'groupname' => '[a-zA-Z][a-zA-Z0-9._-]+' 
														),
														'defaults' => array (
																'groupname' => null 
														) 
												) 
										),
										'ajax' => array (
												'type' => 'Segment',
												'options' => array (
														// Change this to something specific to your module
														'route' => '/ajax/:action',
														'constraints' => array (
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*' 
														),
														'defaults' => array () 
												) 
										) 
								) 
						) 
				) 
		),
		'view_manager' => array (
				'template_path_stack' => array (
						'HtgroupManager' => __DIR__ . '/../view' 
				),
				'strategies' => array (
						'ViewJsonStrategy' 
				) 
		),
		'HtgroupManager' => array (
				// Carefull! File needs to be writeable by apache-user (www-data)
				// The .htaccess file needs to be set to use this .htpasswd_HtpasswdManager file for authentication
				'htgroup' => '/var/www/.htpasswd_HtpasswdManager' 
		) 
);
