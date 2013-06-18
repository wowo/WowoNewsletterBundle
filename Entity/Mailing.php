<?php

namespace Wowo\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Wowo\NewsletterBundle\Entity\Mailing
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Wowo\NewsletterBundle\Entity\MailingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Mailing
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $title
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var text $body
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="body", type="text")
     */
    protected $body;

    /**
     * @var string $senderEmail
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(name="senderEmail", type="string", length=255)
     */
    protected $senderEmail;

    /**
     * @var string $senderName
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="senderName", type="string", length=255)
     */
    protected $senderName;

    /**
     * @var datetime $sendDate
     *
     * @ORM\Column(name="sendDate", type="datetime", nullable=true)
     */
    protected $sendDate;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var integer $totalCount
     *
     * @ORM\Column(name="totalCount", type="integer")
     */
    protected $totalCount;

    /**
     * @var integer $sentCount
     *
     * @ORM\Column(name="sentCount", type="integer")
     */
    protected $sentCount;

    /**
     * @var integer $errorsCount
     *
     * @ORM\Column(name="errorsCount", type="integer")
     */
    protected $errorsCount;

    /**
     * @var bool $delayedMailing
     *
     * @ORM\Column(name="delayedMailing", type="boolean")
     */
    protected $delayedMailing = false;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title title
     * 
     * @return null
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param text $body body
     * 
     * @return null
     * 
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist()
     * 
     * @return null
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * 
     * @return null
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * sender email getter
     * 
     * @return string senderEmail
     */ 
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }
    /**
     * sender email setter
     * 
     * @param string $value sender email
     * 
     * @return null
     */
    public function setSenderEmail($value)
    {
        $this->senderEmail = $value;
    }
    /**
     * sender name getter
     * 
     * @return string senderName
     */
    public function getSenderName()
    {
        return $this->senderName;
    }
    /**
     * sender email getter
     * 
     * @param string $value sender name
     * 
     * @return null
     */
    public function setSenderName($value)
    {
        $this->senderName = $value;
    }

    /**
     * send date getter
     * 
     * @return \DateTime send date
     */
    public function getSendDate()
    {
        return (null != $this->sendDate) ? $this->sendDate : new \DateTime("+5 minute");
    }
    /**
     * send date setter
     * 
     * @param \DateTime $value send date
     * 
     * @return null
     */
    public function setSendDate($value)
    {
        $this->sendDate = $value;
    }
    /**
     * total count setter
     * 
     * @param integer $value total count value
     * 
     * @return null
     */
    public function setTotalCount($value)
    {
        $this->totalCount = $value;
    }
    /**
     * total count setter
     * 
     * @return integer totalcount
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }
    /**
     * error count setter
     * 
     * @param integer $value errors count
     * 
     * @return null
     */
    public function setErrorsCount($value)
    {
        $this->errorsCount = $value;
    }
    /**
     * error count getter
     * 
     * @return integer $value errors count
     */
    public function getErrorsCount()
    {
        return $this->errorsCount;
    }
    /**
     * error count setter
     * 
     * @param integer $value errors count
     * 
     * @return null
     */
    public function setSentCount($value)
    {
        $this->sentCount = $value;
    }
    /**
     * error count getter
     * 
     * @return integer errors count
     * 
     */
    public function getSentCount()
    {
        return $this->sentCount;
    }
    /**
     * delayed mailing checker
     * 
     * @return boolean
     */
    public function isDelayedMailing()
    {
        return (bool) $this->delayedMailing;
    }
    /**
     * delayed mailing checker
     * 
     * @param boolean $value delayedmailing
     * 
     * @return null
     */
    public function setDelayedMailing($value)
    {
        $this->delayedMailing = $value;
    }
    /**
     * Stringifier
     * 
     * @return string title or uniqueid value just for sonata admin bundle integration
     */
    public function __toString()
    {
        return $this->getTitle()!=null?$this->getTitle():uniqid();
    }
}
