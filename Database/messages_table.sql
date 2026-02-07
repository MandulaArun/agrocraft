-- Create messages table for farmer-buyer communication
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `sender_type` varchar(20) NOT NULL COMMENT 'farmer or buyer',
  `receiver_id` int(11) NOT NULL,
  `receiver_type` varchar(20) NOT NULL COMMENT 'farmer or buyer',
  `message_text` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

