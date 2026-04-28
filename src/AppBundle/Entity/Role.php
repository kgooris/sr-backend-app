<?php


namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Role Entity
 *
 * @ORM\Entity
 * @ORM\Table( name="roles" )
 * @UniqueEntity("role")
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     */
    protected $role;
    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="role_permission",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *      )
     */
    protected $children;

    /**
     * Populate the role field
     *
     * @param string $role
     */
    public function __construct($role = "")
    {
        $this->role = $role;
    }
    /**
     * Return the role field.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
    /**
     * Return the string representation of the role entity.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->role;
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Modify the role field.
     *
     * @param string $role ROLE_FOO etc
     *
     * @return RoleInterface
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }


}