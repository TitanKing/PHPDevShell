#!/bin/bash

# Pass the directory through that needs to be sync to, sync /some/directory
# Sync main trunk to other projects.
clear
echo ""
echo ""
echo " _     _  _        __            "
echo "|_)|_||_)| \ _    (_ |_  _  |  | "
echo "|  | ||  |_/(/_\_/__)| |(/_ |  | "
echo ""
echo "GNU/LGPL Copyright (C) 2012 Jason Schoeman"
echo ""
echo "This script will attempt to updated your other projects from this release to whatever directory you wish. This shell uses rsync."
echo ""

# Ask for dry run test?
# ---------------------
echo -n "Would you like to do a test run only? [Y/N] (Default Yes) : "
read TEST

if [ "$TEST" = "Y" -o "$TEST" = "" ]; then
	TEST="--dry-run"
	echo "Only a test will be run!"
else
	TEST=""
	echo "THIS IS NOT A TEST!"
fi

# Will we be using SSH to connect to remote server?
# -------------------------------------------------
echo -n "Will this be a remote SSH connection? [Y/N] (Default No) : "
read SSH

if [ "$SSH" = "Y" ]; then
	echo -n "What should the SSH port be? (Default 22) : "
	read SSH

	if [ "$SSH" = "" ]; then
		SSH="-e \"ssh -p 22\""
	else
		SSH="-e \"ssh -p $SSH\""
	fi
	echo "Will attempt to connect remotely via SSH on port $SSH!"
else
	SSH=""
	echo "Local syncing selected!"
fi

# What directory will we be syncing to?
# ---------------------------------------
SOURCE=$1
if [ "$SOURCE" = "" ]; then
	SOURCE=""
	echo "You could also provide a source directory from terminal direct eg. sync /source/some/phpdev /destination/some/directory"
	echo ""
	echo -n "Enter the SOURCE directory (/some/development) or remote server (remoteuser@remotehost.remotedomain:/home/path/folder2) where it needs to sync to: "
	read SOURCE
fi

if [ "$SOURCE" = "" ]; then
	echo ""
	echo "Error (1):"
	echo ""
	echo "No source path given!"
	echo "Pass the SOURCE directory through that needs to be sync to eg. sync.sh /source/some/phpdev /destination/some/directory"
	exit 0
fi

DIR=$2
if [ "$DIR" = "" ]; then
	DIR=""
	echo "You could also provide a destination directory from terminal direct eg. sync /source/some/phpdev /destination/some/directory"
	echo ""
	echo -n "Enter the DESTINATION directory (/some/development) or remote server (remoteuser@remotehost.remotedomain:/home/path/folder2) where it needs to sync from: "
	read DIR
fi

if [ "$DIR" = "" ]; then
	echo ""
	echo "Error (1):"
	echo ""
	echo "No destination path given!"
	echo "Pass the DESTINATION directory through that needs to be sync from eg. sync.sh /source/some/phpdev /destination/some/directory"
	exit 0
fi

# Would you like to sync only a specific folder of PHPDevShell?
# -----------------------------------------------------------
echo -n "Would you like to sync only a specific folder of PHPDevShell eg. plugins (Default do ALL) ? "
read SPECIFIC

if [ "$SPECIFIC" = "" ]; then
	echo "Selected to sync all project folders..."
	CFG1="rsync $TEST -av --progress $SSH $SOURCE/config/PHPDS-defaults.config.php $DIR/config/PHPDS-defaults.config.php"
	CFG2="rsync $TEST -av --progress $SSH $SOURCE/includes/ $DIR/includes/"
	CFG3="rsync $TEST -av --progress $SSH $SOURCE/language/ $DIR/language/"
	CFG4="rsync $TEST -av --progress $SSH $SOURCE/other/ $DIR/other/"
	CFG5="rsync $TEST -av --progress $SSH $SOURCE/plugins/ $DIR/plugins/"
	CFG6="rsync $TEST -av --progress $SSH $SOURCE/readme/ $DIR/readme/"
	CFG7="rsync $TEST -av --progress $SSH $SOURCE/tests/ $DIR/tests/"
	CFG8="rsync $TEST -av --progress $SSH $SOURCE/themes/ $DIR/themes/"
	CFG9="rsync $TEST -av --progress $SSH $SOURCE/gzip.php $DIR/gzip.php"
	CFG10="rsync $TEST -av --progress $SSH $SOURCE/index.php $DIR/index.php"
	CFG11="rsync $TEST -av --progress $SSH $SOURCE/rename.htaccess $DIR/rename.htaccess"

	eval "$CFG5; $CFG8; $CFG2; $CFG1; $CFG3; $CFG4; $CFG6; $CFG7; $CFG9; $CFG10; $CFG11"
else
	echo "Selected to sync only selected folder $SPECIFIC"
	eval "rsync $TEST -av --progress $SSH $SOURCE/$SPECIFIC/ $DIR/$SPECIFIC/"
fi

