<?php

namespace App\Security;

use App\Entity\TreeNode;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Ldap;


class ActiveDirectoryService
{
    private $connection_string;
    private $bind_dn;
    private $domain_fqdn;
    private $bind_user;
    private $bind_password;

    public function __construct(string $connection_string, string $bind_dn, string $domain_fqdn, string $bind_user, string $bind_password)
    {
        $this->connection_string = $connection_string;
        $this->bind_dn = $bind_dn;
        $this->domain_fqdn = $domain_fqdn;
        $this->bind_user = $bind_user;
        $this->bind_password = $bind_password;
    }

    /**
     * @return string
     */
    public function getConnectionString(): string
    {
        return $this->connection_string;
    }

    /**
     * @return string
     */
    public function getBindDn(): string
    {
        return $this->bind_dn;
    }

    /**
     * @return string
     */
    public function getBindUser(): string
    {
        return $this->bind_user;
    }

    /**
     * @return string
     */
    public function getDomainFqdn(): string
    {
        return $this->domain_fqdn;
    }

    /**
     * @return string
     */
    public function getBindPassword(): string
    {
        return $this->bind_password;
    }

    public function bind(){
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $this->getConnectionString()]);
        try{
            if($this->getBindUser() === null){
                $ldap->bind();
            } else {
                $ldap->bind($this->getBindUser(), base64_decode($this->getBindPassword()));
            }
            return $ldap;
        } catch (ConnectionException $e) {
            throw new ConnectionException($e->getMessage());
        }
    }
}