<?php
namespace Nameco\UserBundle\Services;

class MenuItemService
{
	private $params;
	
	public function __construct($params)
	{
//		var_dump($params);die;
		$this->params = $params;
	}
	
	public function getItems()
	{
		return $this->params;
	}
}
