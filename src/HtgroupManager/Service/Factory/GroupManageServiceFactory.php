<?php

namespace HtgroupManager\Service\Factory;

use HtgroupManager\Service\HtgroupService;
use HtgroupManager\Service\GroupManageService;

class GroupManageServiceFactory {

	public function __invoke($sm) {
		$config = $sm->get ( 'Config' );
		$HtGroupFileService = $sm->get ( 'HtgroupManager\Service\HtGroupFileService' );
		
		// Get the users that are allowed to mange groups, or just allow everyone
		if (isset ( $config ['HtpasswdManager'] ) && isset ( $config ['HtpasswdManager'] ['usermanagement_users'] )) {
			// Array or true
			$groupManagementUsers = $config ['HtpasswdManager'] ['usermanagement_users'];
		} else {
			// Allow everyone;
			$groupManagementUsers = true;
		}
		
		// Get the users that are allowed to create NEW groups (that don't exist so far), or just allow everyone
		$userMayCreateNewGroups = $config ['HtgroupManager'] ['users_may_reate_new_groups'];
		
		$service = new GroupManageService ( $HtGroupFileService, $groupManagementUsers, $userMayCreateNewGroups );
		
		return $service;
	}

}
?>