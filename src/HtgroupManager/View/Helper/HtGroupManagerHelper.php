<?php

namespace HtgroupManager\View\Helper;

use Zend\View\Helper\AbstractHelper;
use HtgroupManager\Service\GroupManageService;

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
	public function __construct(GroupManageService $htGroup) {
		$this->groupManagerService = $htGroup;
	}

	/**
	 * @return GroupManageService
	 */
	public function __invoke() {
		return $this->groupManagerService;
	}

}
