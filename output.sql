-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: localhost    Database: courselab
-- ------------------------------------------------------
-- Server version	8.0.31

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Web Development','2025-01-18 23:22:39'),(2,'Data Science','2025-01-18 23:22:39'),(3,'Digital Marketing','2025-01-18 23:22:39'),(4,'Business & Entrepreneurship','2025-01-18 23:22:39'),(5,'Personal Development','2025-01-18 23:22:40');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificates` (
  `certificate_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `certificate_data` longblob,
  `issued_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`certificate_id`),
  KEY `student_id` (`student_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `course_details`
--

DROP TABLE IF EXISTS `course_details`;
/*!50001 DROP VIEW IF EXISTS `course_details`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `course_details` AS SELECT 
 1 AS `course_id`,
 1 AS `title`,
 1 AS `description`,
 1 AS `category_id`,
 1 AS `category_name`,
 1 AS `banner_image`,
 1 AS `course_created_at`,
 1 AS `content`,
 1 AS `content_type`,
 1 AS `teacher_id`,
 1 AS `teacher_first_name`,
 1 AS `teacher_last_name`,
 1 AS `teacher_profile_image`,
 1 AS `student_id`,
 1 AS `student_first_name`,
 1 AS `student_last_name`,
 1 AS `student_profile_image`,
 1 AS `enrollment_date`,
 1 AS `completion_status`,
 1 AS `review_id`,
 1 AS `comment`,
 1 AS `rating`,
 1 AS `reviewed_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `course_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `teacher_id` int DEFAULT NULL,
  `category` int DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text,
  `content_type` enum('video','text') DEFAULT 'text',
  PRIMARY KEY (`course_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `fk_category` (`category`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (2,'this is a test chapter','some random description',2,1,NULL,'2025-01-19 15:05:27','<p style=\"text-align:center;\"><strong>udhqsqd</strong></p><p style=\"text-align:center;\">&nbsp;</p><p style=\"text-align:center;\">&nbsp;</p><p style=\"text-align:center;\"><strong>this is total shit&nbsp;</strong></p><p style=\"text-align:center;\">&nbsp;</p><figure class=\"media\"><oembed url=\"https://www.youtube.com/watch?v=I2szngGhiCc\"></oembed></figure>','text'),(3,'this is test course','course course course till i have no more courses to have lol',2,3,NULL,'2025-01-19 15:26:45','<h2 style=\"text-align:center;\"><i><strong>ok some contect ajdkqsjdgqhhdqhsjdjqsdj</strong></i></h2><ul style=\"list-style-type:circle;\"><li>sqdosqjldjqndq<ol><li>sqdjnqkdnqkjsd</li></ol></li></ul><h2>dkqsdksqlkdqs</h2><ol><li>sdnqskjdqksjd</li></ol><ul style=\"list-style-type:circle;\"><li>lol content have something of no use lol</li><li>dsqjdkjqjd</li><li>skqkjdqkjbs</li><li>sdqqksjdnkqs</li></ul>','text'),(4,'course test','random description ',2,4,NULL,'2025-01-19 15:51:51','../up/text_course/1737301911_course_test.html','text'),(5,'another course created','random description sqd kqdsqd',2,5,NULL,'2025-01-19 16:29:36','../up/text_course/1737304176_another_course_created.html','text'),(6,'new retarded test to check if this works ','random description sqd kqdsqd',2,5,'../up/1737304292_banner_new_retarded_test_to_check_if_this_works_.jpg','2025-01-19 16:31:32','../up/text_course/1737304292_new_retarded_test_to_check_if_this_works_.html','text'),(7,'Flying cats','lol idk if this will work just fine lol',2,4,'../up/1737319110_banner_Flying_cats.png','2025-01-19 17:50:24','../up/text_course/1737309024_Flying_cats.html','text'),(8,'asdasdasd','dsasdasd',2,1,NULL,'2025-01-20 01:45:10','../up/videos/RAIN DROPS ON THE WINDOW  4K WALLPAPER.mp4','video'),(9,'this is another test','ok lets test this out',2,3,'../up/1737369202_banner_this_is_another_test.png','2025-01-20 01:46:56','../up/videos/vid55.mp4','video'),(10,'OSAMA IS AWESOME','some random description idk for what but it is a description right lol',2,2,'../up/1737368602_banner_OSAMA_IS_AWESOME.png','2025-01-20 10:23:22','../up/videos/SPOILER_mao.mov','video');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coursetags`
--

DROP TABLE IF EXISTS `coursetags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coursetags` (
  `course_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`course_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `coursetags_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  CONSTRAINT `coursetags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coursetags`
--

LOCK TABLES `coursetags` WRITE;
/*!40000 ALTER TABLE `coursetags` DISABLE KEYS */;
INSERT INTO `coursetags` VALUES (5,1),(6,1),(2,2),(4,3),(5,3),(6,3),(7,3),(8,3),(10,3),(3,4),(4,4),(5,4),(6,4),(7,4),(5,5),(6,5),(7,5),(10,5);
/*!40000 ALTER TABLE `coursetags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `enrollment_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `enrollment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completion_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`enrollment_id`),
  KEY `student_id` (`student_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (3,1,9,'2025-01-21 03:06:36',0);
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `comment` text,
  `rating` int DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `student_id` (`student_id`,`course_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (7,' how about dying'),(2,'Advanced'),(1,'Beginner Friendly'),(4,'Certificate Included'),(3,'Project Based'),(5,'Self Paced'),(6,'this is awseome');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `profile_image` varchar(255) DEFAULT '../up/defaultPFP.jpg',
  `banner_image` varchar(255) DEFAULT '../up/defaultBanner.jpg',
  `bio` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `isBanned` enum('yes','no') DEFAULT 'no',
  `isActive` enum('yes','no') DEFAULT 'yes',
  `status` enum('active','suspended','deleted') DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'osama2code79@gmail.com','$2y$12$IHxYyi2Qm2M89x9KWkIFo.jKb3QPyM7NnN3fFYlNDTgIOs4NSUYYW','jamal','benoujja','student','../up/osamapfp.png','../up/Ren_amamiya_.jpg','this is the new bio','2025-01-17 14:03:17','no','yes','active'),(2,'ouchad98@gmail.com','$2y$12$wCT23jxSXtV8sVuWLjxcwOpniEPiwtutnyVT76cx7hTkvCGL5/S5u','houssni','ouchad','teacher','../up/default.jpg','../up/Use_Case.png','there exists amazing things which you can do','2025-01-18 23:32:02','no','yes','active'),(3,'dd@gmail.com','$2y$12$VJkvj.Kfb2zzU9SE2n0in.3hdf5ySbFOiGwQFkCmpMlnCH.9z714C','mohhamed','daddssi','teacher','../up/defaultPFP.jpg','../up/defaultBanner.jpg',NULL,'2025-01-21 12:44:35','no','yes','active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `course_details`
--

/*!50001 DROP VIEW IF EXISTS `course_details`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `course_details` AS select `c`.`course_id` AS `course_id`,`c`.`title` AS `title`,`c`.`description` AS `description`,`c`.`category` AS `category_id`,`cat`.`category_name` AS `category_name`,`c`.`banner_image` AS `banner_image`,`c`.`created_at` AS `course_created_at`,`c`.`content` AS `content`,`c`.`content_type` AS `content_type`,`u`.`user_id` AS `teacher_id`,`u`.`first_name` AS `teacher_first_name`,`u`.`last_name` AS `teacher_last_name`,`u`.`profile_image` AS `teacher_profile_image`,`e`.`student_id` AS `student_id`,`u_student`.`first_name` AS `student_first_name`,`u_student`.`last_name` AS `student_last_name`,`u_student`.`profile_image` AS `student_profile_image`,`e`.`enrollment_date` AS `enrollment_date`,`e`.`completion_status` AS `completion_status`,`r`.`review_id` AS `review_id`,`r`.`comment` AS `comment`,`r`.`rating` AS `rating`,`r`.`reviewed_at` AS `reviewed_at` from (((((`courses` `c` join `users` `u` on((`c`.`teacher_id` = `u`.`user_id`))) left join `categories` `cat` on((`c`.`category` = `cat`.`category_id`))) left join `enrollments` `e` on((`c`.`course_id` = `e`.`course_id`))) left join `users` `u_student` on((`e`.`student_id` = `u_student`.`user_id`))) left join `reviews` `r` on(((`c`.`course_id` = `r`.`course_id`) and (`e`.`student_id` = `r`.`student_id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-21 13:48:07
