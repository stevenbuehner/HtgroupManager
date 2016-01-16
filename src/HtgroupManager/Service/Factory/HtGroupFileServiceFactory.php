<?php

namespace HtgroupManager\Service\Factory;

use HtgroupManager\Service\HtGroupFileService;

class HtGroupFileServiceFactory {

	public function __invoke($sm) {
		$config = $sm->get ( 'Config' );
		
		if (! isset ( $config ['HtgroupManager'] ) || ! is_array ( $config ['HtgroupManager'] ) || ! isset ( $config ['HtgroupManager'] ['htgroup'] ) || empty ( $config ['HtgroupManager'] ['htgroup'] )) {
			throw new \Exception ( 'HtgroupManager Config not found' );
		}
		$htgroup_filename = $config ['HtgroupManager'] ['htgroup'];
		
		$groupService = new HtGroupFileService ( $htgroup_filename );
		
		return $groupService;
	}

}
?>