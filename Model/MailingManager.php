<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class MailingManager extends AbstractManager implements MailingManagerInterface
{
    public function createMailingBasedOnForm($form, $contactCount)
    {
        $mailing = $form->getData()->mailing;
        $mailing->setTotalCount($contactCount);
        $mailing->setSentCount(0);
        $mailing->setErrorsCount(0);
        $this->em->persist($mailing);
        $this->em->flush();
        return $mailing;
    }
}
