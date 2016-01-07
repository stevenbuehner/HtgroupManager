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

class GroupController extends AbstractActionController {
	private $htgroupService = null;
	private $htpasswdService = null;

	function indexAction() {
		$htpasswd = $this->getHtpasswdService ();
		$userList = $htpasswd->getUserList ();
		
		$result = array ();
		foreach ( $userList as $username => $pass ) {
			$result [] = array (
					'username' => $username,
					'paswd' => $pass,
					'isAdmin' => $htpasswd->isUserAllowedToManageUsers ( $username ),
					'isDeletable' => $htpasswd->isUserDeleteable ( $username ) 
			);
		}
		
		$model = new ViewModel ( array (
				'userList' => $result 
		) );
		
		return $model;
	}

	public function listGroupsAction() {
		$user = $this->params ( 'user' );
		
		return $this->getViewModellForListGroupActions ( $user );
	}

	private function getViewModellForListGroupActions($user) {
		$groupService = $this->getHtgroupService ();
		$groups = $groupService->getGroupsByUser ( $user );
		
		return new ViewModel ( array (
				'groups' => $groups,
				'username' => $user 
		) );
	}

	public function addAction() {
		$username = $post = $this->getRequest ()->getPost ( 'username', '' );
		$groupname = $post = $this->getRequest ()->getPost ( 'groupname', '' );
		
		$messages = array ();
		
		if (empty ( $username ) || empty ( $groupname )) {
			// Loading View without sending Post
			$messages [] = "Fehler: Das Formular enthält keine Daten!";
		} else {
			// Loading View with sending Post-Data
			$uValid = $this->isUsernameValid ( $username );
			if (true !== $uValid) {
				$messages [] = $uValid;
			}
			
			$gValid = $this->isGroupnameValid ( $groupname );
			if (true !== $gValid) {
				$messages [] = $gValid;
			}
			
			if (true === $uValid && true === $gValid) {
				$groupS = $this->getHtgroupService ();
				
				$groupS->addUserToGroup ( $username, $groupname );
				$messages [] = "Die Gruppe '{$groupname}' wurde dem Benutzer '{$username}' hinzugefügt.";
			}
		}
		
		$model = $this->getViewModellForListGroupActions ( $username );
		$model->setTemplate ( 'htgroup-manager/group/list-groups' );
		
		$model->setVariable ( 'messages', $messages );
		
		return $model;
	}

	public function removeGroupAction() {
		$username = $this->params ( 'user', null );
		$groupname = $this->params ( 'groupname', null );
		
		$this->getHtgroupService ()->deleteUserFromGroup ( $username, $groupname );
		
		$model = $this->getViewModellForListGroupActions ( $username );
		$model->setTemplate ( 'htgroup-manager/group/list-groups' );
		
		$messages [] = "Dem Benutzer '{$username}' wurde die Gruppengehörigkeit zu '{$groupname}' entzogen.";
		$model->setVariable ( 'messages', $messages );
		
		return $model;
	}

	public function removeUserAction() {
	}
	
	// List all Users of a group
	public function listAction() {
		$groupname = $this->params ( 'groupname' );
		
		$users = $this->getHtgroupService ()->getUsersByGroup ( $groupname );
		
		return new ViewModel ( array (
				'groupname' => $groupname,
				'users' => $users,
				'inputFieldUsername' => '' 
		) );
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
	 * @return false | \HtgroupManager\Service\HtgroupService
	 */
	private function getHtgroupService() {
		if ($this->htgroupService === null) {
			$sl = $this->getServiceLocator ();
			$this->htgroupService = $sl->get ( 'HtgroupManager\Service\HtgroupService' );
		}
		
		return $this->htgroupService;
	}

}
