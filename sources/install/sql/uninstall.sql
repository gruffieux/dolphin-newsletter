DROP TABLE `mg_newsl_campagneContenus`;
DROP TABLE `mg_newsl_campagneMembres`;
DROP TABLE `mg_newsl_contenus`;
DROP TABLE `mg_newsl_campagnes`;
DROP TABLE `mg_newsl_membres`;
DROP TABLE `mg_newsl_blackList`;
DROP TABLE `mg_newsl_stats`;

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Newsletter' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'mg_newsl';

-- Cron job
DELETE FROM `sys_cron_jobs` WHERE `name` = 'Newsletter';

-- Bloques de page
DELETE FROM `sys_page_compose` WHERE `Caption` = '_mg_newsl_inscription';

-- Sous-Menu
DELETE FROM `sys_menu_top` WHERE `Name` = 'NewsletterHome';
DELETE FROM `sys_menu_top` WHERE `Name` = 'NewsletterMe';