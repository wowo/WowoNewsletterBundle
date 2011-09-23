<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class MailingManager extends AbstractManager implements MailingManagerInterface
{
  public function createMailingFromRequest()
  {
    $mailing = new Mailing();
    $mailing->setTitle($this->request->get("title"));
    $mailing->setBody($this->request->get("body"));

    $this->em->persist($mailing);
    $this->em->flush();
    return $mailing;
  }
}
