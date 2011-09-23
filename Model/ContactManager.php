<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;

class ContactManager implements ContactManagerInterface
{
  protected $em;
  protected $repository;
  protected $class;

  public function __construct(EntityManager $em, $class)
  {
    $this->em = $em;
    $this->repository = $em->getRepository($class);

    $metadata = $em->getClassMetadata($class);
    $this->class = $metadata->name;
  }

  public function findContactToChooseForMailing()
  {
    return $this->em->getRepository($this->class)->findAll();
  }
}
