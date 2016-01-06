<?php

/**
 * @author Steven B�hner
 * @license MIT
 * Parts of code are from: http://www.kavoir.com/2012/04/php-class-for-handling-htpasswd-and-htgroup-member-login-user-management.html
 * 
 */
namespace HtgroupManager\Service;

class HtgroupService {
	private $filename;
	
	// Caching of htpasswd-file
	private $groupCache = null;
	
	// Static Variables
	static $REGULAR_USER_PASSWORD = '~^([^:]+):(.+)$~im';

	public function __construct($htgroup_filename) {
		$this->filename = $htgroup_filename;
		$this->createFileIfNotExistant ();
	}

	private function createFileIfNotExistant() {
		if (true === file_exists ( $this->filename )) {
		} else {
			touch ( $this->filename );
		}
	}

	private function readGroups() {
		$groups = array ();
		
		$groups_str = file ( $this->filename, FILE_IGNORE_NEW_LINES );
		foreach ( $groups_str as $group_str ) {
			if (! empty ( $group_str )) {
				$group_str_array = explode ( ': ', $group_str );
				if (count ( $group_str_array ) == 2) {
					$users_array = explode ( ' ', $group_str_array [1] );
					$groups [$group_str_array [0]] = $users_array;
				}
			}
		}
		
		return $groups;
	}

	private function writeGroups($groups = array()) {
		$str = '';
		
		foreach ( $groups as $group => $users ) {
			$users_str = join ( ' ', $users );
			$str .= "{$group}: {$users_str}\n";
		}
		
		file_put_contents ( $this->filename, $str );
		$this->groupCache = $groups;
	}

	public function getGroups() {
		if (null === $this->groupCache) {
			$this->groupCache = $this->readGroups ();
		}
		
		return $this->groupCache;
	}

	/**
	 *
	 * @param string $groupname        	
	 * @return array:
	 */
	public function getUsersByGroup($groupname) {
		$groups = $this->getGroups ();
		
		if (isset ( $group [$groupname] ) && is_array ( $group [$groupname] )) {
			return $group [$groupname];
		}
		
		return array ();
	}

	public function addUserToGroup($username = '', $group = '') {
		if (! empty ( $username ) && ! empty ( $group )) {
			$all = $this->getGroups ();
			
			if (isset ( $all [$group] )) {
				if (! in_array ( $username, $all [$group] )) {
					$all [$group] [] = $username;
				}
			} else {
				$all [$group] [] = $username;
			}
			
			$this->writeGroups ( $all );
		} else {
			return false;
		}
	}

	public function deleteUserFromGroup($username = '', $groupname = '') {
		$allGroups = $this->getGroups ();
		
		if (array_key_exists ( $groupname, $allGroups )) {
			$user_index = array_search ( $username, $allGroups [$groupname] );
			
			if ($user_index !== false) {
				unset ( $allGroups [$groupname] [$user_index] );
				if (count ( $allGroups [$groupname] ) == 0) {
					unset ( $allGroups [$groupname] );
				}
				
				$this->writeGroups ( $allGroups );
			}
		}
	}

	public function getGroupsByUser($username = '') {
		$allGroups = $this->getGroups ();
		
		$userGroups = array ();
		
		foreach ( $allGroups as $groupName => $users ) {
			if (in_array ( $username, $users )) {
				$userGroups [] = $groupName;
			}
		}
		
		return $userGroups;
	}

}

?>