<?php

namespace HtgroupManager\View\Helper\Factory;

use HtgroupManager\View\Helper\HtGroupManagerHelper;

class HtGroupManagerHelperFactory {

	public function __invoke($serviceLocator) {
		$sm = $serviceLocator->getServiceLocator ();
		$groupService = $sm->get ( 'HtgroupManager\Service\HtgroupService' );
		
		return new HtGroupManagerHelper ( $groupService );
	}

}