CREATE TABLE IF NOT EXISTS `list__name` (
  `id` varchar(12) NOT NULL,
  `de_de` text NOT NULL,
  `en_en` text NOT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
