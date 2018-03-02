Blogs
=====

Blogs v5. Hosted in the Cloud.

How to install
-------
To run the Blogs v5 application locally, you'll need an up-to-date [programmes-cloud-sandbox](https://github.com/bbc/programmes-cloud-sandbox).

**Clone the repository into your workspace**

    git@github.com:bbc/blogs-frontend.git
    
**Install dependencies**

    composer install
    
When you first run this command, you'll be prompted to provide values for the `parameters.yaml` file. 
The defaults are fine, but you may need to obtain an API key for the comments API.

**Install Static Assets**

[Install Yarn](https://yarnpkg.com/en/docs/install) if you don't already have it.

Perform `yarn install`

To compile the static assests, it's `yarn run gulp`

To watch for file changes, it's `yarn run watch`

Tests
-------
Blogs v5 uses PHPUnit for unit tests, and the `symfony/phpunit-bridge` package.

Unit tests can be run using the following command:
    
    bin/phpunit

A test script, which runs unit tests, code sniffer and static analysis tools is located in the bin directory and is run using the following command:

    bin/test
    
Developing
-------
The application is available at the following endpoint when run in the sandbox:

    http://sandbox.bbc.co.uk:83/blogs

License
-------

This repository is available under the terms of the Apache 2.0 license.
View the [LICENSE file](LICENSE) file for more information.

Copyright (c) 2017 BBC
