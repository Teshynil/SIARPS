<?php

namespace App\Controller;

use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends SIARPSController {

    public function index(Request $request) {
        $projects=$this->getDoctrine()->getManager()->getRepository(\App\Entity\Project::class)->getAvailableGrouped($this->getUser());
        $summary=$this->getDoctrine()->getManager()->getRepository(\App\Entity\Project::class)->getSummary($this->getUser());
        $groups=$this->getDoctrine()->getManager()->getRepository(\App\Entity\Group::class)->getAvailable($this->getUser());
        $perGroupSummary=[];
        foreach ($groups as $group) {
            $perGroupSummary[$group->getId()]=$this->getDoctrine()->getManager()->getRepository(\App\Entity\Project::class)->getSummary($this->getUser(),$group);
        }
        $data=[
            'summary'=>$summary,
            'groups'=>$groups,
            'projects'=>$projects,
            'perGroupSummary'=>$perGroupSummary,
        ];
        return $this->render('dashboard.html.twig',$data);
    }

}
