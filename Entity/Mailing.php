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
     * @param string $title
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
     * @param text $body
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
     * @param datetime $createdAt
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
     * @param datetime $updatedAt
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    public function setSenderEmail($value)
    {
        $this->senderEmail = $value;
    }

    public function getSenderName()
    {
        return $this->senderName;
    }

    public function setSenderName($value)
    {
        $this->senderName = $value;
    }

    public function getSendDate()
    {
        return (null != $this->sendDate) ? $this->sendDate : new \DateTime("+5 minute");
    }

    public function setSendDate($value)
    {
        $this->sendDate = $value;
    }

    public function setTotalCount($value)
    {
        $this->totalCount = $value;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    public function setErrorsCount($value)
    {
        $this->errorsCount = $value;
    }

    public function getErrorsCount()
    {
        return $this->errorsCount;
    }

    public function setSentCount($value)
    {
        $this->sentCount = $value;
    }

    public function getSentCount()
    {
        return $this->sentCount;
    }

    public function isDelayedMailing()
    {
        return (bool)$this->delayedMailing;
    }

    public function setDelayedMailing($value)
    {
        $this->delayedMailing = $value;
    }
    
    public function __toString()
    {
        return $this->getTitle();
    }
}
