Environments
============

The Frontend application is a standard Symfony application, which by default
exposes three environments - `dev`, `test` and `prod`. This is however not quite
sufficient for us as the frontend application may deployed to one of three
Cosmos environments: INT, TEST or LIVE. Most of the time we want to keep the
same configuration between these production environments. However there are some
cases where we want to change configuration between the INT, TEST and LIVE
Cosmos environments. Thus in total we have five environments.

We have chosen to follow Symfony's existing convention for naming environments,
but add extra environments based for the Cosmos environments we wish to
customise away from the LIVE production environment. Our environments are as
follows:

* `dev`: for local development
* `test`: for running unit tests
* `prod`: for running the deployed app in the Cosmos LIVE environment
* `prod_int`: for running the deployed app in the Cosmos INT environment
* `prod_test`: for running the deployed app in the Cosmos TEST environment

Both `prod_int` and `prod_test` inherit from `prod`. Only configuration
parameters should need to be changed between these environments.


## API crosstalk

The primary reason for requiring different configuration between the INT, TEST
and LIVE deployed environments is that any APIs that use certificates for
authentication are subject to the BBC's "no-crosstalk" rule which states that
INT and TEST applications can not request data from LIVE applications (but an
application on INT can request data from TEST APIs). Thus INT and TEST may need
to talk to TEST APIs as the LIVE API is inaccessible.


