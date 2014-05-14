<?php
namespace PQstudio\RestUtilityBundle\Acl;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


class Acl
{
    protected $acl;

    public function __construct($acl)
    {
        $this->acl = $acl;
    }

    public function addAclForUser($domainObject, $user, $flags)
    {
        if($domainObject === null) {
            return false;
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($domainObject);
        $acl = $this->acl->createAcl($objectIdentity);

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, $flags);
        $this->acl->updateAcl($acl);

        return $this;
    }
}
