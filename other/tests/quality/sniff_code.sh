echo '' > sniff.results.txt

phpcs --standard=PHPDS ../../includes/PHPDS_controller.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_core.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_db.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_dbConnector.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_debug.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_errorHandler.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_exception.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_navigation.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_query.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_security.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_tagger.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_template.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_user.class.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS_utils.inc.php >> sniff.results.txt
phpcs --standard=PHPDS ../../includes/PHPDS.inc.php >> sniff.results.txt

echo Done
