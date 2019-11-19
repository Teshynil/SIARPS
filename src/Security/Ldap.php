<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Security;

use App\Entity\User;
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
    private $LDAP;

    const USER = 0x01;
    const ADMIN = 0x02;

    private static $adapterMap = [
        'ext_ldap' => 'Symfony\Component\Ldap\Adapter\ExtLdap\Adapter',
    ];

    public function __construct(Adapter $adapter, Array $ldapSettings) {
        $this->adapter = $adapter;
        $this->LDAP = new ArrayObject($ldapSettings);
        $this->LDAP->setFlags(ArrayObject::ARRAY_AS_PROPS);
        $this->LDAP->SIARPS_MAIN_OU = ($ldapSettings["SIARPS_MAIN_OU"] == null ? $this->LDAP->BASE_DN : $ldapSettings["SIARPS_MAIN_OU"]);
    }

    public function getLdapParams() {
        return $this->LDAP;
    }

    public function checkConnection() {
        try {
            $op = fsockopen($this->LDAP->LDAP_HOST, 389, $errno, $errstr, 2);
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
            $dn = $this->LDAP->READ_ONLY_USER;
            $password = $this->LDAP->READ_ONLY_USER_PASSWORD;
        }
        $this->adapter->getConnection()->bind($dn, $password);
    }

    public function bindUser($username, $password) {
        $this->checkConnection();
        $this->adapter->getConnection()->bind($username, $password);
    }

    public function findAllUsers() {
        $query = "("
                . "memberOf=" . $this->LDAP->SIARPS_LDAP_USER_GROUP_DN
                . ")";
        return $this->query($this->LDAP->SIARPS_MAIN_OU, $query)->execute();
    }

    public function findAllAdmins() {
        $query = "("
                . "memberOf=" . $this->LDAP->SIARPS_LDAP_ADMIN_GROUP_DN
                . ")";
        return $this->query($this->LDAP->SIARPS_MAIN_OU, $query)->execute();
    }

    public function findUserQuery($username) {
        $query_string = "(&"
                . "(|"
                . "(memberOf=" . $this->LDAP->SIARPS_LDAP_USER_GROUP_DN . ")"
                . "(memberOf=" . $this->LDAP->SIARPS_LDAP_ADMIN_GROUP_DN . ")"
                . ")"
                . "(" . $this->LDAP->SIARPS_LOGIN_ATTRIBUTE . "={username})"
                . ")";
        $username = $this->escape($username, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{username}', $username, $query_string);
        return $this->query($this->LDAP->SIARPS_MAIN_OU, $query)->execute();
    }

    public function findGroupOwner($gdn) {
        $query = "(memberOf=" . $this->LDAP->SIARPS_LDAP_GROUP_OWNER_DN . ")";
        return $this->query($gdn, $query)->execute();
    }

    public function findOU($dn) {
        $query_string = "(&(objectClass=organizationalUnit)(objectClass=top)(distinguishedName={ou}))";
        $dn = $this->escape($dn, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{ou}', $dn, $query_string);
        return $this->query($this->LDAP->BASE_DN, $query)->execute();
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
