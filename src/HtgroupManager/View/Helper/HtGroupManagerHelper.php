<?php

namespace HtgroupManager\View\Helper;

use Zend\View\Helper\AbstractHelper;
use RoleInterfaces\Service\GroupManagementInterface;
use HtgroupManager\Service\HtgroupService;

class HtGroupManagerHelper extends AbstractHelper {
	/**
	 *
	 * @var HtgroupService
	 */
	private $groupManagerService;

	/**
	 *
	 * @param HtgroupService $htGroup        	
	 */
	public function __construct(HtgroupService $htGroup) {
		$this->groupManagerService = $htGroup;
	}

	public function __invoke() {
		return $this->groupManagerService;
	}
}
