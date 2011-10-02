<?php

namespace Wowo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wowo\Bundle\NewsletterBundle\Form\NewsletterType;
use Wowo\Bundle\NewsletterBundle\Model\Newsletter;

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
        $newsletter = $this->get('wowo_newsletter.empty_newsletter');
        $this->get('wowo_newsletter.newsletter_manager')->validateDependencies();
        $form = $this->createForm(new NewsletterType(), $newsletter,
            array('data' => array(
                'availableContacts' => $contactManager->findContactToChooseForMailing(),
                'mailing' => $newsletter->mailing,
            )));
        if ('POST' == $this->get('request')->getMethod()) {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $contactIds = $contactManager->findChoosenContactIdForMailing($form);
                $mailing    = $mailingManager->createMailingBasedOnForm($form, count((array)$contactIds));
                $this->get("wowo_newsletter.newsletter_manager")->putMailingInQueue($mailing, $contactIds);

                $this->get('session')->setFlash('notice',
                    sprintf('Mailing to %d recipients has been enqueued for sending', count($contactIds)));
                return $this->redirect($this->generateUrl('wowo_newsletter_default_createmailing'));
            }
        }
        return array(
            "form" => $form->createView(),
        );
    }
}
