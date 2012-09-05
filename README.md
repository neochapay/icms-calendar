Установка: Установить как обычный компонент.

Обновление:
- С последней бетты (0.4.beta6):
    ALTER TABLE  `cms_events` CHANGE  `apx`  `category_id` INT NOT NULL;
    CREATE TABLE IF NOT EXISTS `cms_events_category` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` text NOT NULL,
    `bg` text NOT NULL,
    `tx` text NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;