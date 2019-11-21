<?php

namespace App\Controller;

use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends SIARPSController {

    public function index(Request $request) {
        return $this->render('dashboard.html.twig');
    }

}
