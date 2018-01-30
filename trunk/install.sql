/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura para tabla cavesman.lang
CREATE TABLE IF NOT EXISTS `lang` (
  `lang_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `iso` char(2) NOT NULL,
  `browser` char(5) NOT NULL,
  `locale` char(5) NOT NULL,
  `name` char(30) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`lang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla cavesman.lang: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
INSERT INTO `lang` (`lang_id`, `iso`, `browser`, `locale`, `name`, `active`) VALUES
	(1, 'es', 'es-ES', 'es_ES', 'Español (ES)', 1);
INSERT INTO `lang` (`lang_id`, `iso`, `browser`, `locale`, `name`, `active`) VALUES
	(2, 'ca', 'ca-ES', 'ca_ES', 'Català', 1);
INSERT INTO `lang` (`lang_id`, `iso`, `browser`, `locale`, `name`, `active`) VALUES
	(3, 'en', 'en-US', 'en_US', 'English (USA)', 0);
/*!40000 ALTER TABLE `lang` ENABLE KEYS */;

-- Volcando estructura para tabla cavesman.pages
CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_footer` tinyint(1) NOT NULL DEFAULT '0',
  `name` char(50) NOT NULL,
  `description` char(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla cavesman.pages: ~8 rows (aproximadamente)
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(5, 1, 'Condiciones', '', '2016-06-07 16:57:06', '2017-02-03 10:19:09');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(6, 2, 'termes legales', '', '2016-06-07 16:57:50', '2016-09-29 10:24:32');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(7, 2, 'política de privacidad', '', '2016-06-07 19:09:21', '2016-09-16 11:07:47');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(8, 0, 'faq', '', '2016-06-07 19:09:24', '2016-06-07 19:09:24');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(9, 2, 'cookies', '', '2016-06-07 19:09:34', '2016-09-20 19:11:32');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(10, 0, 'empresa', '', '2016-06-07 19:09:51', '2016-06-07 19:09:51');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(11, 1, 'MISIÓN / VISIÓN', '', '2016-09-26 13:07:34', '2016-09-26 13:07:34');
INSERT INTO `pages` (`page_id`, `section_footer`, `name`, `description`, `date_created`, `date_modified`) VALUES
	(12, 1, 'Tarifas', '', '2016-11-07 12:02:35', '2016-11-07 12:04:44');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;

-- Volcando estructura para tabla cavesman.pages_lang
CREATE TABLE IF NOT EXISTS `pages_lang` (
  `page_id` int(10) unsigned NOT NULL,
  `lang_id` tinyint(2) unsigned NOT NULL,
  `seo_title` char(50) NOT NULL,
  `title` char(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`page_id`,`lang_id`),
  KEY `FK_pages_lang_lang` (`lang_id`),
  CONSTRAINT `FK_pages_lang_lang` FOREIGN KEY (`lang_id`) REFERENCES `lang` (`lang_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_pages_lang_pages` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla cavesman.pages_lang: ~24 rows (aproximadamente)
/*!40000 ALTER TABLE `pages_lang` DISABLE KEYS */;
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(5, 1, 'condiciones', 'Condiciones', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(5, 2, 'condicions', 'Condicions', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(5, 3, 'condiciones', 'condiciones', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(6, 1, 'aviso-legal', 'Aviso Legal', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(6, 2, 'termies-legales', 'Termes Legals', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(6, 3, 'terminos-legales', 'terminos legales', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(7, 1, 'política de prvacidad', 'Política de Privacidad', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(7, 2, 'seguridad', 'Política de Privacitat', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(7, 3, 'seguridad', 'seguridad', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(8, 1, 'faq', 'Faq', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(8, 2, 'faq', 'Faq', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(8, 3, 'faq', 'faq', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(9, 1, 'cookies', 'Cookies', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(9, 2, 'cookies', 'Cookies', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(9, 3, 'cookies', 'cookies', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(10, 1, 'empresa', 'Empresa', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(10, 2, 'empresa', 'Empresa', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(10, 3, 'empresa', 'empresa', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(11, 1, 'mision-vision', 'Misión / Visión', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(11, 2, 'misio-visio', 'Missió / Visió', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(11, 3, 'mision-vision', 'MISSIÓ / VISIÓ', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(12, 1, 'tarifas', 'Tarifas', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(12, 2, 'tarifes', 'Tarifes', '');
INSERT INTO `pages_lang` (`page_id`, `lang_id`, `seo_title`, `title`, `description`) VALUES
	(12, 3, 'tarifas', 'Tarifas', '');
/*!40000 ALTER TABLE `pages_lang` ENABLE KEYS */;

-- Volcando estructura para tabla cavesman.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla cavesman.settings: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`setting_id`, `name`, `value`) VALUES
	(1, 'logo_header', '/uploads/logo.png');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Volcando estructura para tabla cavesman.translates
CREATE TABLE IF NOT EXISTS `translates` (
  `translate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `string` text COLLATE utf8_bin NOT NULL,
  `md5` char(50) COLLATE utf8_bin NOT NULL,
  `founded` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`translate_id`),
  UNIQUE KEY `md5` (`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Volcando datos para la tabla cavesman.translates: ~7 rows (aproximadamente)
/*!40000 ALTER TABLE `translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `translates` ENABLE KEYS */;

-- Volcando estructura para tabla cavesman.translates_lang
CREATE TABLE IF NOT EXISTS `translates_lang` (
  `translate_id` int(11) unsigned NOT NULL,
  `lang_id` tinyint(3) unsigned NOT NULL,
  `translate` text CHARACTER SET utf8 NOT NULL,
  `traducido` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`translate_id`,`lang_id`),
  KEY `lang_id` (`lang_id`),
  CONSTRAINT `translates_lang_ibfk_1` FOREIGN KEY (`lang_id`) REFERENCES `lang` (`lang_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `translates_lang_ibfk_2` FOREIGN KEY (`translate_id`) REFERENCES `translates` (`translate_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Volcando datos para la tabla cavesman.translates_lang: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `translates_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `translates_lang` ENABLE KEYS */;

-- Volcando estructura para tabla cavesman.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nts_code` char(50) NOT NULL DEFAULT '',
  `sysuser` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user` char(50) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL DEFAULT '',
  `firstname` char(64) NOT NULL DEFAULT '',
  `lastname` char(64) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `permanent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `permisos` text,
  `date_expire` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla cavesman.users: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`user_id`, `nts_code`, `sysuser`, `user`, `password`, `firstname`, `lastname`, `active`, `permanent`, `permisos`, `date_expire`, `date_created`, `date_modified`) VALUES
	(1, '', 1, 'admin', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'SuperAdmin', '', 1, 0, '1', NULL, '2017-07-31 18:44:49', '2017-07-31 18:44:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Volcando estructura para disparador cavesman.pages_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `pages_after_insert` AFTER INSERT ON `pages` FOR EACH ROW INSERT INTO pages_lang
	SELECT new.page_id, lang_id, REPLACE(new.name, " ", "-"), new.name, '' FROM lang;//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Volcando estructura para disparador cavesman.pages_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `pages_before_insert` BEFORE INSERT ON `pages` FOR EACH ROW SET NEW.date_created = NOW();//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Volcando estructura para disparador cavesman.translates_add
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `translates_add` AFTER INSERT ON `translates` FOR EACH ROW INSERT INTO translates_lang
(translate_id, lang_id, translate)
SELECT new.translate_id, lang_id, new.string FROM lang;//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Volcando estructura para disparador cavesman.users_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `users_after_insert` BEFORE INSERT ON `users` FOR EACH ROW SET NEW.date_created = NOW();//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
