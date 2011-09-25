<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;

class NewsletterManager implements NewsletterManagerInterface
{
    protected $class;
    protected $pheanstalk;
    protected $preparationTube;
    protected $sendingTube;

    public function __construct(EntityManager $em,$pheanstalk, $class, $preparationTube)
    {
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;

        $this->pheanstalk = $pheanstalk;
        $this->preparationTube = $preparationTube;
    }

    public function putMailingInPreparationQueue($mailingId, array $contactIds)
    {
        if (null == $this->preparationTube) {
            throw new \InvalidArgumentException("Preparation tube unkonwn!");
        }
        if (count($contactIds) == 0) {
            throw new \InvalidArgumentException('No contacs selected, it need to be at least one contact to send mailing');
        }
        foreach($contactIds as $contactId) {
              $job = new \StdClass();
              $job->contactId = $contactId;
              $job->mailingId = $mailingId;
              $job->contactClass = $this->class;
              $this->pheanstalk->useTube($this->preparationTube)->put(json_encode($job));
        }
    }
}
