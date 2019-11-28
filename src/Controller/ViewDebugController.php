<?php

namespace App\Controller;

use App\Helpers\SIARPSController;
use App\Security\PermissionService;

class ViewDebugController extends SIARPSController {

    public function index($page = "main") {
        return $this->render('view_debug/' . $page . ".html.twig");
    }

    public function test() {
        return $this->render('core/document_base.html.twig',[
            'page'=>[
                'unit'=>'cm',
                'orientation'=>'landscape',
                'margin'=>[
                    'top'=>'4.5',
                    'header'=>'4.5',
                    'left'=>'1.8',
                    'right'=>'1.8',
                    'bottom'=>'1.8',
                    'footer'=>'1.8',
                ]
            ]
        ]);
        
    }

}
