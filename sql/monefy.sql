/* Database export results for db monefy */

/* Preserve session variables */
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS=0;

/* Export data */

/* Table structure for budget */
CREATE TABLE `budget` (
  `budget_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `category_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`budget_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/* data for Table budget */
INSERT INTO `budget` VALUES (1,"9300.00",1,1,"2019-04-10 08:43:41","2019-04-10 08:43:41");
INSERT INTO `budget` VALUES (2,"10000.00",7,1,"2019-04-10 08:44:07","2019-04-10 08:44:07");
INSERT INTO `budget` VALUES (3,"10000.00",2,1,"2019-04-10 08:44:25","2019-04-10 08:44:25");
INSERT INTO `budget` VALUES (4,"1300.00",4,1,"2019-04-10 08:44:57","2019-04-10 08:44:57");
INSERT INTO `budget` VALUES (5,"4000.00",3,1,"2019-04-10 08:48:40","2019-04-10 08:48:40");
INSERT INTO `budget` VALUES (6,"2000.00",5,1,"2019-04-10 08:48:54","2019-04-10 08:48:54");
INSERT INTO `budget` VALUES (7,"3000.00",6,1,"2019-04-10 08:49:03","2019-04-10 08:49:03");
INSERT INTO `budget` VALUES (8,"1000.00",8,1,"2019-04-10 09:08:44","2019-04-10 09:08:44");
INSERT INTO `budget` VALUES (9,"1000.00",9,1,"2019-04-10 09:08:53","2019-04-10 09:08:53");

/* Table structure for expense */
CREATE TABLE `expense` (
  `expense_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `recurring_cost_type_id` int(5) NOT NULL,
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/* data for Table expense */
INSERT INTO `expense` VALUES (1,"300.00",1,1,"2019-01-01","2019-04-10 08:41:33","2019-04-10 08:41:33",2);
INSERT INTO `expense` VALUES (2,"6000.00",7,1,"2019-01-03","2019-04-10 08:43:05","2019-04-10 08:43:05",1);

/* Table structure for expense_categories */
CREATE TABLE `expense_categories` (
  `expense_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_category` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`expense_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/* data for Table expense_categories */
INSERT INTO `expense_categories` VALUES (1,"Food");
INSERT INTO `expense_categories` VALUES (2,"Home");
INSERT INTO `expense_categories` VALUES (3,"Shopping");
INSERT INTO `expense_categories` VALUES (4,"Transport");
INSERT INTO `expense_categories` VALUES (5,"Healthcare");
INSERT INTO `expense_categories` VALUES (6,"Education");
INSERT INTO `expense_categories` VALUES (7,"Bills & Fees");
INSERT INTO `expense_categories` VALUES (8,"Entertainment");
INSERT INTO `expense_categories` VALUES (9,"Gifts");
INSERT INTO `expense_categories` VALUES (10,"Travel");

/* Table structure for income */
CREATE TABLE `income` (
  `income_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `income_date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`income_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/* data for Table income */
INSERT INTO `income` VALUES (1,"45000.00",1,"2019-01-01","2019-04-10 08:37:10","2019-04-10 08:37:10",1);

/* Table structure for income_categories */
CREATE TABLE `income_categories` (
  `income_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `income_category` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`income_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/* data for Table income_categories */
INSERT INTO `income_categories` VALUES (1,"Salary");
INSERT INTO `income_categories` VALUES (2,"Business");

/* Table structure for recurring_costs_type */
CREATE TABLE `recurring_costs_type` (
  `recurring_cost_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recurring_cost_type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`recurring_cost_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/* data for Table recurring_costs_type */
INSERT INTO `recurring_costs_type` VALUES (1,"Once");
INSERT INTO `recurring_costs_type` VALUES (2,"Daily");
INSERT INTO `recurring_costs_type` VALUES (3,"Weekly");

/* Table structure for users */
CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/* data for Table users */
INSERT INTO `users` VALUES (1,"sathish","0bbd64a3917b62ac50a4b8ca225438115f497f8b","2019-04-10 08:29:47","2019-04-10 08:29:47");

/* Restore session variables to original values */
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
