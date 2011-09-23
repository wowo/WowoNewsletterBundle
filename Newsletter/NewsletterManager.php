<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;

class NewsletterManager implements NewsletterManagerInterface
{
  protected $class;
  protected $pheanstalk;
  protected $preparationTube = "newsletter_preparation_tube";
  protected $sendingTube     = "newsletter_sending_tube";

  public function __construct(EntityManager $em, $class)
  {
    $metadata = $em->getClassMetadata($class);
    $this->class = $metadata->name;

    $this->pheanstalk = new \Pheanstalk('127.0.0.1:11300');
  }

  public function putMailingInPreparationQueue($mailingId, array $contactIds)
  {
    foreach($contactIds as $contactId) {
      $job = new \StdClass();
      $job->contactId = $contactId;
      $job->mailingId = $mailingId;
      $job->contactClass = $this->class;
      $this->pheanstalk->useTube($this->preparationTube)->put(json_encode($job));
    }
  }
}
