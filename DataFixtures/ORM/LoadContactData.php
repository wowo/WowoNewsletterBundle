<?php

namespace Acme\HelloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Wowo\Bundle\NewsletterBundle\Entity\Contact;

class LoadContactData implements FixtureInterface
{
  public function load($manager)
  {
    $names = array("John", "Thomas", "Jack");
    $surnames = array(
      "Li",
      "Smith",
      "Lam",
      "Martin",
      "Brown",
      "Roy",
      "Tremblay",
      "Lee",
      "Gagnon",
      "Wilson",
    );
    for($i = 0; $i < 10; $i++) {
      shuffle($surnames);
      $contact = new Contact();
      $contact->setName($names[array_rand($names)]);
      $contact->setSurname(array_pop($surnames));
      $contact->setEmail(strtolower($contact->getName() . "." . $contact->getSurname() . "@example.com"));

      $manager->persist($contact);
      $manager->flush();
    }
  }
}
