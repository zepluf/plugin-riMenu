CREATE TABLE IF NOT EXISTS `menus` (
  `menus_id` int(11) NOT NULL AUTO_INCREMENT,
  `menus_image` varchar(64) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(3) DEFAULT '0',
  `menus_status` tinyint(1) NOT NULL DEFAULT '1',
  `menus_main_page` varchar(255) NOT NULL,
  `menus_parameters` varchar(255) NOT NULL DEFAULT '',
  `menus_attributes` varchar(255) NOT NULL,
  `menus_is_ssl` int(1) NOT NULL DEFAULT '0',
  `menus_type` int(11) NOT NULL,
  PRIMARY KEY (`menus_id`),
  KEY `idx_parent_id_cat_id_zen` (`parent_id`,`menus_id`),
  KEY `idx_status_zen` (`menus_status`),
  KEY `idx_sort_order_zen` (`sort_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `menus_description` (
  `menus_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '1',
  `menus_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`menus_id`,`language_id`),
  KEY `idx_categories_name_zen` (`menus_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;