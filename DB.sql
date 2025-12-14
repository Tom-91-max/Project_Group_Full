CREATE DATABASE IF NOT EXISTS aquatic_plant_advisor
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE aquatic_plant_advisor;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS plant_images;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS answers;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS plant_logs;
DROP TABLE IF EXISTS water_logs;
DROP TABLE IF EXISTS tank_plants;
DROP TABLE IF EXISTS tanks;
DROP TABLE IF EXISTS plants;
DROP TABLE IF EXISTS plant_taxa;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user','expert','admin') NOT NULL DEFAULT 'user',
  `status` ENUM('active','blocked') NOT NULL DEFAULT 'active',
  `avatar` VARCHAR(255) NULL,
  `bio` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`),
  KEY `users_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plant_taxa` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `genus` VARCHAR(100) NOT NULL,
  `species` VARCHAR(150) NOT NULL DEFAULT '',
  `family` VARCHAR(150) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plant_taxa_unique` (`genus`,`species`,`family`),
  KEY `plant_taxa_genus_index` (`genus`),
  KEY `plant_taxa_family_index` (`family`),
  KEY `plant_taxa_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `taxon_id` BIGINT UNSIGNED NULL,
  `origin` VARCHAR(100) NULL,
  `ph_min` DECIMAL(3,2) NULL,
  `ph_max` DECIMAL(3,2) NULL,
  `temp_min` DECIMAL(4,1) NULL,
  `temp_max` DECIMAL(4,1) NULL,
  `light_level` ENUM('low','medium','high') NULL,
  `difficulty` ENUM('easy','medium','hard') NULL,
  `growth_rate` ENUM('very_slow','slow','moderate','fast') NULL,
  `placement` ENUM('foreground','midground','background') NULL,
  `height_min_cm` DECIMAL(5,1) NULL,
  `height_max_cm` DECIMAL(5,1) NULL,
  `water_hardness` ENUM('very_soft','soft','medium','hard','very_hard') NULL,
  `co2_min_mg` DECIMAL(6,2) NULL,
  `co2_max_mg` DECIMAL(6,2) NULL,
  `propagation` TEXT NULL,
  `image_path` VARCHAR(255) NULL,
  `care_guide` TEXT NULL,
  `extra` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plants_name_index` (`name`),
  KEY `plants_taxon_id_index` (`taxon_id`),
  KEY `plants_light_level_index` (`light_level`),
  KEY `plants_difficulty_index` (`difficulty`),
  KEY `plants_growth_rate_index` (`growth_rate`),
  KEY `plants_placement_index` (`placement`),
  KEY `plants_origin_index` (`origin`),
  KEY `plants_water_hardness_index` (`water_hardness`),
  KEY `plants_deleted_at_index` (`deleted_at`),
  CONSTRAINT `plants_taxon_id_foreign`
    FOREIGN KEY (`taxon_id`) REFERENCES `plant_taxa` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `plants_ph_range_check`
    CHECK (`ph_min` IS NULL OR `ph_max` IS NULL OR `ph_min` <= `ph_max`),
  CONSTRAINT `plants_temp_range_check`
    CHECK (`temp_min` IS NULL OR `temp_max` IS NULL OR `temp_min` <= `temp_max`),
  CONSTRAINT `plants_co2_range_check`
    CHECK (`co2_min_mg` IS NULL OR `co2_max_mg` IS NULL OR `co2_min_mg` <= `co2_max_mg`),
  CONSTRAINT `plants_height_range_check`
    CHECK (`height_min_cm` IS NULL OR `height_max_cm` IS NULL OR `height_min_cm` <= `height_max_cm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tanks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `size` VARCHAR(50) NULL,
  `volume_liters` DECIMAL(7,2) NULL,
  `substrate` VARCHAR(255) NULL,
  `light` VARCHAR(255) NULL,
  `co2` TINYINT(1) NOT NULL DEFAULT 0,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanks_user_id_index` (`user_id`),
  KEY `tanks_deleted_at_index` (`deleted_at`),
  CONSTRAINT `tanks_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tank_plants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tank_id` BIGINT UNSIGNED NOT NULL,
  `plant_id` BIGINT UNSIGNED NOT NULL,
  `planted_at` DATE NULL,
  `note` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tank_plants_tank_id_index` (`tank_id`),
  KEY `tank_plants_plant_id_index` (`plant_id`),
  KEY `tank_plants_deleted_at_index` (`deleted_at`),
  CONSTRAINT `tank_plants_tank_id_foreign`
    FOREIGN KEY (`tank_id`) REFERENCES `tanks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `tank_plants_plant_id_foreign`
    FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `water_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tank_id` BIGINT UNSIGNED NOT NULL,
  `logged_at` DATETIME NOT NULL,
  `ph` DECIMAL(3,2) NULL,
  `temperature` DECIMAL(4,1) NULL,
  `no3` DECIMAL(6,2) NULL,
  `other_params` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `water_logs_tank_id_logged_at_index` (`tank_id`,`logged_at`),
  KEY `water_logs_deleted_at_index` (`deleted_at`),
  CONSTRAINT `water_logs_tank_id_foreign`
    FOREIGN KEY (`tank_id`) REFERENCES `tanks` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plant_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tank_plant_id` BIGINT UNSIGNED NOT NULL,
  `logged_at` DATE NOT NULL,
  `height` DECIMAL(5,1) NULL,
  `status` VARCHAR(100) NULL,
  `note` TEXT NULL,
  `image_path` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plant_logs_tank_plant_id_logged_at_index` (`tank_plant_id`,`logged_at`),
  KEY `plant_logs_deleted_at_index` (`deleted_at`),
  CONSTRAINT `plant_logs_tank_plant_id_foreign`
    FOREIGN KEY (`tank_plant_id`) REFERENCES `tank_plants` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `questions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `tank_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image_path` VARCHAR(255) NULL,
  `status` ENUM('open','resolved') NOT NULL DEFAULT 'open',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_user_id_index` (`user_id`),
  KEY `questions_tank_id_index` (`tank_id`),
  KEY `questions_status_index` (`status`),
  KEY `questions_deleted_at_index` (`deleted_at`),
  CONSTRAINT `questions_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `questions_tank_id_foreign`
    FOREIGN KEY (`tank_id`) REFERENCES `tanks` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `answers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `is_accepted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `answers_question_id_index` (`question_id`),
  KEY `answers_user_id_index` (`user_id`),
  KEY `answers_is_accepted_index` (`is_accepted`),
  KEY `answers_deleted_at_index` (`deleted_at`),
  CONSTRAINT `answers_question_id_foreign`
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `answers_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `posts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image_path` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_index` (`user_id`),
  KEY `posts_deleted_at_index` (`deleted_at`),
  CONSTRAINT `posts_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_post_id_index` (`post_id`),
  KEY `comments_user_id_index` (`user_id`),
  KEY `comments_deleted_at_index` (`deleted_at`),
  CONSTRAINT `comments_post_id_foreign`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `comments_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plant_images` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plant_id` BIGINT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `feature_vector` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plant_images_plant_id_index` (`plant_id`),
  KEY `plant_images_deleted_at_index` (`deleted_at`),
  CONSTRAINT `plant_images_plant_id_foreign`
    FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
