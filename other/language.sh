#!/bin/bash

# Give user options...
clear
echo ""
echo ""
echo " _     _  _        __            "
echo "|_)|_||_)| \ _    (_ |_  _  |  | "
echo "|  | ||  |_/(/_\_/__)| |(/_ |  | "
echo ""
echo "GNU/LGPL Copyright (C) 2010 Jason Schoeman"
echo ""
echo "What would you like to Extract?"
echo "Note, it is normal to see a few notices when extracting!"
echo ""
OPTION=0
echo "(1) Complete Core and Default Plugin Language Extraction"
echo "(2) Core Language Extraction Only"
echo "(3) Default Plugin Language Extraction Only"
echo -n "Choose [1 - 3] or enter plugin name to extract: "
read OPTION

# Handle options...
TYPE=1
if [ $OPTION -eq 1 ]; then
	echo "#######################################"
	echo "### Complete language extraction... ###"
	echo "#######################################"
	TYPE=1
else
	if [ $OPTION -eq 2 ]; then
			echo "########################################"
			echo "### Extracting core language only... ###"
			echo "########################################"
			TYPE=2
		else
			if [ $OPTION -eq 3 ]; then
					echo "###########################################"
					echo "### Extracting plugins language only... ###"
					echo "###########################################"
					TYPE=3
				else
					echo "#################################################"
					echo "### Third party extraction attempt on $OPTION ###"
					echo "#################################################"
					TYPE=4
				fi
		fi
fi

# Extract Core Strings
if [ $TYPE -eq 1 -o $TYPE -eq 2 ]; then
	rm ./language/core.lang.pot
	touch ./language/core.lang.pot
	find ./index.php ./includes ./themes/cloud ./language -type f -iname "*.php" | xgettext -o ./language/core.lang.pot --package-name=PHPDevShell --copyright-holder="Jason Schoeman" --msgid-bugs-address=titan@phpdevshell.org --from-code=utf-8 --no-wrap --language=PHP --keyword=_ --keyword=__ --keyword=___ -j -f -
fi

# Extract Plugins Strings
if [ $TYPE -eq 1 -o $TYPE -eq 3 -o $TYPE -eq 4 ]; then
	# Extract PHPDevShell Plugin
	# Set Plugin variable to extract complete directory.
	if [ $TYPE -eq 4 ]; then
		PLUGINLIST=$OPTION
	else
		PLUGINLIST="ControlPanel PHPDevShell FileMan PHPMailer PHPThumbs Pagination Smarty StandardLogin userActions CRUD RedBeanORM BotBlock"
	fi
	for PLUGIN in ${PLUGINLIST}; do
		# No need to change this.
		PLUGINDIR=./plugins/$PLUGIN
		LANGDIR=language/$PLUGIN
		LANGFILE=$PLUGINDIR/$LANGDIR.pot
		rm $LANGFILE
		touch $LANGFILE
		find $PLUGINDIR/ -type f -iname "*.php" | xgettext -o $LANGFILE --package-name=$PLUGIN --from-code=utf-8 --no-wrap --language=PHP --keyword=_ --keyword=__ -j -f -
		find $PLUGINDIR/ -type f -iname "*.tpl" | xgettext -o $LANGFILE --package-name=$PLUGIN --from-code=utf-8 --no-wrap --language=Python --keyword=_i --keyword=__i --keyword=_e --keyword=__e --keyword=_ --keyword=__ -j -f -
	done
fi

