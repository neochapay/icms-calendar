CREATE TABLE IF NOT EXISTS `cms_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `type` varchar(128) NOT NULL,
  `category_id` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `content` longtext NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

INSERT INTO cms_comment_targets (target, component, title)
                VALUES ('calendar', 'calendar', 'Календарь');
CREATE TABLE IF NOT EXISTS `cms_events_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `bg` text NOT NULL,
  `tx` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `cms_events_signup` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COLLATE=cp1251_bin;


CREATE TABLE IF NOT EXISTS `cms_fotolib` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`type` text NOT NULL,
`photo_id` int(11) NOT NULL,
`name` text NOT NULL,
`time` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;