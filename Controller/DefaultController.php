<?php

namespace Wowo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/mailing")
     * @Template()
     */
    public function createMailingAction()
    {
      $contactManager = $this->get("wowo_newsletter.contact_manager");
      var_dump($this->getRequest()->request->get("contact"));
      return array("contacts" => $contactManager->findContactToChooseForMailing());
    }
}
