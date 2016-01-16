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
namespace HtgroupManager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class GroupController extends AbstractActionController {
	protected $htGroupFileService = null;
	protected $htpasswdService = null;
	protected $groupManageService = null;
	protected $userService = null;

	/**
	 * Function returns false, if the current User does not have permission to edit any one of the given group
	 *
	 * @param array $groups        	
	 * @return boolean
	 */
	protected function checkActionOnGroupAllowed($groups) {
		$groupService = $this->getGroupManageService ();
		$username = $this->getUserService ()->getCurrentUser ();
		
		// If only one group the user tries to edit has wrong priviledges -> kill everything
		foreach ( $groups as $g ) {
			if (false === $groupService->isUserAllowedToManageGroup ( $username, $g )) {
				// $this->getResponse ()->setStatusCode ( 401 );
				return false;
			}
		}
		
		return true;
	}

	public function updateUserGroupsAction() {
		$user = $post = $this->getRequest ()->getPost ( 'user', '' );
		$newGroups = $post = $this->getRequest ()->getPost ( 'groups', null );
		$result = array( 
				'success' => true,
				'data' => array() 
		);
		
		// When no value is transmitted at all it will be equal to ''
		if ($newGroups == '')
			$newGroups = array();
		
		$htGroupService = $this->getHtGroupFileService ();
		
		if (empty ( $user ) || ! is_array ( $newGroups )) {
			return new JsonModel ( array( 
					'error with parameters' 
			) );
		}
		
		$oldUserGroups = $htGroupService->getGroupsByUser ( $user );
		$new = array_diff ( $newGroups, $oldUserGroups );
		$delted = array_diff ( $oldUserGroups, $newGroups );
		
		// Check Permissions
		$chkGroups = array_merge ( $new, $delted );
		if (false === $this->checkActionOnGroupAllowed ( $chkGroups )) {
			$result ['success'] = false;
			$result ['data'] = $oldUserGroups;
			$result ['msg'] = 'Sie haben hierfür keine Berechtigung.';
			return new JsonModel ( $result );
		}
		
		foreach ( $new as $n ) {
			$htGroupService->addUserToGroup ( $user, $n );
		}
		
		foreach ( $delted as $d ) {
			$htGroupService->deleteUserFromGroup ( $user, $d );
		}
		
		$result ['data'] = $htGroupService->getGroupsByUser ( $user );
		$result ['debug'] = array( 
				'new' => $new,
				'del' => $delted 
		);
		
		return new JsonModel ( $result );
	}

	/**
	 * Returns true if valid, of not it returns a String with information about the reason
	 *
	 * @param string $username        	
	 * @return boolean string
	 */
	private function isUsernameValid($username) {
		if (strlen ( $username ) <= 2)
			return "Benutzername ist zu kurz.";
		else if (preg_match ( '~[a-z_0-9.-]+~i', $username ) !== 1)
			return "Benutzername enthält ungültige Zeichen";
		else if (strpos ( $username, ' ' ) !== false)
			return "Leerzeichen sind im Benutzernamen nicht erlaubt";
		return true;
	}

	/**
	 * Returns true if valid, of not it returns a String with information about the reason
	 *
	 * @param string $username        	
	 * @return boolean string
	 */
	private function isGroupnameValid($groupname) {
		if (strlen ( $groupname ) <= 2)
			return "Gruppenname ist zu kurz.";
		else if (preg_match ( '~[a-z_0-9.-]+~i', $groupname ) !== 1)
			return "Gruppenname enthält ungültige Zeichen";
		else if (strpos ( $groupname, ' ' ) !== false)
			return "Leerzeichen sind im Gruppenname nicht erlaubt";
		return true;
	}

	private function getHtpasswdService() {
		if ($this->htpasswdService === null) {
			$sl = $this->getServiceLocator ();
			
			if (true == $sl->has ( 'HtpasswdManager\Service\HtpasswdService' )) {
				$this->htpasswdService = $sl->get ( 'HtpasswdManager\Service\HtpasswdService' );
			} else {
				return false;
			}
		}
		
		return $this->htpasswdService;
	}

	/**
	 *
	 * @return false | \HtgroupManager\Service\HtGroupFileService
	 */
	private function getHtGroupFileService() {
		if ($this->htGroupFileService === null) {
			$sl = $this->getServiceLocator ();
			$this->htGroupFileService = $sl->get ( 'HtgroupManager\Service\HtGroupFileService' );
		}
		
		return $this->htGroupFileService;
	}

	/**
	 *
	 * @return \HtgroupManager\Service\GroupManageService
	 */
	private function getGroupManageService() {
		if ($this->groupManageService === null) {
			$sl = $this->getServiceLocator ();
			$this->groupManageService = $sl->get ( 'HtgroupManager\Service\GroupManageService' );
		}
		
		return $this->groupManageService;
	}

	/**
	 *
	 * @return HtpasswdManager\Service\UserService
	 */
	private function getUserService() {
		if ($this->userService === null) {
			$sl = $this->getServiceLocator ();
			$this->userService = $sl->get ( 'HtpasswdManager\Service\UserService' );
		}
		
		return $this->userService;
	}

}
