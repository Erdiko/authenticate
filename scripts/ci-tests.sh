#!/bin/sh

# Run unit tests inside of docker
cd /code/vendor/erdiko/authenticate/tests/
phpunit AllTests
