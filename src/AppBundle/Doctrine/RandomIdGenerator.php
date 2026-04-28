<?php 
namespace AppBundle\Doctrine;
use Doctrine\ORM\Id\AbstractIdGenerator;

class RandomIdGenerator extends AbstractIdGenerator
{
    public function generate(\Doctrine\ORM\EntityManager $em, $entity)
    {
        $entity_name = $em->getClassMetadata(get_class($entity))->getName();

        while (true)
        {
            $id = mt_rand(10000000, 99999999);
            $item = $em->find($entity_name, $id);

            if (!$item)
            {
                return $id;
            }
        }

        throw new \Exception('RandomIdGenerator worked hard, but could not generate unique ID :(');
    }
}