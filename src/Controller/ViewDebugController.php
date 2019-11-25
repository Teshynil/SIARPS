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
                'orientation'=>'landscape',
                'margin'=>[
                    'top'=>'4.5cm',
                    'header'=>'4.5cm',
                    'left'=>'1.8cm',
                    'right'=>'1.8cm',
                    'bottom'=>'1.8cm',
                    'footer'=>'1.8cm',
                ]
            ]
        ]);
        
    }

}
