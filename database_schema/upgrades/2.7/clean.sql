SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `payment_configuration`;
DROP TABLE IF EXISTS `payment_gateway_settings`;
DROP TABLE IF EXISTS `credit_log`;
DROP TABLE IF EXISTS `payment_transaction_log`;
DROP TABLE IF EXISTS `refund_transaction_log`;
DROP TABLE IF EXISTS `terms_of_service`;
DROP TABLE IF EXISTS `resource_images`;
DROP TABLE IF EXISTS `custom_time_blocks`;

SET foreign_key_checks = 1;