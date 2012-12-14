#!/bin/bash


rm -rf $PWD/other/phpdoc/*
/usr/bin/phpdoc										\
	-d $PWD/includes/								\
	-i $PWD/includes/legacy/mootols/			\
	-i $PWD/includes/legacy/phpmailer/		\
	-i $PWD/includes/legacy/smarty/				\
	-i $PWD/includes/legacy/thumb_plugin/	\
	-t $PWD/other/phpdoc/							\
	-ct date													\
	-ti PHPDevShell