<?php
namespace Nameco\UserBundle\Twig\Extension;

use Nameco\UserBundle\Services\MenuManagerService;

class MenuManagerExtension extends \Twig_Extension
{
    protected $service;

    public function __construct(MenuManagerService $service) {
        $this->service = $service;
    }

    public function getGlobals() {
        return array(
            'menumanager' => $this->service,
        );
    }

    public function getName()
    {
        return 'MenuManagerExtension';
    }
}
