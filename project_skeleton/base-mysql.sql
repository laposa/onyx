SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `client_action` (
`id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `action_id` varchar(255) DEFAULT NULL,
  `network` varchar(255) DEFAULT NULL,
  `action_name` varchar(255) DEFAULT NULL,
  `object_name` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_address` (
`id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `line_1` varchar(255) DEFAULT NULL,
  `line_2` varchar(255) DEFAULT NULL,
  `line_3` varchar(255) DEFAULT NULL,
  `post_code` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_company` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `www` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `vat_no` varchar(255) DEFAULT NULL,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_customer` (
`id` int(11) NOT NULL,
  `title_before` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `title_after` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `mobilephone` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `invoices_address_id` int(11) DEFAULT NULL,
  `delivery_address_id` int(11) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `currency_code` char(3) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `newsletter` smallint(6) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `other_data` longtext,
  `modified` datetime DEFAULT NULL,
  `account_type` smallint(6) NOT NULL DEFAULT '0',
  `agreed_with_latest_t_and_c` smallint(6) NOT NULL DEFAULT '0',
  `verified_email_address` smallint(6) NOT NULL DEFAULT '0',
  `oauth` longtext,
  `deleted_date` datetime DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `twitter_id` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `profile_image_url` longtext,
  `store_id` int(11) DEFAULT NULL,
  `janrain_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `client_customer` (`id`, `title_before`, `first_name`, `last_name`, `title_after`, `email`, `username`, `telephone`, `mobilephone`, `nickname`, `password`, `company_id`, `invoices_address_id`, `delivery_address_id`, `gender`, `created`, `currency_code`, `status`, `newsletter`, `birthday`, `other_data`, `modified`, `account_type`, `agreed_with_latest_t_and_c`, `verified_email_address`, `oauth`, `deleted_date`, `facebook_id`, `twitter_id`, `google_id`, `profile_image_url`, `store_id`, `janrain_id`) VALUES
(0, '', 'Anonym', 'Anonymous', '', 'anonym@noemail.noemail', 'anonymous', 'notelephone', '', '', '9ce21d8f3992d89a325aa9dcf520a591', 0, 1, 1, '', '2011-12-13 14:00:00', 'GBP', 0, 0, '2007-06-14', '', '2011-12-13 14:00:00', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1, 'Mr', 'Onxshop', 'Tester', NULL, 'test@onxshop.com', NULL, '+44(0) 2890 328 988', NULL, NULL, 'b3f61bf1cb26243ef478a3c181dd0aa2', 0, 1, 1, NULL, '2011-12-13 14:00:00', 'GBP', 1, 0, NULL, '', '2011-12-13 14:00:00', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `client_customer_group` (
`id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_customer_image` (
`id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `node_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `content` longtext,
  `other_data` longtext,
  `link_to_node_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_customer_role` (
`id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_customer_taxonomy` (
`id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `taxonomy_tree_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_customer_token` (
`id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `publish` smallint(6) NOT NULL DEFAULT '0',
  `token` char(32) DEFAULT NULL,
  `oauth_data` longtext,
  `other_data` longtext,
  `ttl` int(11) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `http_user_agent` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_group` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext,
  `search_filter` longtext,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `client_role` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext,
  `other_data` longtext
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO `client_role` (`id`, `name`, `description`, `other_data`) VALUES
(1, 'Admin', NULL, NULL),
(2, 'Front Office Only CMS Editor', NULL, NULL),
(3, 'CMS Editor', NULL, NULL),
(4, 'eCommerce Editor', NULL, NULL),
(5, 'Customer Services', NULL, NULL),
(6, 'Warehouse', NULL, NULL);

CREATE TABLE IF NOT EXISTS `client_role_permission` (
`id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `resource` enum('_all_') DEFAULT NULL,
  `operation` enum('_all_') DEFAULT NULL,
  `scope` longtext,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `other_data` longtext
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

INSERT INTO `client_role_permission` (`id`, `role_id`, `resource`, `operation`, `scope`, `created`, `modified`, `other_data`) VALUES
(1, 1, '_all_', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(2, 2, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(3, 2, '', '', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(4, 2, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(5, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(6, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(7, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(8, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(9, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(10, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(11, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(12, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(13, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(14, 3, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(15, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(16, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(17, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(18, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(19, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(20, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(21, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(22, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(23, 4, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(24, 5, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(25, 5, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(26, 5, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(27, 5, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(28, 5, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(29, 5, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL),
(30, 6, '', '_all_', NULL, '2014-12-31 20:29:08', '2014-12-31 20:29:08', NULL);

CREATE TABLE IF NOT EXISTS `common_comment` (
`id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `author_name` varchar(255) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `author_website` varchar(255) DEFAULT NULL,
  `author_ip_address` varchar(255) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `publish` smallint(6) DEFAULT NULL,
  `rating` smallint(6) DEFAULT '0',
  `relation_subject` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `common_comment` (`id`, `parent`, `node_id`, `title`, `content`, `author_name`, `author_email`, `author_website`, `author_ip_address`, `customer_id`, `created`, `publish`, `rating`, `relation_subject`) VALUES
(0, NULL, 0, 'Base', 'n/a', 'n/a', 'noemail@noemail.com', 'n/a', 'n/a', 0, '2008-08-06 21:25:04', 0, 0, NULL);

CREATE TABLE IF NOT EXISTS `common_configuration` (
`id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL DEFAULT '0',
  `object` varchar(255) DEFAULT NULL,
  `property` varchar(255) DEFAULT NULL,
  `value` longtext,
  `description` longtext,
  `apply_to_children` smallint(6) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

INSERT INTO `common_configuration` (`id`, `node_id`, `object`, `property`, `value`, `description`, `apply_to_children`) VALUES
(1, 0, 'global', 'title', 'basetestdevlaposacouk', '', 0),
(2, 0, 'global', 'author_content', 'White Label, http://www.example.com/', '', 0),
(3, 0, 'global', 'credit', '', '', 0),
(4, 0, 'global', 'html_title_suffix', '- White Label', '', 0),
(5, 0, 'global', 'locale', 'en_GB.UTF-8', '', 0),
(6, 0, 'global', 'default_currency', 'GBP', '', 0),
(7, 0, 'global', 'admin_email', 'test@onxshop.com', '', 0),
(8, 0, 'global', 'css', '@import url(/css/v1.css?1);', '', 0),
(9, 0, 'global', 'google_analytics', '', '', 0),
(10, 0, 'global', 'google_adwords', '', '', 0),
(11, 0, 'global', 'display_content_side', '0', '', 0),
(12, 0, 'global', 'extra_head', '', '', 0),
(13, 0, 'global', 'extra_body_top', '', '', 0),
(14, 0, 'global', 'extra_body_bottom', '', '', 0),
(15, 0, 'global', 'display_secondary_navigation', '0', '', 0),
(16, 0, 'global', 'display_content_foot', '0', '', 0);

CREATE TABLE IF NOT EXISTS `common_email` (
`id` int(11) NOT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `name_from` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` longtext,
  `template` varchar(255) DEFAULT NULL,
  `email_recipient` varchar(255) DEFAULT NULL,
  `name_recipient` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_file` (
`id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `node_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `content` longtext,
  `other_data` longtext,
  `link_to_node_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_image` (
`id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `node_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `content` longtext,
  `other_data` longtext,
  `link_to_node_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_node` (
`id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `node_group` varchar(255) NOT NULL,
  `node_controller` varchar(255) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `parent_container` smallint(6) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `strapline` longtext,
  `content` longtext,
  `description` longtext,
  `keywords` longtext,
  `page_title` varchar(255) DEFAULT NULL,
  `head` longtext,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `publish` int(11) NOT NULL DEFAULT '0',
  `display_in_menu` smallint(6) NOT NULL DEFAULT '1',
  `author` int(11) NOT NULL,
  `uri_title` varchar(255) DEFAULT NULL,
  `display_permission` smallint(6) NOT NULL DEFAULT '0',
  `other_data` longtext,
  `css_class` varchar(255) NOT NULL DEFAULT '',
  `layout_style` varchar(255) NOT NULL DEFAULT '',
  `component` longtext,
  `relations` longtext,
  `display_title` smallint(6) DEFAULT NULL,
  `display_secondary_navigation` smallint(6) DEFAULT NULL,
  `require_login` smallint(6) DEFAULT NULL,
  `display_breadcrumb` smallint(6) NOT NULL DEFAULT '0',
  `browser_title` varchar(255) NOT NULL DEFAULT '',
  `link_to_node_id` int(11) NOT NULL DEFAULT '1',
  `require_ssl` smallint(6) NOT NULL DEFAULT '0',
  `display_permission_group_acl` longtext,
  `share_counter` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) DEFAULT NULL,
  `custom_fields` text
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=latin1;

INSERT INTO `common_node` (`id`, `title`, `node_group`, `node_controller`, `parent`, `parent_container`, `priority`, `strapline`, `content`, `description`, `keywords`, `page_title`, `head`, `created`, `modified`, `publish`, `display_in_menu`, `author`, `uri_title`, `display_permission`, `other_data`, `css_class`, `layout_style`, `component`, `relations`, `display_title`, `display_secondary_navigation`, `require_login`, `display_breadcrumb`, `browser_title`, `link_to_node_id`, `require_ssl`, `display_permission_group_acl`, `share_counter`, `customer_id`, `custom_fields`) VALUES
(0, 'Root', 'site', 'default', NULL, 0, 0, '', '', '', '', '', '', '2008-08-06 21:24:09', '2008-08-06 21:24:09', 1, 1, 0, '', 0, '', '', '', NULL, NULL, NULL, NULL, NULL, 0, '', 0, 0, NULL, 0, NULL, NULL),
(1, 'Primary navigation', 'container', 'default', 0, 0, 10, NULL, 'N;', '', '', NULL, '', '2006-09-29 18:20:29', '2008-08-24 22:57:58', 1, 1, 1000, '', 0, 'N;', '', '', 'N;', 'N;', 1, NULL, NULL, 0, '', 0, 0, NULL, 0, NULL, NULL),
(3, 'Special', 'container', 'default', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2006-09-30 09:55:36', '2006-09-30 09:55:36', 1, 0, 1000, NULL, 0, NULL, '', '', NULL, NULL, NULL, NULL, NULL, 0, '', 0, 0, NULL, 0, NULL, NULL),
(5, 'Home', 'page', 'default', 1, 0, 0, '', NULL, '', NULL, '', '', '2017-06-27 17:23:27', '2017-06-27 17:26:17', 1, 1, 0, '', 0, NULL, '', 'fibonacci-2-1', 'a:1:{s:13:"allow_comment";i:0;}', NULL, 0, 0, 0, 0, '', 0, 0, '', 0, 0, NULL),
(9, 'Password reset', 'page', 'default', 3, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2006-09-30 10:35:15', '2006-09-30 10:35:15', 1, 1, 1000, NULL, 0, NULL, '', 'fibonacci-2-1', NULL, NULL, NULL, NULL, NULL, 0, '', 0, 1, NULL, 0, NULL, NULL),
(14, '404', 'page', 'default', 3, 0, 0, '', NULL, '', '', '', '', '2006-09-30 11:56:37', '2008-08-16 13:06:19', 1, 1, 1000, '', 0, 'N;', '', 'fibonacci-2-1', 'N;', 'N;', 1, 0, NULL, 0, '', 0, 0, NULL, 0, NULL, NULL),
(57, 'Password reset component', 'content', 'component', 9, 0, 0, NULL, '', '', '', NULL, '', '2006-09-30 15:30:31', '2008-08-24 18:26:03', 1, 1, 1000, '', 0, 'N;', '', '', 'a:3:{s:8:"template";s:26:"client/password_reset.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}', 'N;', 0, NULL, NULL, 0, '', 0, 0, NULL, 0, NULL, NULL),
(78, '404 error', 'content', 'rte', 14, 0, 0, NULL, '<p><strong>We have recently restructured this website, you might find what you are looking for by going via the <a href="/">home page</a>.</strong></p>\r\n<p><strong>If you believe you have found a broken link please <a href="/page/20">let us know</a>.</strong></p>\r\n<div class="line">\r\n<hr />\r\n</div>\r\n<p><strong>Please try the following:</strong></p>\r\n<ul>\r\n<li>If you typed the page address in the Address bar, make sure that it is spelled correctly. </li>\r\n<li>Click the <a href="javascript:history.go(-1)">Back</a> button to try another link. </li>\r\n</ul>\r\n<p>HTTP 404 : Page not found</p>', '', '', NULL, '', '2006-09-30 16:37:05', '2008-08-24 18:28:28', 1, 1, 1000, '', 0, 'N;', '', '', 'N;', 'N;', 1, NULL, NULL, 0, '', 0, 0, NULL, 0, NULL, NULL),
(94, 'Bin', 'page', 'default', 0, 0, 0, '', NULL, '', '', '', '', '2014-12-07 00:00:00', '2014-12-07 00:00:00', 0, 0, 1000, '', 0, NULL, '', '', NULL, NULL, 1, 0, 0, 0, '', 0, 0, '', 0, NULL, NULL);

CREATE TABLE IF NOT EXISTS `common_node_taxonomy` (
`id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `taxonomy_tree_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_print_article` (
`id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `node_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `authors` longtext,
  `issue_number` int(11) DEFAULT NULL,
  `page_from` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `other` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_revision` (
`id` int(11) NOT NULL,
  `object` varchar(255) NOT NULL,
  `node_id` int(11) NOT NULL,
  `content` longtext,
  `status` smallint(6) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_scheduler` (
`id` int(11) NOT NULL,
  `node_id` int(11) DEFAULT NULL,
  `node_type` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `parameters` longtext,
  `scheduled_time` datetime DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `lock_token` int(11) DEFAULT NULL,
  `result` longtext,
  `start_time` datetime DEFAULT NULL,
  `completed_time` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_session` (
`id` int(11) NOT NULL,
  `session_id` varchar(32) DEFAULT NULL,
  `session_data` longtext,
  `customer_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `php_auth_user` varchar(255) DEFAULT NULL,
  `http_referer` longtext,
  `http_user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_session_archive` (
`id` int(11) NOT NULL,
  `session_id` varchar(32) DEFAULT NULL,
  `session_data` longtext,
  `customer_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `php_auth_user` varchar(255) DEFAULT NULL,
  `http_referer` longtext,
  `http_user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_taxonomy_label` (
`id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `publish` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO `common_taxonomy_label` (`id`, `title`, `description`, `priority`, `publish`) VALUES
(0, 'Root', '', 0, 1),
(1, 'Brands', '', 0, 1),
(2, 'Products categories', '', 0, 1),
(3, 'Blog categories', '', 0, 1);

CREATE TABLE IF NOT EXISTS `common_taxonomy_label_image` (
`id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `node_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `content` longtext,
  `other_data` longtext,
  `link_to_node_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `common_taxonomy_tree` (
`id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `priority` smallint(6) NOT NULL DEFAULT '0',
  `publish` smallint(6) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `common_taxonomy_tree` (`id`, `label_id`, `parent`, `priority`, `publish`) VALUES
(1, 1, NULL, 0, 1),
(2, 2, NULL, 0, 1),
(3, 3, NULL, 0, 1);

CREATE TABLE IF NOT EXISTS `common_uri_mapping` (
`id` int(11) NOT NULL,
  `node_id` int(11) DEFAULT NULL,
  `public_uri` longtext,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO `common_uri_mapping` (`id`, `node_id`, `public_uri`, `type`) VALUES
(1, 5, '/home', 'generic'),
(2, 9, '/password-reset', 'generic'),
(3, 14, '/404', 'generic'),
(4, 94, '/bin', 'generic');

CREATE TABLE IF NOT EXISTS `common_watchdog` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `watched_item_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `publish` smallint(6) DEFAULT NULL,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `education_survey` (
`id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `priority` smallint(6) DEFAULT '0',
  `publish` smallint(6) DEFAULT '0',
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `education_survey_entry` (
`id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `relation_subject` longtext,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `publish` smallint(6) DEFAULT '0',
  `ip_address` varchar(255) DEFAULT NULL,
  `session_id` varchar(32) DEFAULT NULL,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `education_survey_entry_answer` (
`id` int(11) NOT NULL,
  `survey_entry_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_answer_id` int(11) DEFAULT NULL,
  `value` longtext,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `publish` smallint(6) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `education_survey_image` (
`id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `node_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext,
  `priority` int(11) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `content` longtext,
  `other_data` longtext,
  `link_to_node_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `education_survey_question` (
`id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `step` smallint(6) DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `mandatory` smallint(6) DEFAULT '1',
  `type` varchar(255) NOT NULL,
  `priority` smallint(6) DEFAULT '0',
  `publish` smallint(6) DEFAULT '1',
  `weight` float NOT NULL DEFAULT '1',
  `content` longtext,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `education_survey_question_answer` (
`id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `description` longtext,
  `is_correct` smallint(6) DEFAULT NULL,
  `points` smallint(6) DEFAULT NULL,
  `priority` smallint(6) DEFAULT '0',
  `publish` smallint(6) DEFAULT '1',
  `content` longtext,
  `other_data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `international_country` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `iso_code2` char(2) DEFAULT NULL,
  `iso_code3` char(3) DEFAULT NULL,
  `eu_status` tinyint(4) DEFAULT NULL,
  `currency_code` char(3) DEFAULT NULL,
  `publish` smallint(6) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=latin1;

INSERT INTO `international_country` (`id`, `name`, `iso_code2`, `iso_code3`, `eu_status`, `currency_code`, `publish`) VALUES
(1, 'Afghanistan', 'AF', 'AFG', 0, NULL, 1),
(2, 'Albania', 'AL', 'ALB', 0, NULL, 1),
(3, 'Algeria', 'DZ', 'DZA', 0, NULL, 1),
(4, 'American Samoa', 'AS', 'ASM', 0, NULL, 1),
(5, 'Andorra', 'AD', 'AND', 0, NULL, 1),
(6, 'Angola', 'AO', 'AGO', 0, NULL, 1),
(7, 'Anguilla', 'AI', 'AIA', 0, NULL, 1),
(8, 'Antarctica', 'AQ', 'ATA', 0, NULL, 1),
(9, 'Antigua and Barbuda', 'AG', 'ATG', 0, NULL, 1),
(10, 'Argentina', 'AR', 'ARG', 0, NULL, 1),
(11, 'Armenia', 'AM', 'ARM', 0, NULL, 1),
(12, 'Aruba', 'AW', 'ABW', 0, NULL, 1),
(13, 'Australia', 'AU', 'AUS', 0, NULL, 1),
(14, 'Austria', 'AT', 'AUT', 1, NULL, 1),
(15, 'Azerbaijan', 'AZ', 'AZE', 0, NULL, 1),
(16, 'Bahamas', 'BS', 'BHS', 0, NULL, 1),
(17, 'Bahrain', 'BH', 'BHR', 0, NULL, 1),
(18, 'Bangladesh', 'BD', 'BGD', 0, NULL, 1),
(19, 'Barbados', 'BB', 'BRB', 0, NULL, 1),
(20, 'Belarus', 'BY', 'BLR', 0, NULL, 1),
(21, 'Belgium', 'BE', 'BEL', 1, NULL, 1),
(22, 'Belize', 'BZ', 'BLZ', 0, NULL, 1),
(23, 'Benin', 'BJ', 'BEN', 0, NULL, 1),
(24, 'Bermuda', 'BM', 'BMU', 0, NULL, 1),
(25, 'Bhutan', 'BT', 'BTN', 0, NULL, 1),
(26, 'Bolivia', 'BO', 'BOL', 0, NULL, 1),
(27, 'Bosnia and Herzegowina', 'BA', 'BIH', 0, NULL, 1),
(28, 'Botswana', 'BW', 'BWA', 0, NULL, 1),
(29, 'Bouvet Island', 'BV', 'BVT', 0, NULL, 1),
(30, 'Brazil', 'BR', 'BRA', 0, NULL, 1),
(31, 'British Indian Ocean Territory', 'IO', 'IOT', 0, NULL, 1),
(32, 'Brunei Darussalam', 'BN', 'BRN', 0, NULL, 1),
(33, 'Bulgaria', 'BG', 'BGR', 1, NULL, 1),
(34, 'Burkina Faso', 'BF', 'BFA', 0, NULL, 1),
(35, 'Burundi', 'BI', 'BDI', 0, NULL, 1),
(36, 'Cambodia', 'KH', 'KHM', 0, NULL, 1),
(37, 'Cameroon', 'CM', 'CMR', 0, NULL, 1),
(38, 'Canada', 'CA', 'CAN', 0, NULL, 1),
(39, 'Cape Verde', 'CV', 'CPV', 0, NULL, 1),
(40, 'Cayman Islands', 'KY', 'CYM', 0, NULL, 1),
(41, 'Central African Republic', 'CF', 'CAF', 0, NULL, 1),
(42, 'Chad', 'TD', 'TCD', 0, NULL, 1),
(43, 'Chile', 'CL', 'CHL', 0, NULL, 1),
(44, 'China', 'CN', 'CHN', 0, NULL, 1),
(45, 'Christmas Island', 'CX', 'CXR', 0, NULL, 1),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', 0, NULL, 1),
(47, 'Colombia', 'CO', 'COL', 0, NULL, 1),
(48, 'Comoros', 'KM', 'COM', 0, NULL, 1),
(49, 'Congo', 'CG', 'COG', 0, NULL, 1),
(50, 'Cook Islands', 'CK', 'COK', 0, NULL, 1),
(51, 'Costa Rica', 'CR', 'CRI', 0, NULL, 1),
(52, 'Cote D''Ivoire', 'CI', 'CIV', 0, NULL, 1),
(53, 'Croatia', 'HR', 'HRV', 0, NULL, 1),
(54, 'Cuba', 'CU', 'CUB', 0, NULL, 1),
(55, 'Cyprus', 'CY', 'CYP', 1, NULL, 1),
(56, 'Czech Republic', 'CZ', 'CZE', 1, NULL, 1),
(57, 'Denmark', 'DK', 'DNK', 1, NULL, 1),
(58, 'Djibouti', 'DJ', 'DJI', 0, NULL, 1),
(59, 'Dominica', 'DM', 'DMA', 0, NULL, 1),
(60, 'Dominican Republic', 'DO', 'DOM', 0, NULL, 1),
(61, 'East Timor', 'TP', 'TMP', 0, NULL, 1),
(62, 'Ecuador', 'EC', 'ECU', 0, NULL, 1),
(63, 'Egypt', 'EG', 'EGY', 0, NULL, 1),
(64, 'El Salvador', 'SV', 'SLV', 0, NULL, 1),
(65, 'Equatorial Guinea', 'GQ', 'GNQ', 0, NULL, 1),
(66, 'Eritrea', 'ER', 'ERI', 0, NULL, 1),
(67, 'Estonia', 'EE', 'EST', 1, NULL, 1),
(68, 'Ethiopia', 'ET', 'ETH', 0, NULL, 1),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', 0, NULL, 1),
(70, 'Faroe Islands', 'FO', 'FRO', 0, NULL, 1),
(71, 'Fiji', 'FJ', 'FJI', 0, NULL, 1),
(72, 'Finland', 'FI', 'FIN', 1, NULL, 1),
(73, 'France', 'FR', 'FRA', 1, NULL, 1),
(74, 'Madeira', 'XM', 'MDR', 0, NULL, 1),
(75, 'French Guiana', 'GF', 'GUF', 0, NULL, 1),
(76, 'French Polynesia', 'PF', 'PYF', 0, NULL, 1),
(77, 'French Southern Territories', 'TF', 'ATF', 0, NULL, 1),
(78, 'Gabon', 'GA', 'GAB', 0, NULL, 1),
(79, 'Gambia', 'GM', 'GMB', 0, NULL, 1),
(80, 'Georgia', 'GE', 'GEO', 0, NULL, 1),
(81, 'Germany', 'DE', 'DEU', 1, NULL, 1),
(82, 'Ghana', 'GH', 'GHA', 0, NULL, 1),
(83, 'Gibraltar', 'GI', 'GIB', 0, NULL, 1),
(84, 'Greece', 'GR', 'GRC', 1, NULL, 1),
(85, 'Greenland', 'GL', 'GRL', 0, NULL, 1),
(86, 'Grenada', 'GD', 'GRD', 0, NULL, 1),
(87, 'Guadeloupe', 'GP', 'GLP', 0, NULL, 1),
(88, 'Guam', 'GU', 'GUM', 0, NULL, 1),
(89, 'Guatemala', 'GT', 'GTM', 0, NULL, 1),
(90, 'Guinea', 'GN', 'GIN', 0, NULL, 1),
(91, 'Guinea-bissau', 'GW', 'GNB', 0, NULL, 1),
(92, 'Guyana', 'GY', 'GUY', 0, NULL, 1),
(93, 'Haiti', 'HT', 'HTI', 0, NULL, 1),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', 0, NULL, 1),
(95, 'Honduras', 'HN', 'HND', 0, NULL, 1),
(96, 'Hong Kong', 'HK', 'HKG', 0, NULL, 1),
(97, 'Hungary', 'HU', 'HUN', 1, NULL, 1),
(98, 'Iceland', 'IS', 'ISL', 0, NULL, 1),
(99, 'India', 'IN', 'IND', 0, NULL, 1),
(100, 'Indonesia', 'ID', 'IDN', 0, NULL, 1),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', 0, NULL, 1),
(102, 'Iraq', 'IQ', 'IRQ', 0, NULL, 1),
(103, 'Ireland', 'IE', 'IRL', 1, NULL, 1),
(104, 'Israel', 'IL', 'ISR', 0, NULL, 1),
(105, 'Italy', 'IT', 'ITA', 1, NULL, 1),
(106, 'Jamaica', 'JM', 'JAM', 0, NULL, 1),
(107, 'Japan', 'JP', 'JPN', 0, NULL, 1),
(108, 'Jordan', 'JO', 'JOR', 0, NULL, 1),
(109, 'Kazakhstan', 'KZ', 'KAZ', 0, NULL, 1),
(110, 'Kenya', 'KE', 'KEN', 0, NULL, 1),
(111, 'Kiribati', 'KI', 'KIR', 0, NULL, 1),
(112, 'Korea, Democratic People''s Republic of', 'KP', 'PRK', 0, NULL, 1),
(113, 'Korea, Republic of', 'KR', 'KOR', 0, NULL, 1),
(114, 'Kuwait', 'KW', 'KWT', 0, NULL, 1),
(115, 'Kyrgyzstan', 'KG', 'KGZ', 0, NULL, 1),
(116, 'Lao People''s Democratic Republic', 'LA', 'LAO', 0, NULL, 1),
(117, 'Latvia', 'LV', 'LVA', 1, NULL, 1),
(118, 'Lebanon', 'LB', 'LBN', 0, NULL, 1),
(119, 'Lesotho', 'LS', 'LSO', 0, NULL, 1),
(120, 'Liberia', 'LR', 'LBR', 0, NULL, 1),
(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', 0, NULL, 1),
(122, 'Liechtenstein', 'LI', 'LIE', 0, NULL, 1),
(123, 'Lithuania', 'LT', 'LTU', 1, NULL, 1),
(124, 'Luxembourg', 'LU', 'LUX', 1, NULL, 1),
(125, 'Macau', 'MO', 'MAC', 0, NULL, 1),
(126, 'Macedonia', 'MK', 'MKD', 0, NULL, 1),
(127, 'Madagascar', 'MG', 'MDG', 0, NULL, 1),
(128, 'Malawi', 'MW', 'MWI', 0, NULL, 1),
(129, 'Malaysia', 'MY', 'MYS', 0, NULL, 1),
(130, 'Maldives', 'MV', 'MDV', 0, NULL, 1),
(131, 'Mali', 'ML', 'MLI', 0, NULL, 1),
(132, 'Malta', 'MT', 'MLT', 1, NULL, 1),
(133, 'Marshall Islands', 'MH', 'MHL', 0, NULL, 1),
(134, 'Martinique', 'MQ', 'MTQ', 0, NULL, 1),
(135, 'Mauritania', 'MR', 'MRT', 0, NULL, 1),
(136, 'Mauritius', 'MU', 'MUS', 0, NULL, 1),
(137, 'Mayotte', 'YT', 'MYT', 0, NULL, 1),
(138, 'Mexico', 'MX', 'MEX', 0, NULL, 1),
(139, 'Micronesia', 'FM', 'FSM', 0, NULL, 1),
(140, 'Moldova', 'MD', 'MDA', 0, NULL, 1),
(141, 'Monaco', 'MC', 'MCO', 0, NULL, 1),
(142, 'Mongolia', 'MN', 'MNG', 0, NULL, 1),
(143, 'Montserrat', 'MS', 'MSR', 0, NULL, 1),
(144, 'Morocco', 'MA', 'MAR', 0, NULL, 1),
(145, 'Mozambique', 'MZ', 'MOZ', 0, NULL, 1),
(146, 'Myanmar', 'MM', 'MMR', 0, NULL, 1),
(147, 'Namibia', 'NA', 'NAM', 0, NULL, 1),
(148, 'Nauru', 'NR', 'NRU', 0, NULL, 1),
(149, 'Nepal', 'NP', 'NPL', 0, NULL, 1),
(150, 'Netherlands', 'NL', 'NLD', 1, NULL, 1),
(151, 'Netherlands Antilles', 'AN', 'ANT', 0, NULL, 1),
(152, 'New Caledonia', 'NC', 'NCL', 0, NULL, 1),
(153, 'New Zealand', 'NZ', 'NZL', 0, NULL, 1),
(154, 'Nicaragua', 'NI', 'NIC', 0, NULL, 1),
(155, 'Niger', 'NE', 'NER', 0, NULL, 1),
(156, 'Nigeria', 'NG', 'NGA', 0, NULL, 1),
(157, 'Niue', 'NU', 'NIU', 0, NULL, 1),
(158, 'Norfolk Island', 'NF', 'NFK', 0, NULL, 1),
(159, 'Northern Mariana Islands', 'MP', 'MNP', 0, NULL, 1),
(160, 'Norway', 'NO', 'NOR', 0, NULL, 1),
(161, 'Oman', 'OM', 'OMN', 0, NULL, 1),
(162, 'Pakistan', 'PK', 'PAK', 0, NULL, 1),
(163, 'Palau', 'PW', 'PLW', 0, NULL, 1),
(164, 'Panama', 'PA', 'PAN', 0, NULL, 1),
(165, 'Papua New Guinea', 'PG', 'PNG', 0, NULL, 1),
(166, 'Paraguay', 'PY', 'PRY', 0, NULL, 1),
(167, 'Peru', 'PE', 'PER', 0, NULL, 1),
(168, 'Philippines', 'PH', 'PHL', 0, NULL, 1),
(169, 'Pitcairn', 'PN', 'PCN', 0, NULL, 1),
(170, 'Poland', 'PL', 'POL', 1, NULL, 1),
(171, 'Portugal', 'PT', 'PRT', 1, NULL, 1),
(172, 'Puerto Rico', 'PR', 'PRI', 0, NULL, 1),
(173, 'Qatar', 'QA', 'QAT', 0, NULL, 1),
(174, 'Reunion', 'RE', 'REU', 0, NULL, 1),
(175, 'Romania', 'RO', 'ROM', 1, NULL, 1),
(176, 'Russia', 'RU', 'RUS', 0, NULL, 1),
(177, 'Rwanda', 'RW', 'RWA', 0, NULL, 1),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA', 0, NULL, 1),
(179, 'Saint Lucia', 'LC', 'LCA', 0, NULL, 1),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', 0, NULL, 1),
(181, 'Samoa', 'WS', 'WSM', 0, NULL, 1),
(182, 'San Marino', 'SM', 'SMR', 0, NULL, 1),
(183, 'Sao Tome and Principe', 'ST', 'STP', 0, NULL, 1),
(184, 'Saudi Arabia', 'SA', 'SAU', 0, NULL, 1),
(185, 'Senegal', 'SN', 'SEN', 0, NULL, 1),
(186, 'Seychelles', 'SC', 'SYC', 0, NULL, 1),
(187, 'Sierra Leone', 'SL', 'SLE', 0, NULL, 1),
(188, 'Singapore', 'SG', 'SGP', 0, NULL, 1),
(189, 'Slovakia (Slovak Republic)', 'SK', 'SVK', 1, NULL, 1),
(190, 'Slovenia', 'SI', 'SVN', 1, NULL, 1),
(191, 'Solomon Islands', 'SB', 'SLB', 0, NULL, 1),
(192, 'Somalia', 'SO', 'SOM', 0, NULL, 1),
(193, 'South Africa', 'ZA', 'ZAF', 0, NULL, 1),
(194, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', 0, NULL, 1),
(195, 'Spain', 'ES', 'ESP', 1, NULL, 1),
(196, 'Sri Lanka', 'LK', 'LKA', 0, NULL, 1),
(197, 'St. Helena', 'SH', 'SHN', 0, NULL, 1),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM', 0, NULL, 1),
(199, 'Sudan', 'SD', 'SDN', 0, NULL, 1),
(200, 'Suriname', 'SR', 'SUR', 0, NULL, 1),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', 0, NULL, 1),
(202, 'Swaziland', 'SZ', 'SWZ', 0, NULL, 1),
(203, 'Sweden', 'SE', 'SWE', 1, NULL, 1),
(204, 'Switzerland', 'CH', 'CHE', 0, NULL, 1),
(205, 'Syrian Arab Republic', 'SY', 'SYR', 0, NULL, 1),
(206, 'Taiwan', 'TW', 'TWN', 0, NULL, 1),
(207, 'Tajikistan', 'TJ', 'TJK', 0, NULL, 1),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA', 0, NULL, 1),
(209, 'Thailand', 'TH', 'THA', 0, NULL, 1),
(210, 'Togo', 'TG', 'TGO', 0, NULL, 1),
(211, 'Tokelau', 'TK', 'TKL', 0, NULL, 1),
(212, 'Tonga', 'TO', 'TON', 0, NULL, 1),
(213, 'Trinidad and Tobago', 'TT', 'TTO', 0, NULL, 1),
(214, 'Tunisia', 'TN', 'TUN', 0, NULL, 1),
(215, 'Turkey', 'TR', 'TUR', 0, NULL, 1),
(216, 'Turkmenistan', 'TM', 'TKM', 0, NULL, 1),
(217, 'Turks and Caicos Islands', 'TC', 'TCA', 0, NULL, 1),
(218, 'Tuvalu', 'TV', 'TUV', 0, NULL, 1),
(219, 'Uganda', 'UG', 'UGA', 0, NULL, 1),
(220, 'Ukraine', 'UA', 'UKR', 0, NULL, 1),
(221, 'United Arab Emirates', 'AE', 'ARE', 0, NULL, 1),
(222, 'United Kingdom', 'GB', 'GBR', 1, NULL, 1),
(223, 'United States', 'US', 'USA', 0, NULL, 1),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI', 0, NULL, 1),
(225, 'Uruguay', 'UY', 'URY', 0, NULL, 1),
(226, 'Uzbekistan', 'UZ', 'UZB', 0, NULL, 1),
(227, 'Vanuatu', 'VU', 'VUT', 0, NULL, 1),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT', 0, NULL, 1),
(229, 'Venezuela', 'VE', 'VEN', 0, NULL, 1),
(230, 'Viet Nam', 'VN', 'VNM', 0, NULL, 1),
(231, 'Virgin Islands (British)', 'VG', 'VGB', 0, NULL, 1),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', 0, NULL, 1),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF', 0, NULL, 1),
(234, 'Western Sahara', 'EH', 'ESH', 0, NULL, 1),
(235, 'Yemen', 'YE', 'YEM', 0, NULL, 1),
(236, 'Yugoslavia', 'YU', 'YUG', 0, NULL, 1),
(237, 'Zaire', 'ZR', 'ZAR', 0, NULL, 1),
(238, 'Zambia', 'ZM', 'ZMB', 0, NULL, 1),
(239, 'Zimbabwe', 'ZW', 'ZWE', 0, NULL, 1),
(240, 'Montenegro', 'ME', 'MNE', 0, NULL, 1),
(241, 'Serbia', 'RS', 'SRB', 0, NULL, 1);

CREATE TABLE IF NOT EXISTS `international_translation` (
`id` int(11) NOT NULL,
  `locale` varchar(20) NOT NULL,
  `original_string` longtext NOT NULL,
  `translated_string` longtext NOT NULL,
  `context` varchar(63) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `client_action`
 ADD PRIMARY KEY (`id`), ADD KEY `client_action_customer_id_key` (`customer_id`), ADD KEY `client_action_network_key` (`network`), ADD KEY `client_action_node_id_fkey` (`node_id`);

ALTER TABLE `client_address`
 ADD PRIMARY KEY (`id`), ADD KEY `client_address_country_id_idx` (`country_id`), ADD KEY `client_address_customer_id_idx` (`customer_id`);

ALTER TABLE `client_company`
 ADD PRIMARY KEY (`id`), ADD KEY `client_company_customer_id_idx` (`customer_id`);

ALTER TABLE `client_customer`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `client_customer_email_key` (`email`,`deleted_date`);

ALTER TABLE `client_customer_group`
 ADD PRIMARY KEY (`id`), ADD KEY `client_customer_group_customer_id_key` (`customer_id`), ADD KEY `client_customer_group_group_id_key` (`group_id`);

ALTER TABLE `client_customer_image`
 ADD PRIMARY KEY (`id`), ADD KEY `client_customer_image_node_id_key` (`node_id`), ADD KEY `client_customer_image_customer_id_fkey` (`customer_id`);

ALTER TABLE `client_customer_role`
 ADD PRIMARY KEY (`id`), ADD KEY `client_customer_role_customer_id_key` (`customer_id`), ADD KEY `client_customer_role_role_id_key` (`role_id`);

ALTER TABLE `client_customer_taxonomy`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `client_customer_taxonomy_node_id_key` (`node_id`,`taxonomy_tree_id`), ADD KEY `client_customer_taxonomy_node_id_key1` (`node_id`), ADD KEY `client_customer_taxonomy_taxonomy_tree_id_key` (`taxonomy_tree_id`);

ALTER TABLE `client_customer_token`
 ADD PRIMARY KEY (`id`), ADD KEY `client_customer_token_key` (`token`), ADD KEY `client_customer_token_publish_key` (`publish`), ADD KEY `client_customer_token_customer_id_fkey` (`customer_id`);

ALTER TABLE `client_group`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `client_role`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `client_role_permission`
 ADD PRIMARY KEY (`id`), ADD KEY `client_role_permission_role_id_key` (`role_id`);

ALTER TABLE `common_comment`
 ADD PRIMARY KEY (`id`), ADD KEY `common_comment_costomer_id_id_idx` (`customer_id`), ADD KEY `common_comment_node_id_idx` (`node_id`), ADD KEY `common_comment_node_id_key1` (`node_id`), ADD KEY `common_comment_parent_idx` (`parent`);

ALTER TABLE `common_configuration`
 ADD PRIMARY KEY (`id`), ADD KEY `common_configuration_node_id_fkey` (`node_id`);

ALTER TABLE `common_email`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `common_file`
 ADD PRIMARY KEY (`id`), ADD KEY `common_file_node_id_idx` (`node_id`), ADD KEY `common_file_customer_id_fkey` (`customer_id`);

ALTER TABLE `common_image`
 ADD PRIMARY KEY (`id`), ADD KEY `common_image_node_id_idx` (`node_id`), ADD KEY `common_image_customer_id_fkey` (`customer_id`);

ALTER TABLE `common_node`
 ADD PRIMARY KEY (`id`), ADD KEY `common_node_display_in_idx` (`display_in_menu`), ADD KEY `common_node_node_controller_idx` (`node_controller`), ADD KEY `common_node_node_type_idx` (`node_group`), ADD KEY `common_node_parent_idx` (`parent`), ADD KEY `common_node_publish_idx` (`publish`), ADD KEY `common_node_customer_id_fkey` (`customer_id`), ADD KEY `common_node_link_to_node_id_fkey` (`link_to_node_id`);

ALTER TABLE `common_node_taxonomy`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `node_node_id_taxonomy_tree_id_key` (`node_id`,`taxonomy_tree_id`), ADD KEY `common_node_taxonomy_node_id_idx` (`node_id`), ADD KEY `common_node_taxonomy_taxonomy_tree_id_idx` (`taxonomy_tree_id`);

ALTER TABLE `common_print_article`
 ADD PRIMARY KEY (`id`), ADD KEY `common_print_article_node_id_idx` (`node_id`);

ALTER TABLE `common_revision`
 ADD PRIMARY KEY (`id`), ADD KEY `common_revision_combined_idx` (`object`,`node_id`), ADD KEY `common_revision_customer_id_fkey` (`customer_id`);

ALTER TABLE `common_scheduler`
 ADD PRIMARY KEY (`id`), ADD KEY `common_scheduler_lock_token_key` (`lock_token`), ADD KEY `common_scheduler_node_id_key` (`node_id`), ADD KEY `common_scheduler_scheduled_time_key` (`scheduled_time`), ADD KEY `common_scheduler_status_key` (`status`);

ALTER TABLE `common_session`
 ADD PRIMARY KEY (`id`), ADD KEY `common_session_modified_idx` (`modified`), ADD KEY `common_session_session_id_idx` (`session_id`), ADD KEY `common_session_customer_id_fkey` (`customer_id`);

ALTER TABLE `common_session_archive`
 ADD PRIMARY KEY (`id`), ADD KEY `common_session_archive_customer_id_fkey` (`customer_id`);

ALTER TABLE `common_taxonomy_label`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `common_taxonomy_label_image`
 ADD PRIMARY KEY (`id`), ADD KEY `common_taxonomy_label_image_customer_id_fkey` (`customer_id`), ADD KEY `common_taxonomy_label_image_node_id_fkey` (`node_id`);

ALTER TABLE `common_taxonomy_tree`
 ADD PRIMARY KEY (`id`), ADD KEY `common_taxonomy_tree_label_id_idx` (`label_id`), ADD KEY `common_taxonomy_tree_parent_idx` (`parent`);

ALTER TABLE `common_uri_mapping`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `common_uri_mapping_public_uri_key` (`public_uri`(255)), ADD KEY `common_uri_mapping_node_id_idx` (`node_id`);

ALTER TABLE `common_watchdog`
 ADD PRIMARY KEY (`id`), ADD KEY `common_watchdog_combined_idx` (`name`,`watched_item_id`,`publish`), ADD KEY `common_watchdog_customer_id_fkey` (`customer_id`);

ALTER TABLE `education_survey`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `education_survey_entry`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `education_survey_entry_survey_id_key` (`survey_id`,`customer_id`,`relation_subject`(255)), ADD KEY `education_survey_entry_customer_id_fkey` (`customer_id`);

ALTER TABLE `education_survey_entry_answer`
 ADD PRIMARY KEY (`id`), ADD KEY `education_survey_entry_answer_question_answer_id_fkey` (`question_answer_id`), ADD KEY `education_survey_entry_answer_question_id_fkey` (`question_id`), ADD KEY `education_survey_entry_answer_survey_entry_id_fkey` (`survey_entry_id`);

ALTER TABLE `education_survey_image`
 ADD PRIMARY KEY (`id`), ADD KEY `education_survey_image_node_id_key` (`node_id`), ADD KEY `education_survey_image_customer_id_fkey` (`customer_id`);

ALTER TABLE `education_survey_question`
 ADD PRIMARY KEY (`id`), ADD KEY `education_survey_question_parent_fkey` (`parent`), ADD KEY `education_survey_question_survey_id_fkey` (`survey_id`);

ALTER TABLE `education_survey_question_answer`
 ADD PRIMARY KEY (`id`), ADD KEY `education_survey_question_answer_question_id_fkey` (`question_id`);

ALTER TABLE `international_country`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `international_translation`
 ADD PRIMARY KEY (`id`), ADD KEY `international_translation_locale_idx` (`locale`), ADD KEY `international_translation_node_id_idx` (`node_id`);


ALTER TABLE `client_action`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_address`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_company`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_customer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `client_customer_group`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_customer_image`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_customer_role`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_customer_taxonomy`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_customer_token`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_group`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `client_role`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
ALTER TABLE `client_role_permission`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
ALTER TABLE `common_comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_configuration`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
ALTER TABLE `common_email`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_file`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_image`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_node`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=108;
ALTER TABLE `common_node_taxonomy`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_print_article`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_revision`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_scheduler`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_session`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_session_archive`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_taxonomy_label`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
ALTER TABLE `common_taxonomy_label_image`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `common_taxonomy_tree`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
ALTER TABLE `common_uri_mapping`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
ALTER TABLE `common_watchdog`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `education_survey`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `education_survey_entry`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `education_survey_entry_answer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `education_survey_image`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `education_survey_question`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `education_survey_question_answer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `international_country`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=242;
ALTER TABLE `international_translation`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `client_action`
ADD CONSTRAINT `client_action_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `client_action_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_address`
ADD CONSTRAINT `client_address_country_id_fkey` FOREIGN KEY (`country_id`) REFERENCES `international_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `client_address_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_company`
ADD CONSTRAINT `client_company_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_customer_group`
ADD CONSTRAINT `client_customer_group_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `client_customer_group_group_id_fkey` FOREIGN KEY (`group_id`) REFERENCES `client_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_customer_image`
ADD CONSTRAINT `client_customer_image_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `client_customer_image_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_customer_role`
ADD CONSTRAINT `client_customer_role_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `client_customer_role_role_id_fkey` FOREIGN KEY (`role_id`) REFERENCES `client_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_customer_taxonomy`
ADD CONSTRAINT `client_customer_taxonomy_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `client_customer` (`id`),
ADD CONSTRAINT `client_customer_taxonomy_taxonomy_tree_id_fkey` FOREIGN KEY (`taxonomy_tree_id`) REFERENCES `common_taxonomy_tree` (`id`);

ALTER TABLE `client_customer_token`
ADD CONSTRAINT `client_customer_token_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `client_role_permission`
ADD CONSTRAINT `client_role_permission_role_id_fkey` FOREIGN KEY (`role_id`) REFERENCES `client_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_comment`
ADD CONSTRAINT `common_comment_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `common_comment_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `common_comment_parent_fkey` FOREIGN KEY (`parent`) REFERENCES `common_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_configuration`
ADD CONSTRAINT `common_configuration_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_file`
ADD CONSTRAINT `common_file_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `common_file_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_image`
ADD CONSTRAINT `common_image_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `common_image_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_node`
ADD CONSTRAINT `common_node_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `common_node_link_to_node_id_fkey` FOREIGN KEY (`link_to_node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `common_node_parent_fkey` FOREIGN KEY (`parent`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_node_taxonomy`
ADD CONSTRAINT `common_node_taxonomy_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `common_node_taxonomy_taxonomy_tree_id_fkey` FOREIGN KEY (`taxonomy_tree_id`) REFERENCES `common_taxonomy_tree` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_print_article`
ADD CONSTRAINT `common_print_article_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_revision`
ADD CONSTRAINT `common_revision_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE;

ALTER TABLE `common_session`
ADD CONSTRAINT `common_session_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_session_archive`
ADD CONSTRAINT `common_session_archive_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_taxonomy_label_image`
ADD CONSTRAINT `common_taxonomy_label_image_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `common_taxonomy_label_image_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_taxonomy_label` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_taxonomy_tree`
ADD CONSTRAINT `common_taxonomy_tree_label_id_fkey` FOREIGN KEY (`label_id`) REFERENCES `common_taxonomy_label` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `common_taxonomy_tree_parent_fkey` FOREIGN KEY (`parent`) REFERENCES `common_taxonomy_tree` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_uri_mapping`
ADD CONSTRAINT `common_uri_mapping_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `common_watchdog`
ADD CONSTRAINT `common_watchdog_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE;

ALTER TABLE `education_survey_entry`
ADD CONSTRAINT `education_survey_entry_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `education_survey_entry_survey_id_fkey` FOREIGN KEY (`survey_id`) REFERENCES `education_survey` (`id`) ON UPDATE CASCADE;

ALTER TABLE `education_survey_entry_answer`
ADD CONSTRAINT `education_survey_entry_answer_question_answer_id_fkey` FOREIGN KEY (`question_answer_id`) REFERENCES `education_survey_question_answer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `education_survey_entry_answer_question_id_fkey` FOREIGN KEY (`question_id`) REFERENCES `education_survey_question` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `education_survey_entry_answer_survey_entry_id_fkey` FOREIGN KEY (`survey_entry_id`) REFERENCES `education_survey_entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `education_survey_image`
ADD CONSTRAINT `education_survey_image_customer_id_fkey` FOREIGN KEY (`customer_id`) REFERENCES `client_customer` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `education_survey_image_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `education_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `education_survey_question`
ADD CONSTRAINT `education_survey_question_parent_fkey` FOREIGN KEY (`parent`) REFERENCES `education_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `education_survey_question_survey_id_fkey` FOREIGN KEY (`survey_id`) REFERENCES `education_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `education_survey_question_answer`
ADD CONSTRAINT `education_survey_question_answer_question_id_fkey` FOREIGN KEY (`question_id`) REFERENCES `education_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `international_translation`
ADD CONSTRAINT `international_translation_node_id_fkey` FOREIGN KEY (`node_id`) REFERENCES `common_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
