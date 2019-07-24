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

final class Ldap implements LdapInterface
{
    private $adapter;
    private $ro_dn;
    private $ro_password;
    private $siarps_ldap_group_dn;
    private $base_dn;
    
    private static $adapterMap = [
        'ext_ldap' => 'Symfony\Component\Ldap\Adapter\ExtLdap\Adapter',
    ];

    public function __construct(Adapter $adapter, $ro_dn,$ro_password,$siarps_ldap_group_dn,$base_dn)
    {
        $this->adapter = $adapter;
        $this->ro_dn=$ro_dn;
        $this->ro_password=$ro_password;
        $this->siarps_ldap_group_dn=$siarps_ldap_group_dn;
        $this->base_dn=$base_dn;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($dn = null, $password = null)
    {
        if($dn==$password&&$dn==null){
            $dn=$this->ro_dn;
            $password=$this->ro_password;
        }
        $this->adapter->getConnection()->bind($dn, $password);
    }
    
    public function bindUser($username, $password)
    {
        $username = $this->escape($username, '', LdapInterface::ESCAPE_DN);
        $dn="CN=$username,CN=Users,$this->base_dn";
        $this->adapter->getConnection()->bind($dn, $password);
    }

    public function findUserQuery($username)
    {
        $query_string = "(&(memberOf=$this->siarps_ldap_group_dn,$this->base_dn)(cn={username}))";
        $username = $this->escape($username, '', LdapInterface::ESCAPE_FILTER);
        $query = str_replace('{username}', $username, $query_string);
        return $this->query($this->base_dn,$query);
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function query($dn, $query, array $options = [])
    {
        return $this->adapter->createQuery($dn, $query, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryManager()
    {
        return $this->adapter->getEntryManager();
    }

    /**
     * {@inheritdoc}
     */
    public function escape($subject, $ignore = '', $flags = 0)
    {
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
    public static function create($adapter, array $config = []): self
    {
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
