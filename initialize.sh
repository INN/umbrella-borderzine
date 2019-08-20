#!/bin/bash
# INN/umbrella-boilerplate Initialization Script, version 0.0.1
#
# Q: Why are we doing this with a script instead of git's native submodules?
# A: Git tracks submodules in the .gitmodules file and in the index. We can
#    copy the .gitmodules file from one repository to the other, but we cannot
#    copy the index easily. Copying .gitmodules is not sufficient to allow
#    running `git submodule update --init --recursive`, so to set up a new
#    umbrella repository from a fresh index, we're going to script adding the
#    submodules in a way that creates the entries in the index and fills out
#    the .gitmodules.
#
# Q: Why not script adding things from the .gitmodules?
# A: This seemed simpler and more maintainable than
#    https://stackoverflow.com/questions/11258737/restore-git-submodules-from-gitmodules/15302396#15302396
#
# Q: Does this check out the submodules at any particular commit?
# A: No. They're currently just checked out at the tip of the master branch.
#    It's assumed that if you're setting up boilerplate, you're going to check
#    out a needed commit yourself.

# make sure we're in the top level of the git repository
cd `git rev-parse --show-toplevel`

# add INN/deploy-tools at master
rm -r tools
git submodule add git@github.com:INN/deploy-tools.git tools

# add Largo at master, overriding the .gitignore on wp-content
mkdir -p wp-content/themes/
rm -r wp-content/themes/largo
git submodule add -f git@github.com:INN/Largo.git wp-content/themes/largo

mkdir -p wp-content/plugins/

# Move readme-template.md over README.md
if [ -f "readme-template.md" ]
then
	mv readme-template.md README.md
fi

# stage the git commit
git add .git-ftp-ignore .gitignore fabfile.py README.md requirements.txt LICENSE contributing.md

# cleanup
rm initialize.sh
rm -r docs/
