<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wowo\Bundle\NewsletterBundle\Model\ContactManagerInterface;

class Newsletter extends ContainerAware
{
    public $mailing;
    private $contacts;

    private $contactManager;

    public function __construct(ContactManagerInterface $contactManager)
    {
        $this->contactManager = $contactManager;
    }

    public function getContacts()
    {
        if (null == $this->contacts) {
            $this->contacts = $this->contactManager->findContactToChooseForMailing();
        }
        return $this->contacts;
    }

    public function setContacts($value)
    {
        $this->contacts = $value;
    }
}
