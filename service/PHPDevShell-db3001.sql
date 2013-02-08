INSERT INTO pds_core_plugin_classes VALUES ('', 'groupTree', 'PHPDS_groupTree', 'PHPDevShell', '1', '1');
INSERT INTO pds_core_plugin_classes VALUES ('', 'menuArray', 'PHPDS_menuArray', 'PHPDevShell', '1', '1');
INSERT INTO pds_core_plugin_classes VALUES ('', 'menuStructure', 'PHPDS_menuStructure', 'PHPDevShell', '1', '1');
INSERT INTO pds_core_plugin_classes VALUES ('', 'userPending', 'PHPDS_userPending', 'PHPDevShell', '1', '1');
INSERT INTO pds_core_plugin_classes VALUES ('', 'pluginManager', 'PHPDS_pluginManager', 'PHPDevShell', '1', '1');
INSERT INTO pds_core_plugin_classes VALUES ('', 'timeZone', 'PHPDS_timeZone', 'PHPDevShell', '1', '1');

REPLACE INTO pds_core_settings VALUES ('PHPDevShell_404_error_page', '562563911');
REPLACE INTO pds_core_settings VALUES ('PHPDevShell_404_error_page_message', 'Oops, this is rather embarrassing, the page you are looking for could not be found. Are you perhaps trying to access a restricted page? Could we interest you in any of the following pages?');

REPLACE INTO pds_core_settings VALUES ('PHPDevShell_reg_email_admin', 'Dear Admin,\r\n\r\nYou have received a new registration at %1$s.\r\nThe user registered with the name %2$s, on this date %3$s, with the username %4$s.\r\n\r\nThank You,\r\n%5$s.%6$s %7$s %8$s\r\n\r\nYou must be logged-in to ban or approve users.');
REPLACE INTO pds_core_settings VALUES ('PHPDevShell_reg_email_approve', 'Dear %1$s,\r\n\r\nYou completed the registration at %2$s.\r\nYour registration was successful but is still pending. This email is to verify that you requested to be registered, while confirming your email address at the same time.\r\n\r\nThank you for registering at %3$s, an Admin will attend to your request soon.');
REPLACE INTO pds_core_settings VALUES ('PHPDevShell_reg_email_direct', 'Dear %1$s,\r\n\r\nYou completed the registration at %2$s.\r\nYour registration was successful. This email is to verify that you requested to be registered, while confirming your email address at the same time.\r\n\r\nThank you for registering at %3$s.');
REPLACE INTO pds_core_settings VALUES ('PHPDevShell_reg_email_verify', 'Dear %1$s,\r\n\r\nYou requested registration at %2$s.\r\nYour registration was successful but it is still pending. This email is to verify that you requested to be registered, while confirming your email address at the same time.\r\nPlease click on the *link\r\n%3$s\r\nto complete the registration process.\r\n\r\nThank you for registering at %4$s.\r\n\r\n*If you cannot click on the link, copy and paste the url in your browsers address bar.');

REPLACE INTO pds_core_menu_items VALUES ('562563911', '0', 'Page Not Found', 'user/page-not-found.php', 'PHPDevShell', '1', '', '0', '10', '1', '844895956', 'page-not-found', '');

INSERT INTO pds_core_menu_structure VALUES ('', '562563911', '0', '2');

INSERT INTO pds_core_user_role_permissions VALUES ('', '1', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '2', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '4', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '5', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '6', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '7', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '8', '562563911');
INSERT INTO pds_core_user_role_permissions VALUES ('', '9', '562563911');