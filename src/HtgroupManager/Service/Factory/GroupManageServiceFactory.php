<?php

namespace HtgroupManager\Service\Factory;

use HtgroupManager\Service\HtgroupService;
use HtgroupManager\Service\GroupManageService;

class GroupManageServiceFactory {

	public function __invoke($sm) {
		$config = $sm->get ( 'Config' );
		$HtGroupFileService = $sm->get ( 'HtgroupManager\Service\HtGroupFileService' );
		
		if (isset ( $config ['HtpasswdManager'] ) && isset ( $config ['HtpasswdManager'] ['usermanagement_users'] )) {
			// Array or true
			$groupManagementUsers = $config ['HtpasswdManager'] ['usermanagement_users'];
		} else {
			// Allow everyone;
			$groupManagementUsers = true;
		}
		
		$service = new GroupManageService ( $HtGroupFileService, $groupManagementUsers );
		
		return $service;
	}

}
?>