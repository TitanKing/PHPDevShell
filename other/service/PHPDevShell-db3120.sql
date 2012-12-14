REPLACE INTO `pds_core_plugin_activation` VALUES ('BotBlock', 'install', '1000', '0');
REPLACE INTO `pds_core_plugin_classes` VALUES ('', 'botBlock', 'PHPDS_botBlock', 'BotBlock', '1', '1');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_spam_assassin', '1', 'Should system attempt to protect public forms from spam bots?');