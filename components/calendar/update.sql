ALTER TABLE  `cms_events` ADD  `parent_id` INT NOT NULL;

ALTER TABLE  `cms_events` CHANGE  `apx`  `category_id` INT NOT NULL;

CREATE TABLE IF NOT EXISTS `cms_fotolib` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`type` text NOT NULL,
`photo_id` int(11) NOT NULL,
`name` text NOT NULL,
`time` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `cms_events_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `bg` text NOT NULL,
  `tx` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;