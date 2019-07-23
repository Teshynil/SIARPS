<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Ldap\LdapInterface;

class DashboardController extends AbstractController {

    public function index(SessionInterface $session, LdapInterface $ldap) {
        $username = $ldap->escape('pedro.diaz', '', $ldap::ESCAPE_DN);
        $query_string = '(&(memberOf=cn=SIARPS,cn=Groups,dc=testing-co,dc=local)(cn={username}))';
        $query = str_replace('{username}', $username, $query_string);
        $ldap->bind('CN=siarps.auth,CN=Users,DC=testing-co,DC=local', '123456789');
        $result = $ldap->query('DC=testing-co,DC=local', $query)->execute();
        var_dump($result[0]);
        return false;
        $session->set("notifications", [
            ["text" => "hola mundo",
                "icon" => "notebook",
                "color" => "success",
                "path" => "notifications",
                "parameters" => ["hola" => "dwa"]
            ]
        ]);
        return $this->render('dashboard.html.twig');
    }

}
