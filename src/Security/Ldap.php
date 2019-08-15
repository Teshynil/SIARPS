<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Security;

use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Exception\DriverNotFoundException;
use Symfony\Component\Ldap\LdapInterface;

final class Ldap implements LdapInterface {

    private $adapter;
    private $READ_ONLY_USER;
    private $READ_ONLY_USER_PASSWORD;
    private $SIARPS_LDAP_BASE_GROUP_DN;
    private $SIARPS_LDAP_GROUP_PREFIX;
    private $SIARPS_LDAP_GROUP_OWNER_DN;
    private $BASE_DN;
    private $SIARPS_MAIN_OU;
    private $SIARPS_LOGIN_ATTRIBUTE;
    private $SIARPS_FIRST_NAME_ATTRIBUTE;
    private $SIARPS_LAST_NAME_ATTRIBUTE;
    private static $adapterMap = [
        'ext_ldap' => 'Symfony\Component\Ldap\Adapter\ExtLdap\Adapter',
    ];

    public function __construct(Adapter $adapter, Array $ldapSettings) {
        $this->adapter = $adapter;
        $this->READ_ONLY_USER = $ldapSettings["READ_ONLY_USER"];
        $this->READ_ONLY_USER_PASSWORD = $ldapSettings["READ_ONLY_USER_PASSWORD"];
        $this->SIARPS_LDAP_BASE_GROUP_DN = $ldapSettings["SIARPS_LDAP_BASE_GROUP_DN"];
        $this->SIARPS_LDAP_GROUP_PREFIX = $ldapSettings["SIARPS_LDAP_GROUP_PREFIX"];
        $this->SIARPS_LDAP_GROUP_OWNER_DN = $ldapSettings["SIARPS_LDAP_GROUP_OWNER_DN"];
        $this->BASE_DN = $ldapSettings["BASE_DN"];
        $this->SIARPS_MAIN_OU = $ldapSettings["SIARPS_MAIN_OU"];
        $this->SIARPS_LOGIN_ATTRIBUTE = $ldapSettings["SIARPS_LOGIN_ATTRIBUTE"];
        $this->SIARPS_FIRST_NAME_ATTRIBUTE = $ldapSettings["SIARPS_FIRST_NAME_ATTRIBUTE"];
        $this->SIARPS_LAST_NAME_ATTRIBUTE = $ldapSettings["SIARPS_LAST_NAME_ATTRIBUTE"];
    }

    /**
     * {@inheritdoc}
     */
    public function bind($dn = null, $password = null) {
        if ($dn == $password && $dn == null) {
            $dn = $this->READ_ONLY_USER;
            $password = $this->READ_ONLY_USER_PASSWORD;
        }
        $this->adapter->getConnection()->bind($dn, $password);
    }

    public function bindUser($username, $password) {
        $this->adapter->getConnection()->bind($username, $password);
    }

    public function findUserQuery($username) {
        $query_string = "(&(memberOf=$this->SIARPS_LDAP_BASE_GROUP_DN,$this->BASE_DN)($this->SIARPS_LOGIN_ATTRIBUTE={username}))";
        $username = $this->escape($username, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{username}', $username, $query_string);
        return $this->query($this->BASE_DN, $query);
    }

    public function findOU($dn) {
        $query_string = "(&(objectClass=organizationalUnit)(objectClass=top)(distinguishedName={ou}))";
        $dn = $this->escape($dn, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{ou}', $dn, $query_string);
        return $this->query($this->BASE_DN, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function query($dn, $query, array $options = []) {
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