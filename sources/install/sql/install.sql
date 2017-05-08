CREATE TABLE IF NOT EXISTS `mg_newsl_campagnes` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `Nom` VARCHAR(50) NOT NULL ,
    `Descriptif` TEXT NOT NULL ,
    `DateEcheance` DATETIME NOT NULL ,
    `DateEnvoi` DATETIME NOT NULL ,
    `Active` TINYINT UNSIGNED NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mg_newsl_membres` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `Email` VARCHAR(255) NOT NULL ,
    `Nom` VARCHAR(50) NOT NULL ,
    `Prenom` VARCHAR(50) NOT NULL ,
    `Pays` VARCHAR(20) NOT NULL ,
    `Ville` VARCHAR(50) NOT NULL ,
    `IDLangue` TINYINT UNSIGNED NOT NULL default '0' ,
    `DateNaissance` DATE NOT NULL ,
    `Zip` VARCHAR(255) NOT NULL ,
    `IDSource` VARCHAR(20) NOT NULL ,
    `Sexe` VARCHAR(20) NOT NULL ,
    `Adresse` VARCHAR(255) NOT NULL ,
    `ComplementAdresse` VARCHAR(255) NOT NULL ,
    `Telephone` VARCHAR(50),
    `BotScore` INT UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mg_newsl_campagneMembres` (
    `IDCampagne` INT UNSIGNED NOT NULL default '0' ,
    `IDMembre` INT UNSIGNED NOT NULL default '0' ,
    `DateInscription` DATETIME NOT NULL ,
    `DateDesinscription` DATETIME NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mg_newsl_contenus` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `IDLangue` TINYINT UNSIGNED NOT NULL default '0' ,
    `Titre` VARCHAR(255) NOT NULL ,
    `Corps` TEXT NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mg_newsl_campagneContenus` (
    `IDCampagne` INT UNSIGNED NOT NULL default '0' ,
    `IDContenu` INT UNSIGNED NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mg_newsl_blackList` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `Email` VARCHAR(255) NOT NULL ,
    `IDSource` VARCHAR(20) NOT NULL ,
    `DateSuppression` DATETIME NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mg_newsl_stats` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `IDCampagne` INT UNSIGNED NOT NULL default '0' ,
    `Email` VARCHAR(255) NOT NULL ,
    `Date` DATETIME NOT NULL ,
    `Type` VARCHAR(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Newsletter', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('mg_newsl_test_dummy', '', @iCategId, 'Use test e-mail', 'checkbox', '', '', '1', ''),
('mg_newsl_test_email', '', @iCategId, 'Test e-mail', 'digit', '', '', '2', ''),
('mg_newsl_imap_box', '{mail.yourdomain.com:143/novalidate-cert}INBOX', @iCategId, 'IMAP box', 'digit', '', '', '3', ''),
('mg_newsl_imap_user', '', @iCategId, 'IMAP user', 'digit', '', '', '4', ''),
('mg_newsl_imap_pwd', '', @iCategId, 'IMAP password', 'digit', '', '', '5', ''),
('mg_newsl_bot_pattern', 'failure notice', @iCategId, 'Subject mail returned when failure<br />(regular expression)', 'text', '', '', '6', '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'mg_newsl', '_mg_newsl', '{siteUrl}modules/?r=newsletter/administration/', 'Create campaigns, contents and associate e-mails to send to.', 'modules/mensgo/newsletter/|admin-menu.png', @iMax+1);

-- Cron job
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('Newsletter', '0 0 * * *', 'MgNewsletterCron', 'modules/mensgo/newsletter/classes/MgNewsletterCron.php', '');

-- Bloques de page
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES
('pedit', '1140px', 'Simple HTML Block', '_mg_newsl_inscription', 2, 3, 'PHP', '$aProfile = getProfileInfo($GET[''ID'']);\r\n$hash = BxDolService::call("newsletter", "membre_to_hash", array($aProfile[''Email'']));\r\nreturn BxDolService::call("newsletter", "inscription_block", array($hash));', 11, 28.1, 'memb', 0, 0);

-- Sous-Menu
SELECT @iTMOrderHome:=MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='4';
SELECT @iTMOrderMe:=MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`='118';
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(4, 'NewsletterHome', '_mg_newsl_inscription', 'modules/?r=newsletter/', @iTMOrderHome+1, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''),
(118, 'NewsletterMe', '_mg_newsl_me', 'modules/?r=newsletter/me', @iTMOrderMe+1, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, '');
