<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Security;

use App\Entity\Setting;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Exception\DriverNotFoundException;
use Symfony\Component\Ldap\LdapInterface;

final class LdapConnectionTimeout extends \Exception {

}

final class Ldap implements LdapInterface {

    private $adapter;
    public $LDAP_HOST;
    public $PORT;
    public $ENCRYPTION;
    public $BASE_DN;
    public $READ_USER;
    public $READ_USER_PASSWORD;
    public $USER_GROUP;
    public $ADMIN_GROUP;
    public $OWNER_GROUP;
    public $GROUP_PREFIX;
    public $LOGIN_ATTR;
    public $FIRSTNAME_ATTR;
    public $LASTNAME_ATTR;
    public $EMAIL_ATTR;
    

    const USER = 0x01;
    const ADMIN = 0x02;

    private static $adapterMap = [
        'ext_ldap' => 'Symfony\Component\Ldap\Adapter\ExtLdap\Adapter',
    ];

    public function __construct(EntityManagerInterface $em) {
        $settings=$em->getRepository(Setting::class);
        $this->HOST=$settings->getValue("ldapHost");
        $this->PORT=$settings->getValue("ldapPort");
        $this->ENCRYPTION=$settings->getValue("ldapEncryption");
        $this->BASE_DN=$settings->getValue("ldapBaseDN");
        $this->READ_USER=$settings->getValue("ldapReadUser");
        $this->READ_USER_PASSWORD=$settings->getValue("ldapReadUserPassword");
        $this->USER_GROUP=$settings->getValue("ldapUserGroupDN");
        $this->ADMIN_GROUP=$settings->getValue("ldapAdminGroupDN");
        $this->OWNER_GROUP=$settings->getValue("ldapOwnerGroupDN");
        $this->GROUP_PREFIX=$settings->getValue("ldapGroupPrefix");
        $this->LOGIN_ATTR=$settings->getValue("ldapLoginAttr");
        $this->FIRSTNAME_ATTR=$settings->getValue("ldapFirstNameAttr");
        $this->LASTNAME_ATTR=$settings->getValue("ldapLastNameAttr");
        $this->EMAIL_ATTR=$settings->getValue("ldapEmailAttr");
        
        $this->adapter = new Adapter(['host'=>$this->HOST,'port'=>$this->PORT,'encryption'=>$this->ENCRYPTION]);
    }   

    public function checkConnection() {
        try {
            $op = fsockopen($this->HOST, 389, $errno, $errstr, 2);
            if (!$op)
                throw new LdapConnectionTimeout();
            else {
                fclose($op); //explicitly close open socket connection
                return; //DC is up & running, we can safely connect with ldap_connect
            }
        } catch (ErrorException $ex) {
            throw new LdapConnectionTimeout();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bind($dn = null, $password = null) {
        $this->checkConnection();
        if ($dn == $password && $dn == null) {
            $dn = $this->READ_USER;
            $password = $this->READ_USER_PASSWORD;
        }
        $this->adapter->getConnection()->bind($dn, $password);
    }

    public function bindUser($username, $password) {
        $this->checkConnection();
        $this->adapter->getConnection()->bind($username, $password);
    }

    public function findAllUsers() {
        $query = "("
                . "memberOf=" . $this->USER_GROUP
                . ")";
        return $this->query($this->BASE_DN, $query)->execute();
    }

    public function findAllAdmins() {
        $query = "("
                . "memberOf=" . $this->ADMIN_GROUP
                . ")";
        return $this->query($this->BASE_DN, $query)->execute();
    }

    public function findUserQuery($username) {
        $query_string = "(&"
                . "(|"
                . "(memberOf=" . $this->USER_GROUP . ")"
                . "(memberOf=" . $this->ADMIN_GROUP . ")"
                . ")"
                . "(" . $this->LOGIN_ATTR . "={username})"
                . ")";
        $username = $this->escape($username, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{username}', $username, $query_string);
        return $this->query($this->BASE_DN, $query)->execute();
    }

    public function findGroupOwner($gdn) {
        $query = "(memberOf=" . $this->OWNER_GROUP . ")";
        return $this->query($gdn, $query)->execute();
    }

    public function findOU($dn) {
        $query_string = "(&(objectClass=organizationalUnit)(objectClass=top)(distinguishedName={ou}))";
        $dn = $this->escape($dn, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{ou}', $dn, $query_string);
        return $this->query($this->BASE_DN, $query)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function query($dn, $query, array $options = []) {
        $this->checkConnection();
        return $this->adapter->createQuery($dn, $query, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryManager() {
        return $this->adapter->getEntryManager();
    }

    /**
     * {@inheritdoc}
     */
    public function escape($subject, $ignore = '', $flags = 0) {
        return $this->adapter->escape($subject, $ignore, $flags);
    }

    /**
     * Creates a new Ldap instance.
     *
     * @param string $adapter The adapter name
     * @param array  $config  The adapter's configuration
     *
     * @return static
     */
    public static function create($adapter, array $config = []): self {
        if (!isset(self::$adapterMap[$adapter])) {
            throw new DriverNotFoundException(sprintf(
                            'Adapter "%s" not found. You should use one of: %s',
                            $adapter,
                            implode(', ', self::$adapterMap)
            ));
        }

        $class = self::$adapterMap[$adapter];

        return new self(new $class($config));
    }

}
