-- conjoon
-- (c) 2002-2012 siteartwork.de/conjoon.org
-- licensing@conjoon.org
--
-- $Author$
-- $Id$
-- $Date$
-- $Revision$
-- $LastChangedDate$
-- $LastChangedBy$
-- $URL$

-- This file will be parsed by the conjoon install wizard. If you wish to execute the
-- sql queries found herin by hand, make sure you remove/ replace the tokens
-- {DATABASE.TABLE.PREFIX}.

--
-- `groupware_contact_items`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_contact_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `first/last_name` (`first_name`,`last_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_contact_items_email`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_contact_items_email` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupware_contact_items_id` int(10) unsigned NOT NULL,
  `email_address` text NOT NULL,
  `is_standard` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `groupware_contact_items_id` (`groupware_contact_items_id`),
  KEY `is_standard` (`groupware_contact_items_id`,`email_address`(255),`is_standard`),
  KEY `email_address` (`groupware_contact_items_id`,`email_address`(255))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_contact_items_flags`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_contact_items_flags` (
  `groupware_contact_items_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`groupware_contact_items_id`,`user_id`),
  KEY `contact_for_user` (`user_id`,`is_deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_accounts`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_accounts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `address` varchar(255) NOT NULL,
  `reply_address` varchar(255) default NULL,
  `is_standard` tinyint(1) NOT NULL default '0',
  `protocol` enum('POP3','IMAP') NOT NULL default 'POP3',
  `server_inbox` varchar(255) NOT NULL,
  `server_outbox` varchar(255) NOT NULL,
  `username_inbox` varchar(64) NOT NULL,
  `username_outbox` varchar(64) default NULL,
  `user_name` varchar(255) NOT NULL,
  `is_outbox_auth` tinyint(1) NOT NULL default '0',
  `password_inbox` varchar(32) NOT NULL,
  `password_outbox` varchar(32) default NULL,
  `signature` varchar(255) default NULL,
  `is_signature_used` tinyint(1) NOT NULL default '0',
  `port_inbox` smallint(5) unsigned NOT NULL default '110',
  `port_outbox` smallint(5) unsigned NOT NULL default '25',
  `is_copy_left_on_server` tinyint(1) NOT NULL default '1',
  `is_deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='table for storing email accounts';

-- --------------------------------------------------------

--
-- `groupware_email_folders`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_folders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `is_child_allowed` tinyint(1) NOT NULL default '1',
  `is_locked` tinyint(1) NOT NULL default '0',
  `type` enum('accounts_root','root','inbox','spam','trash','draft','sent','outbox','folder') NOT NULL,
  `meta_info` enum('inbox','draft','sent','outbox') NOT NULL default 'inbox',
  `parent_id` int(10) unsigned NOT NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_folders_accounts`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_folders_accounts` (
  `groupware_email_folders_id` int(10) unsigned NOT NULL,
  `groupware_email_accounts_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`groupware_email_folders_id`,`groupware_email_accounts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_items`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupware_email_folders_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `subject` text,
  `from` text NOT NULL,
  `reply_to` text,
  `to` text NOT NULL,
  `cc` text,
  `bcc` text,
  `in_reply_to` text,
  `references` text,
  `content_text_plain` longtext,
  `content_text_html` longtext,
  `recipients` text NOT NULL,
  `sender` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `groupware_email_folders_id` (`groupware_email_folders_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for storing incomin emails in a readable format.';

-- --------------------------------------------------------

--
-- `groupware_email_items_attachments`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `groupware_email_items_id` int(11) unsigned NOT NULL,
  `file_name` tinytext NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `encoding` varchar(32) NOT NULL,
  `content` blob NOT NULL,
  `content_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `groupware_email_items_id` (`groupware_email_items_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_items_flags`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` (
  `groupware_email_items_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_read` tinyint(1) NOT NULL default '0',
  `is_spam` tinyint(1) NOT NULL default '0',
  `is_deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`groupware_email_items_id`,`user_id`),
  KEY `flags` (`is_read`,`is_spam`,`is_deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_items_inbox`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_items_inbox` (
  `groupware_email_items_id` int(10) unsigned NOT NULL,
  `raw_header` longblob NOT NULL,
  `raw_body` longblob NOT NULL,
  `hash` varchar(32) default NULL,
  `message_id` varchar(255) default NULL,
  `uid` varchar(255) default NULL,
  `fetched_timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`groupware_email_items_id`),
  KEY `hash` (`hash`),
  KEY `fetched_timestamp` (`groupware_email_items_id`,`fetched_timestamp`),
  KEY `uid` (`uid`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_items_outbox`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_items_outbox` (
  `groupware_email_items_id` int(10) unsigned NOT NULL,
  `groupware_email_accounts_id` int(10) unsigned NOT NULL,
  `raw_header` longblob NOT NULL,
  `raw_body` longblob NOT NULL,
  `sent_timestamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`groupware_email_items_id`),
  KEY `accounts_is` (`groupware_email_accounts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_email_items_references`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_items_references` (
  `groupware_email_items_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `reference_items_id` int(10) unsigned NOT NULL,
  `reference_type` enum('','reply','reply_all','forward') NOT NULL,
  `is_pending` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`groupware_email_items_id`,`user_id`),
  KEY `references` (`reference_items_id`,`is_pending`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_feeds_accounts`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_feeds_accounts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `uri` varchar(255) NOT NULL,
  `link` tinytext,
  `description` tinytext,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `update_interval` int(10) unsigned NOT NULL default '3600',
  `delete_interval` int(10) unsigned NOT NULL default '172800',
  `last_updated` int(10) unsigned NOT NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_feeds_items`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_feeds_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupware_feeds_accounts_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pub_date` datetime NOT NULL,
  `link` tinytext NOT NULL,
  `guid` tinytext NOT NULL,
  `author` varchar(255) default NULL,
  `is_read` tinyint(1) NOT NULL default '0',
  `saved_timestamp` int(10) unsigned NOT NULL,
  `content` text,
  PRIMARY KEY  (`id`),
  KEY `groupware_feeds_accounts_id` (`groupware_feeds_accounts_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `groupware_feeds_items_flags`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_feeds_items_flags` (
  `groupware_feeds_accounts_id` int(10) unsigned NOT NULL,
  `guid` tinytext NOT NULL,
  PRIMARY KEY  (`groupware_feeds_accounts_id`,`guid`(255))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- `users`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `{DATABASE.TABLE.PREFIX}users` ADD `user_name` VARCHAR( 64 ) NOT NULL AFTER `email_address` ;
ALTER TABLE `{DATABASE.TABLE.PREFIX}users` ADD INDEX `username` ( `user_name` );

ALTER TABLE `{DATABASE.TABLE.PREFIX}users` ADD `is_root` BOOL NOT NULL DEFAULT '0';

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_accounts` ADD `request_timeout` TINYINT NOT NULL DEFAULT '10' AFTER `last_updated`;

 CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}service_twitter_accounts` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 15 ) NOT NULL ,
`password` VARCHAR( 32 ) NOT NULL ,
`update_interval` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_references` DROP INDEX `references` ,
ADD INDEX `references` ( `reference_items_id` , `is_pending` , `user_id` );

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_inbox` CHANGE `fetched_timestamp` `fetched_timestamp` INT( 11 ) UNSIGNED NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` ADD INDEX `user_id` ( `id` );

ALTER TABLE `{DATABASE.TABLE.PREFIX}users` ADD `auth_token` VARCHAR( 32 ) NOT NULL ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}users` ADD `last_login` INT( 11 ) UNSIGNED NULL ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items` ADD `author_uri` TEXT NULL AFTER `author` ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items` ADD `author_email` TEXT NULL AFTER `author_uri` ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_accounts` ADD `is_image_enabled` BOOL NOT NULL DEFAULT '0' AFTER `request_timeout` ;


-- --------------------------------------------------------

--
-- `groupware_email_folders_users`
--

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_folders_users` (
`groupware_email_folders_id` INT UNSIGNED NOT NULL ,
`users_id` INT UNSIGNED NOT NULL ,
`relationship` ENUM( 'owner' ) NOT NULL,
 PRIMARY KEY ( `groupware_email_folders_id` , `users_id` )
) ENGINE = MYISAM;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` CHANGE `update_interval` `update_interval` INT( 10 ) UNSIGNED NOT NULL DEFAULT '60000';

ALTER TABLE `{DATABASE.TABLE.PREFIX}users` CHANGE `auth_token` `auth_token` VARCHAR( 32 ) NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_accounts` ADD `inbox_connection_type` ENUM( 'SSL', 'TLS' ) NULL AFTER `port_outbox`,
ADD `outbox_connection_type` ENUM( 'SSL', 'TLS' ) NULL AFTER `inbox_connection_type` ;

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_email_imap_mapping` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`groupware_email_accounts_id` INT UNSIGNED NOT NULL ,`global_name` TEXT NULL ,`type` ENUM( 'INBOX', 'OUTBOX', 'SENT', 'DRAFT', 'TRASH' )
NOT NULL ,PRIMARY KEY ( `id` )) ENGINE = MYISAM;

 CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}registry` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`key` VARCHAR( 255 ) NOT NULL ,
`parent_id` INT UNSIGNED NOT NULL
) ENGINE = MYISAM;

 CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}registry_values` (
`registry_id` INT UNSIGNED NOT NULL ,
`user_id` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`value` TEXT NOT NULL ,
`type` ENUM( 'STRING', 'INTEGER', 'BOOLEAN', 'FLOAT') NOT NULL,
INDEX ( `registry_id` , `user_id` )
) ENGINE = MYISAM;

ALTER TABLE `{DATABASE.TABLE.PREFIX}registry_values` ADD PRIMARY KEY ( `user_id` , `name` );

ALTER TABLE `{DATABASE.TABLE.PREFIX}registry` ADD UNIQUE (`key` ,`parent_id`);

ALTER TABLE `{DATABASE.TABLE.PREFIX}registry_values` ADD `is_editable` BOOL NOT NULL DEFAULT '1';


ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments` ADD `key` VARCHAR( 32 ) NOT NULL AFTER `id`;

UPDATE `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments` SET `key`=MD5(RAND()) WHERE `key` = '';

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments` ADD UNIQUE `key` ( `key` );


ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` DROP `password`;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` ADD `oauth_token` VARCHAR( 255 ) NOT NULL AFTER `name` ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` ADD `oauth_token_secret` VARCHAR( 255 ) NOT NULL AFTER `oauth_token` ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` ADD `twitter_id` VARCHAR( 255 ) NOT NULL AFTER `name` ;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` DROP INDEX `user_id` ,
ADD INDEX `user_id` ( `user_id` );

ALTER TABLE  `{DATABASE.TABLE.PREFIX}groupware_feeds_items` CHANGE  `content`  `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items` CHANGE `references` `references` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items_flags` CHANGE `guid` `guid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE  IF NOT EXISTS  `{DATABASE.TABLE.PREFIX}groupware_files_folders` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`is_child_allowed` BOOL NOT NULL ,
`is_locked` BOOL NOT NULL ,
`type` ENUM( 'temp', 'trash' ) NOT NULL ,
`parent_id` INT NOT NULL
) ENGINE = MYISAM;

 CREATE TABLE  IF NOT EXISTS  `{DATABASE.TABLE.PREFIX}groupware_files` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`groupware_files_folders_id` INT UNSIGNED NOT NULL ,
`key` VARCHAR( 32 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`mime_type` VARCHAR( 255 ) NOT NULL ,
`content` LONGBLOB NULL,
`storage_container` VARCHAR( 255 ) NULL
) ENGINE = MYISAM;

CREATE TABLE IF NOT EXISTS `{DATABASE.TABLE.PREFIX}groupware_files_folders_users` (
  `groupware_files_folders_id` int(10) unsigned NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `relationship` enum('owner') NOT NULL
) ENGINE=MyISAM;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files_folders_users` ADD PRIMARY KEY ( `groupware_files_folders_id`  , `users_id` );

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_contact_items_email` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_contact_items_flags` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_accounts` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders_accounts` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders_users` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_inbox` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_outbox` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_references` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_accounts` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items_flags` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}users` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

-- CN-474
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items` CHANGE `title` `title` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_contact_items` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_imap_mapping` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files_folders` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files_folders_users` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}registry` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE `{DATABASE.TABLE.PREFIX}registry_values` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_contact_items`
CHANGE `first_name` `first_name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `last_name` `last_name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_contact_items_email`
CHANGE `email_address` `email_address` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_accounts`
CHANGE `name` `name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `address` `address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `reply_address` `reply_address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `protocol` `protocol` ENUM('POP3','IMAP') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'POP3',
CHANGE `server_inbox` `server_inbox` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `server_outbox` `server_outbox` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `username_inbox` `username_inbox` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `username_outbox` `username_outbox` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `user_name` `user_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `password_inbox` `password_inbox` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `password_outbox` `password_outbox` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `signature` `signature` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `inbox_connection_type` `inbox_connection_type` ENUM('SSL','TLS') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `outbox_connection_type` `outbox_connection_type` ENUM('SSL','TLS') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders`
CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `meta_info` `meta_info` ENUM( 'inbox', 'draft', 'sent', 'outbox' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'inbox';

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders_users`
CHANGE `relationship` `relationship` ENUM( 'owner' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_imap_mapping`
CHANGE `global_name` `global_name` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `type` `type` ENUM( 'INBOX', 'OUTBOX', 'SENT', 'DRAFT', 'TRASH' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items`
CHANGE `subject` `subject` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `from` `from` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `reply_to` `reply_to` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `to` `to` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `cc` `cc` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `bcc` `bcc` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `in_reply_to` `in_reply_to` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `references` `references` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `content_text_plain` `content_text_plain` LONGTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `content_text_html` `content_text_html` LONGTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
CHANGE `recipients` `recipients` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
CHANGE `sender` `sender` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments`
CHANGE `key` `key` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `file_name` `file_name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `encoding` `encoding` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `content_id` `content_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_inbox`
CHANGE `hash` `hash` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `message_id` `message_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `uid` `uid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_references`
CHANGE `reference_type` `reference_type` ENUM( '', 'reply', 'reply_all', 'forward' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_accounts`
CHANGE `uri` `uri` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `link` `link` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `description` `description` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items`
CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `link` `link` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `guid` `guid` TINYTEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `author` `author` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `author_uri` `author_uri` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `author_email` `author_email` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items`
CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items_flags`
CHANGE `guid` `guid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;


ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files`
CHANGE `key` `key` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `storage_container` `storage_container` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files_folders` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `type` `type` ENUM( 'temp', 'trash' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_files_folders_users`
CHANGE `relationship` `relationship` ENUM( 'owner' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}registry`
CHANGE `key` `key` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}registry_values`
CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `type` `type` ENUM( 'STRING', 'INTEGER', 'BOOLEAN', 'FLOAT' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}service_twitter_accounts`
CHANGE `name` `name` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `twitter_id` `twitter_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `oauth_token` `oauth_token` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `oauth_token_secret` `oauth_token_secret` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `{DATABASE.TABLE.PREFIX}users`
CHANGE `firstname` `firstname` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `lastname` `lastname` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `email_address` `email_address` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `user_name` `user_name` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `password` `password` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `auth_token` `auth_token` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;

-- CN-469
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders` CHANGE `type`
`type` ENUM( 'accounts_root', 'root', 'root_remote', 'inbox',
'spam', 'trash', 'draft', 'sent', 'outbox', 'folder' )
CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

-- CN-589
ALTER TABLE `{DATABASE.TABLE.PREFIX}users` CHANGE `email_address` `email_address` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `{DATABASE.TABLE.PREFIX}users` CHANGE `user_name` `user_name` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `{DATABASE.TABLE.PREFIX}users` DROP INDEX `username`, ADD UNIQUE `username` ( `user_name` );

-- CN-616
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_attachments` CHANGE `content` `content` LONGBLOB NOT NULL;

-- CN-618
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items` CHANGE `guid` `guid` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items` CHANGE `link` `link` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_feeds_items_flags` CHANGE `guid` `guid` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

-- CN-655
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders` CHANGE `parent_id` `parent_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;
UPDATE `{DATABASE.TABLE.PREFIX}groupware_email_folders` SET `parent_id` = NULL WHERE `parent_id`=0;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders` ADD FOREIGN KEY ( `parent_id` )
REFERENCES `{DATABASE.TABLE.PREFIX}groupware_email_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

-- CN-661
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items` ADD FOREIGN KEY ( `groupware_email_folders_id` )
REFERENCES `{DATABASE.TABLE.PREFIX}groupware_email_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` DROP PRIMARY KEY;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` CHANGE `groupware_email_items_id` `groupware_email_items_id`
INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` CHANGE `user_id` `user_id`
INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` ADD PRIMARY KEY
( `groupware_email_items_id` , `user_id` );
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` ADD FOREIGN KEY ( `groupware_email_items_id` )
REFERENCES `{DATABASE.TABLE.PREFIX}groupware_email_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_items_flags` ADD FOREIGN KEY ( `user_id` )
REFERENCES `{DATABASE.TABLE.PREFIX}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

-- CN-687
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_accounts` ADD FOREIGN KEY ( `user_id` )
REFERENCES `{DATABASE.TABLE.PREFIX}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `{DATABASE.TABLE.PREFIX}groupware_email_folders_accounts`
ADD FOREIGN KEY ( `groupware_email_accounts_id` )
REFERENCES `{DATABASE.TABLE.PREFIX}groupware_email_accounts` (`id`)
ON DELETE CASCADE ON UPDATE CASCADE ;
