<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;

class ContactManager extends AbstractManager implements ContactManagerInterface
{
  /**
   * findContactToChooseForMailing 
   * 
   * @access public
   * @return void
   */
  public function findContactToChooseForMailing()
  {
    return $this->em->getRepository($this->class)->findAll();
  }

  public function findContactForMailingFromRequest()
  {
  }
}
