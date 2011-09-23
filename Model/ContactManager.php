<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;

class ContactManager extends AbstractManager implements ContactManagerInterface
{
  /**
   * findContactToChooseForMailing 
   * 
   * @access public
   * @return Contact[]
   */
  public function findContactToChooseForMailing()
  {
    return $this->em->getRepository($this->class)->findAll();
  }

  /**
   * findContactForMailingFromRequest 
   * 
   * @access public
   * @return array
   */
  public function findContactIdForMailingFromRequest()
  {
    $ids = array_keys($this->request->request->get("contact"));
    //TODO check if ids are in contacts returned by findContactToChooseForMailing
    return $ids;
  }
}
