<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

/**
 * Message Spooler Interface 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
interface SpoolerInterface
{
    /**
     * Processes the spooler (gets one message and does proper job with it)
     * 
     * @access public
     * @return void
     */
    public function process();
    /**
     * Clears all spooler queues
     * 
     * @access public
     * @return void
     */
    public function clear();
    /**
     * Spool one mailing with one contact id
     * 
     * @param Mailing $mailing 
     * @param mixed $contactId 
     * @access public
     * @return void
     */
    public function spool(Mailing $mailing, $contactId, $contactClass);
    /**
     * Spools array of Contacts ids
     * 
     * @param Mailing $mailing 
     * @param array $contactIds 
     * @access public
     * @return void
     */
    public function spoolManyContacts(Mailing $mailing, array $contactIds, $contactClass);
}
