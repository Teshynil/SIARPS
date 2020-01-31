<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;

use App\Security\PermissionService;
use LogicException;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



abstract class SIARPSController extends AbstractController{
    
    
    protected function getPermissionService(): PermissionService{
        if (!$this->container->has('security.permission')) {
            throw new LogicException('Unknown Exception on PermissionService');
        }

        return $this->container->get('security.permission');
    }
    
    public static function getSubscribedServices(){
        return array_merge(parent::getSubscribedServices(),
                [
                    'security.permission'=>'?'.PermissionService::class,
                    FileSaver::class=>'?'.FileSaver::class
                ]);
    }
}
