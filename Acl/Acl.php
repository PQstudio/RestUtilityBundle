<?php
namespace PQstudio\RestUtilityBundle\Acl;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\Entry;

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

        //$objectIdentity = ObjectIdentity::fromDomainObject($domainObject);
        //$acl = $this->acl->createAcl($objectIdentity);
        $acl = $this->getAcl($domainObject);

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, $flags);
        $this->acl->updateAcl($acl);

        return $this;
    }

    public function revokeAclForUser($entity, $user, $mask = MaskBuilder::MASK_OWNER)
    {
		$acl = $this->getAcl($entity);
		$aces = $acl->getObjectAces();

		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		foreach($aces as $i => $ace) {
			if($securityIdentity->equals($ace->getSecurityIdentity())) {
				$this->revokeMask($i, $acl, $ace, $mask);
			}
		}

		$this->acl->updateAcl($acl);

		return $this;
	}

    protected function getAcl($entity)
    {
		// creating the ACL
		$aclProvider = $this->acl;
		$objectIdentity = ObjectIdentity::fromDomainObject($entity);
		try {
			$acl = $aclProvider->createAcl($objectIdentity);
		}catch(\Exception $e) {
			$acl = $aclProvider->findAcl($objectIdentity);
		}

		return $acl;
	}

    protected function revokeMask($index, \Symfony\Component\Security\Acl\Domain\Acl $acl, Entry $ace, $mask) {
		$acl->updateObjectAce($index, $ace->getMask() & ~$mask);

		return $this;
	}
}
