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
      $mailingManager = $this->get("wowo_newsletter.mailing_manager");
      if ('POST' == $this->get('request')->getMethod()) {
        $mailing  = $mailingManager->createMailingFromRequest();
        $contactIds = $contactManager->findContactIdForMailingFromRequest();
        $this->get("wowo_newsletter.newsletter_manager")->putMailingInPreparationQueue($mailing->getId(), $contactIds);
      }
      return array("contacts" => $contactManager->findContactToChooseForMailing());
    }
}
