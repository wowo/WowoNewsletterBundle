<?php

namespace Wowo\NewsletterBundle\Newsletter\Model;

interface ContactManagerInterface
{
    /**
     * Finds all contacts that can be choosen as a recipient of mailing (for example they confirmed will to recieve mailing)
     */
    public function findContactToChooseForMailing();

    /**
     * Finds contacts that user has choosen for recieve the mailing
     */
    public function findChoosenContactIdForMailing($source);

    /**
     * Finds contact of given class
     */
    public function findContact($id, $class);
}
