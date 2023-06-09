<?php
namespace App\Security;

use MongoDB\Driver\Exception\ConnectionException;
use Symfony\Component\Ldap\Ldap;

class ActiveDirectoryUserService extends ActiveDirectoryService
{

    public function queryUserByAccountName(string $sAMAccountName)
    {
        $ldap = $this->bind();
        $query = $ldap->query($this->getBindDn(),sprintf('(&(objectClass=user)(sAMAccountName=%s))',$sAMAccountName));
        return $query->execute()->toArray();
    }

    public function userCheckPassword(string $username, string $password){
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $this->getConnectionString()]);
        try{
            $ldap->bind($username, $password);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}