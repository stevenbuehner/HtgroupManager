<?php

namespace HtgroupManager\Service;

use HtgroupManager\Service\HtgroupService;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class GroupManageService implements EventManagerAwareInterface {
	protected $HtGroupFileService;
	protected $eventManager;
	protected $groupManagementUsers = array();
	protected $userMayCreateNewGroups = true;

	public function __construct($HtGroupFileService, $groupManagementUsers, $userMayCreateNewGroups) {
		$this->HtGroupFileService = $HtGroupFileService;
		$this->groupManagementUsers = $groupManagementUsers;
		$this->userMayCreateNewGroups = $userMayCreateNewGroups;
	}

	public function getGroupsUserIsAllowedToManage($user) {
		$groups = array();
		
		$eResult = $this->getEventManager ()->trigger ( 'pre_' . __FUNCTION__, $this, array( 
				'user' => $user,
				'groups' => $groups 
		) );
		if ($eResult->stopped ()) {
			return $eResult->last ();
		}
		
		$allGroups = $this->HtGroupFileService->getGroups ();
		
		foreach ( $allGroups as $groupName => $users ) {
			$groups [] = $groupName;
		}
		
		$eResult = $this->getEventManager ()->trigger ( 'post_' . __FUNCTION__, $this, array( 
				'user' => $user,
				'groups' => $groups 
		) );
		if ($eResult->stopped ()) {
			return $eResult->last ();
		}
		
		return $groups;
	}

	public function getGroupsUserIsAssignedTo($user) {
		$userGroups = $this->HtGroupFileService->getGroupsByUser ( $user );
		
		$eResult = $this->getEventManager ()->trigger ( 'post_' . __FUNCTION__, $this, array( 
				'groups' => $userGroups 
		) );
		if ($eResult->stopped ())
			return $eResult->last ();
		
		return $userGroups;
	}

	public function isUserAllowedToManageGroup($username, $groupname) {
		$eResult = $this->getEventManager ()->trigger ( 'pre_' . __FUNCTION__, $this, array( 
				'user' => $username,
				'group' => $groupname 
		) );
		if ($eResult->stopped ()) {
			return $eResult->last ();
		}
		
		if ($this->groupManagementUsers === true || (is_array ( $this->groupManagementUsers )) && in_array ( $username, $this->groupManagementUsers )) {
			return true;
		}
		
		return false;
	}

	public function isUserAllowedToCreateNewGroups($username) {
		$eResult = $this->getEventManager ()->trigger ( 'pre_' . __FUNCTION__, $this, array( 
				'user' => $username 
		) );
		if ($eResult->stopped ()) {
			return $eResult->last ();
		}
		
		if (is_bool ( $this->userMayCreateNewGroups )) {
			return $this->userMayCreateNewGroups;
		}
		
		return (is_array ( $this->userMayCreateNewGroups ) && in_array ( $username, $this->userMayCreateNewGroups ));
	}

	public function setEventManager(EventManagerInterface $eventManager) {
		$eventManager->setIdentifiers ( array( 
				__CLASS__,
				get_class ( $this ) 
		) );
		$this->eventManager = $eventManager;
	}

	public function getEventManager() {
		return $this->eventManager;
	}

}
?>