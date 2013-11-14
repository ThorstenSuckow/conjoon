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


-- --
-- registry
-- --

INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(1, 'service', 0);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(2, 'youtube', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(3, 'chromeless', 2);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(4, 'client', 0);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(5, 'system', 4);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(6, 'sfx', 5);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(7, 'base', 0);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(8, 'conjoon', 7);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(9, 'server', 0);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(10, 'php', 9);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(11, 'environment', 4);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(12, 'environment', 9);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
(13, 'applicationCache', 4);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('14', 'modules', '8');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('15', 'email', '14');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('16', 'options', '15');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('17', 'reading', '16');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('20', 'state', '5');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('21', 'workbench', '20');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('22', 'widgets', '21');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('23', 'panels', '21');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('24', 'emailModule', '20');
 INSERT INTO `{DATABASE.TABLE.PREFIX}registry` (`id`, `key`, `parent_id`) VALUES
('25', 'contentPanel', '24');


-- --
-- registry_values
-- --
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(6, 0, 'enabled', '1', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'cache-flash', '0', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'cache-html', '0', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'cache-images', '0', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'cache-javascript', '0', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'cache-sounds', '0', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'cache-stylesheets', '0', 'BOOLEAN', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
(13, 0, 'last-changed', '0', 'FLOAT', 1);
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('17', '0', 'preferred_format', 'plain', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('17', '0', 'allow_externals', '0', 'BOOLEAN', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('22', '0', 'quickPanelWidget', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('22', '0', 'twitterWidget', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('22', '0', 'emailWidget', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('22', '0', 'feedWidget', '', 'STRING', '1');
 INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('23', '0', 'eastPanel', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('23', '0', 'westPanel', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('25', '0', 'folderTree', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('23', '0', 'contentPanel', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('25', '0', 'previewButton', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('25', '0', 'rightPreview', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('25', '0', 'bottomPreview', '', 'STRING', '1');
INSERT INTO `{DATABASE.TABLE.PREFIX}registry_values` (`registry_id`, `user_id`, `name`, `value`, `type`, `is_editable`) VALUES
('25', '0', 'mailItemsGrid', '', 'STRING', '1');
