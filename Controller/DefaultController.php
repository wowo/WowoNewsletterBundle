<?php

namespace Wowo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wowo\Bundle\NewsletterBundle\Form\MailingType;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class DefaultController extends Controller
{
    /**
     * @Route("/mailing")
     * @Template()
     */
    public function createMailingAction()
    {
        $form = $this->createForm(new MailingType(), new Mailing());
        $contactManager = $this->get("wowo_newsletter.contact_manager");
        $mailingManager = $this->get("wowo_newsletter.mailing_manager");
        if ('POST' == $this->get('request')->getMethod()) {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $contactIds = $contactManager->findContactIdForMailingFromRequest();
                $mailing    = $mailingManager->createMailingBasedOnForm($form, count((array)$contactIds));
                $this->get("wowo_newsletter.newsletter_manager")->putMailingInPreparationQueue($mailing->getId(), $contactIds);
            }
        }
        return array(
            "contacts" => $contactManager->findContactToChooseForMailing(),
            "form"     => $form->createView(),
        );
    }
}
