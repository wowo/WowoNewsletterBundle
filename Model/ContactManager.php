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
        $contacts = $this->em->getRepository($this->class)->findAll();
        $result   = array();
        foreach ($contacts as $contact) {
            $result[$contact->getId()] = (string)$contact;
        }
        return $result;
    }

    /**
    * findContactForMailingFromRequest 
    * 
    * @access public
    * @return array
    */
    public function findChoosenContactIdForMailing($form)
    {
        $data = $form->getData();
        return $data['contacts'];
    }
}
