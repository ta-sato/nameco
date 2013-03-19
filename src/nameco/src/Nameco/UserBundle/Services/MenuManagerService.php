<?php
namespace Nameco\UserBundle\Services;

class MenuManagerService
{
	private $items;
	
	public function __construct()
	{
		$this->items = array();
	}

	public function addItem(MenuItemService $itemservice)
	{
		foreach ($itemservice->getItems() as $items)
		{
			$this->items[] = $items;
		}
	}
	
	public function getItems()
	{
		return $this->items;
	}
}
