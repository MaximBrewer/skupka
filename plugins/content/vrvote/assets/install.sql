
CREATE TABLE IF NOT EXISTS  `#__content_vrvote` (
  `content_id` INT(11) NOT NULL,
  `extra_id` INT(11) NOT NULL,
  `lastip` VARCHAR(50) NOT NULL,
  `rating_count` INT(11) NOT NULL,
  `rating_sum` INT(11) NOT NULL,
  KEY `vrvote_idx` (`content_id`)
 )