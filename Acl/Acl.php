<?php
namespace PQstudio\RestUtilityBundle\Acl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


/**
 * @DI\Service("pq.acl")
 */
class Acl
{
    protected $acl;

    protected $security;

    /**
     * @DI\InjectParams({
     *     "acl" = @DI\Inject("security.acl.provider"),
     *     "security" = @DI\Inject("security.context")
     * })
     */
    public function __construct($acl, $security)
    {
        $this->acl = $acl;
        $this->security = $security;
    }

    public function addAclForCurrentUser($domainObject, $flags)
    {
        if($domainObject === null) {
            return false;
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($domainObject);
        $acl = $this->acl->createAcl($objectIdentity);

        $user = $this->security->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, $flags);
        $this->acl->updateAcl($acl);

        return $this;
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
