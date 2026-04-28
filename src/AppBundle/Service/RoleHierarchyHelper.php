<?php
/**
 * Created by PhpStorm.
 * User: kristof
 * Date: 28/04/2016
 * Time: 11:40
 */

namespace AppBundle\Service;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;

class RoleHierarchyHelper extends RoleHierarchy
{
    private $em;

    /**
     * @param array $hierarchy
     */
    public function __construct(array $hierarchy, EntityManager $em)
    {
        $this->em = $em;
        parent::__construct($this->buildRolesTree());
    }

    /**
     * Here we build an array with roles. It looks like a two-levelled tree - just
     * like original Symfony roles are stored in security.yml
     * @return array
     */
    private function buildRolesTree()
    {
        $hierarchy = array();
        $roles = $this->em->createQuery('select r, p from AppBundle:Role r JOIN r.children p')->execute();
        /** @var $role Role */
        foreach ($roles as $role) {
            if (count($role->getChildren()) > 0)
            {
                $roleChildren = array();

                foreach ($role->getChildren() as $child)
                {
                    /* @var $child Role */
                    $roleChildren[] = $child->getRole();
                }

                $hierarchy[$role->getRole()] = $roleChildren;
            }
        }
        return $hierarchy;
    }
}