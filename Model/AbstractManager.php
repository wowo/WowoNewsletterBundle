<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

abstract class AbstractManager
{
  protected $em;
  protected $repository;
  protected $class;
  protected $request;

  public function __construct(EntityManager $em, Container $container, $class)
  {
    $this->em = $em;
    $this->repository = $em->getRepository($class);

    $metadata = $em->getClassMetadata($class);
    $this->class = $metadata->name;

    $this->request = $container->get("request");
  }
}
