<?php

namespace HtgroupManager\Service\Factory;

use HtgroupManager\Service\HtgroupService;
use HtgroupManager\Service\GroupManageService;

class GroupManageServiceFactory {

	public function __invoke($sm) {
		$config = $sm->get ( 'Config' );
		$HtGroupFileService = $sm->get ( 'HtgroupManager\Service\HtGroupFileService' );
		
		$service = new GroupManageService ( $HtGroupFileService );
		
		return $service;
	}

}
?>