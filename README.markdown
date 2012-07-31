# WowoNewsletterBundle

[![Build Status](https://secure.travis-ci.org/wowo/WowoNewsletterBundle.png)](https://secure.travis-ci.org/wowo/WowoNewsletterBundle)

The WowoNewsletterBundle provides easy way to send huge amount of rich HTML
e-mails. It uses beanstalkd queue broker to handle mails before they will be
sent. Bundle is highy extendable - you can provide your own source of contacts
or use default one, provided with bundle.

Features included:
- Sending HTML e-mails (with embed rich content)
- Customizable contacts source
- High performance (beanstalkd offfers several thousand operations per second)
- Personalizable messages - you can define as many placeholders as you wish
- Scalable-ready - you can put beanstalkd queue and worker which sends mails
 away from your main application webserver

 This bundle depends on [WowoQueueBundle](git://github.com/wowo/WowoQueueBundle.git), which is abstraction layer for beanstalkd messaging system

## Installation

### Step 1: Download WowoNewsletterBundle

Add following lines to your `deps` file:

```
    [WowoNewsletterBundle]
        git=git://github.com/wowo/WowoNewsletterBundle.git
        target=bundles/Wowo/NewsletterBundle

    [WowoQueueBundle]
        git=git://github.com/wowo/WowoQueueBundle.git
        target=bundles/Wowo/QueueBundle

```
Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

### Step 2: Configure the Autoloader

Add the `Wowo` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
        'Wowo' => __DIR__.'/../vendor/bundles',
        ));
```


### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
        $bundles = array(
            // ...
            new Wowo\NewsletterBundle\WowoNewsletterBundle(),
        );
}
```

### Step 4: run newsletter:send worker

Last thing you need to do, to achieve mailings sending is to run worker:

``` bash
$ php app/console newsletter:send
```

There's optional switch `--verbose` which can be useful as a simple stdout monitor

## TinyMCE integration

This bundle is TinyMCE-ready. Just turn on this bundle and add some config (example is below) and body field will transform into Rich Text editor.

``` yml
stfalcon_tinymce:
    include_jquery: true
    theme:
        advanced:
            mode: "textareas"
            theme: "advanced"
            theme_advanced_buttons1: "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,link,unlink"
            theme_advanced_buttons2: ""
            theme_advanced_buttons3: ""
            theme_advanced_toolbar_location: "top"
```

## Configuration

You can set plenty of parameters, which can be found in services.xml. Also you can
adjust some options in app/config/config.yml (mapping and templates)

### Parameters:

* wowo_newsletter.queue (default: newsletter_tube) - Beanstalkd tube name
* wowo_newsletter.default.sender_name (default: Wojciech Sznapka) - "from" name in email messages
* wowo_newsletter.default.sender_email (default: wojciech@sznapka.pl) - "from" address in email messages
* wowo_newsletter.form.can.choose.contacts.via.form (default: true) - determines if contacts can be choosen using form
* wowo_newsletter.form.has.delayed.sending (default: true) - determines wheter form allows to delay mailing (setting send date)

### Configuration (config.yml)

Example:
``` yml
wowo_newsletter:
    placeholders:
        key1: value1
        key2: value3
        key3: value3
        name:       getName
        email:      getEmail
    templates:
        'template name': %kernel.root_dir%/Resources/mailing/mailing.html
```

In placeholders you should provide map, in which key is placeholder name (example: email) and value is property/getter name on contact entity.
There are two obligatory keys: (email and name).

With templates you can set html templates (with images relative to its dir) source. By default it takes first position ('template name' in above) and resolves filesystem path for HTML template and images. You can add your own implementation, so user can choose from configured templates or even add his own (stored in database).

## Extension and adjustments guidelines

You can extend bundle by providing your own contact source. There are more extension points, but this one is most probably to use.

``` yml
parameters:
    wowo_newsletter.contact_manager.class: Your\Bundle\NewsletterContactManager
    wowo_newsletter.model.contact.class: Your\Bundle\Entity\User

wowo_newsletter:
    placeholders:
        firstname:  getFirstname
        lastname:   getLastname
        email:      getEmail
    templates:
        'main template': %kernel.root_dir%/Resources/templates/newsletter/mailing.html
```

In above example *User* is an existing Entity, which has fields firstname, lastname, email. For this purposes we wrote NewsletterContactManager which implements *Wowo\NewsletterBundle\Newsletter\Model\ContactManagerInterface* and provides bundle with contacts retrieved by Doctrine2.

![tracking](http://visitspy.net/spot/1c8ff7c1/track)