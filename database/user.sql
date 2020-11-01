CREATE TABLE `test`.`user`
(
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR
(45) NOT NULL,
  `last_name` VARCHAR
(45) NOT NULL,
  `user_type_id` VARCHAR
(45) NOT NULL,
  `password` VARCHAR
(45) NOT NULL,
  `username` VARCHAR
(45) NOT NULL,
  `user_status_id` INT NOT NULL,
  `created_by` VARCHAR
(45) NOT NULL,
  `created_date` DATE NOT NULL,
  PRIMARY KEY
(`user_id`));
