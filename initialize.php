<?php
error_reporting(0);
$sidebar = true;
$isAdmin=false;
$pagetittle = "Inicjalizacja bazy danych";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!-- Code Here -->
<?php

$username="admin";
$password="admin";
$salt = "aaa";
$passencrypted = sha1($password.$salt);

$query2 = "INSERT INTO users (username, password, salt, is_admin) VALUES ('$username', '$passencrypted', '$salt', '1');";

$query = "-- MySQL Script generated by MySQL Workbench
-- Thu Mar 10 15:43:24 2016
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema fajrantdb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema fajrantdb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `fajrantdb` DEFAULT CHARACTER SET latin1 ;
USE `fajrantdb` ;

-- -----------------------------------------------------
-- Table `fajrantdb`.`jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`jobs` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`jobs` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `job` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 33
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`vacancies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`vacancies` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`vacancies` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `nominator` INT(11) NULL DEFAULT NULL,
  `denominator` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`users` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`users` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `username` VARCHAR(255) NULL DEFAULT NULL,
  `password` VARCHAR(255) NULL DEFAULT NULL,
  `name` VARCHAR(55) NULL DEFAULT NULL,
  `surname` VARCHAR(55) NULL DEFAULT NULL,
  `email` VARCHAR(55) NULL DEFAULT NULL,
  `salt` VARCHAR(55) NULL DEFAULT NULL,
  `is_admin` TINYINT(1) NULL DEFAULT '0',
  `job_id` INT(11) UNSIGNED NULL,
  `vacancy_id` INT(11) UNSIGNED NULL,
  `contract_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_users_jobs_idx` (`job_id` ASC),
  INDEX `fk_users_vacancies1_idx` (`vacancy_id` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_users_jobs`
    FOREIGN KEY (`job_id`)
    REFERENCES `fajrantdb`.`jobs` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_vacancies1`
    FOREIGN KEY (`vacancy_id`)
    REFERENCES `fajrantdb`.`vacancies` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 42
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`breaks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`breaks` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`breaks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NULL,
  `available` TINYINT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_breaks_users1_idx` (`user_id` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_breaks_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `fajrantdb`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 67
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`calendar`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`calendar` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`calendar` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `day_of_the_week` INT(11) NULL DEFAULT NULL,
  `day` INT(11) NULL DEFAULT NULL,
  `month` INT(11) NULL DEFAULT NULL,
  `year` INT(11) NULL DEFAULT NULL,
  `holiday` TINYINT(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 673
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`messages` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`messages` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_sender` INT(11) UNSIGNED NULL,
  `id_receiver` INT(11) UNSIGNED NULL,
  `topic` TEXT NULL DEFAULT NULL,
  `message` TEXT NULL DEFAULT NULL,
  `unreaded` TINYINT(1) NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`requests`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`requests` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`requests` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NULL,
  `calendar_id` INT(11) UNSIGNED NULL,
  `time_start` TIME NULL DEFAULT NULL,
  `time_end` TIME NULL DEFAULT NULL,
  `absence` INT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_requests_users1_idx` (`user_id` ASC),
  INDEX `fk_requests_calendar1_idx` (`calendar_id` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_requests_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `fajrantdb`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_requests_calendar1`
    FOREIGN KEY (`calendar_id`)
    REFERENCES `fajrantdb`.`calendar` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 48
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`settings` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_start` VARCHAR(11) NULL DEFAULT NULL,
  `company_end` VARCHAR(11) NULL DEFAULT NULL,
  `breaks_per_time` INT(11) NULL DEFAULT NULL,
  `how_many_breaks` INT(11) NULL DEFAULT NULL,
  `extra_breaks` INT(11) NULL DEFAULT NULL,
  `clear_breaks_day` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `fajrantdb`.`workdays`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `fajrantdb`.`workdays` ;

CREATE TABLE IF NOT EXISTS `fajrantdb`.`workdays` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NULL,
  `calendar_id` INT(11) UNSIGNED NULL,
  `time_start` TIME NULL DEFAULT NULL,
  `time_end` TIME NULL DEFAULT NULL,
  `absence` INT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_workdays_users1_idx` (`user_id` ASC),
  INDEX `fk_workdays_calendar1_idx` (`calendar_id` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_workdays_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `fajrantdb`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_workdays_calendar1`
    FOREIGN KEY (`calendar_id`)
    REFERENCES `fajrantdb`.`calendar` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2094
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

";

$query = $query . $query2;

//echo $query;

$sql = mysqli_multi_query($conn, $query);


if($sql){
    echo showAlert("success", "Baza danych utworzona prawidłowo");
    echo showAlert("success", "Default user: ".$username.' <br> Password: '.$password);
}
else{
    echo showAlert("danger", mysqli_error($conn));
}

echo showAlert("info", "Po wygenerowaniu danych radzimy w celu bezpieczeństwa usunąć plik initialize.php");
;?>



<!--------------->

<?php include "./layout/sidebar.php";?>
<?php include "./layout/footer.php";?>
