# WowoNewsletterBundle

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

## Installation

### Step 2: Download WowoNewsletterBundle

Add following lines to your `deps` file:

```
    [WowoNewsletterBundle]
        git=git://github.com/wowo/WowoNewsletterBundle.git
        target=bundles/Wowo/NewsletterBundle
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

### Step 4: install and run beanstalkd

On Debian linux systems (including Ubuntu) you can run:

``` bash
$ sudo apt-get install beanstalkd
```

Then run it as a daemon:

``` bash
$ beanstalkd -d -l 127.0.01 -p 11300
```

### Step 5: run newsletter:send worker

Last thing you need to do, to achieve mailings sending is to run worker:

``` bash
$ php app/console newsletter:send
```

There's optional switch `--verbose` which can be useful as a simple stdout monitor
