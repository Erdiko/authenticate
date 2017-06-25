#!/bin/sh

# set branch and use everywhere
BRANCH="develop"
REPO="authenticate"

# Add some color bling
GREEN='\033[0;32m'
RESET='\033[0m'

pwd
ls -la

cd ../

# Clone user-admin
git clone git@github.com:Erdiko/user-admin.git
cd ./user-admin
git checkout $BRANCH
echo "${GREEN}user-admin updated\n ${RESET}"

# Clone repos
chmod 770 scripts/ci/*.sh
./scripts/ci/install-repos.sh

# Swap in the recent ci build code
cd ../
rm -rf repos/authenticate
cp -R /code repos/authenticate
mv /user-admin /code

# Debug
pwd
ls -al
