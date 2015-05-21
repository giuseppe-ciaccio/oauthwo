-- MySQL dump 10.13  Distrib 5.1.63, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: oauthwo
-- ------------------------------------------------------
-- Server version	5.1.63-0+squeeze1

--
-- Table structure for table `authorization_codes`
--

DROP TABLE IF EXISTS `authorization_codes`;
CREATE TABLE `authorization_codes` (
  `authorization_code` varchar(1000) NOT NULL,
  `client_id` varchar(22) NOT NULL,
  `resource_owner_id` varchar(500) NOT NULL,
  `scopes` varchar(200) NOT NULL,
  `generation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`authorization_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `client_id` varchar(22) NOT NULL,
  `client_secret` varchar(40) NOT NULL,
  `client_type` enum('web','user-agent','native') NOT NULL,
  `redirect_uri` varchar(500) NOT NULL,
  `client_name` varchar(500) NOT NULL,
  `client_description` longtext,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_secret` (`client_secret`),
  UNIQUE KEY `redirect_uri` (`redirect_uri`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refresh_tokens`
--

DROP TABLE IF EXISTS `refresh_tokens`;
CREATE TABLE `refresh_tokens` (
  `refresh_token` varchar(1000) NOT NULL,
  `client_id` varchar(22) NOT NULL,
  `resource_owner_id` varchar(500) NOT NULL,
  `scopes` varchar(200) NOT NULL,
  `generation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refresh_token`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `resource_server`
--

DROP TABLE IF EXISTS `resource_server`;
CREATE TABLE `resource_server` (
  `resource_server_id` varchar(22) NOT NULL,
  `resource_server_secret` varchar(64) NOT NULL,
  `reference_type` set('cf','mail','nickname') NOT NULL,
  `resource_server_name` varchar(500) NOT NULL,
  `resource_server_endpoint_uri` varchar(1024) NOT NULL,
  PRIMARY KEY (`resource_server_id`),
  UNIQUE KEY `resource_server_secret` (`resource_server_secret`),
  UNIQUE KEY `resource_server_name` (`resource_server_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `scope`
--

DROP TABLE IF EXISTS `scope`;
CREATE TABLE `scope` (
  `resource_server_id` varchar(22) NOT NULL,
  `scope_id` varchar(100) NOT NULL,
  `scope_description` varchar(250) NOT NULL,
  PRIMARY KEY (`resource_server_id`,`scope_id`),
  UNIQUE KEY `scope_id` (`scope_id`),
  CONSTRAINT `scope_ibfk_3` FOREIGN KEY (`resource_server_id`) REFERENCES `resource_server` (`resource_server_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` varchar(500) NOT NULL,
  `user_password` char(32) NOT NULL,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_reference`
--

DROP TABLE IF EXISTS `user_reference`;
CREATE TABLE `user_reference` (
  `user_id` varchar(500) NOT NULL,
  `resource_server_id` varchar(22) NOT NULL,
  `user_reference` varchar(200) NOT NULL,
  PRIMARY KEY (`user_id`,`resource_server_id`),
  KEY `resource_server_id` (`resource_server_id`),
  CONSTRAINT `user_reference_ibfk_1` FOREIGN KEY (`resource_server_id`) REFERENCES `resource_server` (`resource_server_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_reference_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Struttura della tabella `resource_set_registration`
--

DROP TABLE IF EXISTS `resource_set_registration`;
CREATE TABLE IF NOT EXISTS `resource_set_registration` (
  `rset_id` varchar(200) NOT NULL,
  `rset_name` varchar(200) NOT NULL,
  `rset_description` varchar(200) NOT NULL,
  `rset_type` varchar(200) DEFAULT NULL,
  `rset_uri` varchar(200) NOT NULL,
  `rset_scopesuri` varchar(300) NOT NULL,
  PRIMARY KEY (`rset_id`),
  FULLTEXT KEY `rset_name` (`rset_name`,`rset_description`,`rset_uri`),
  FULLTEXT KEY `rset_name_2` (`rset_name`,`rset_description`,`rset_type`,`rset_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Struttura della tabella `rset_scope`
--

DROP TABLE IF EXISTS `rset_scope`;
CREATE TABLE IF NOT EXISTS `rset_scope` (
  `scope_uri` varchar(100) NOT NULL,
  `rset_id` varchar(100) NOT NULL,
  PRIMARY KEY (`scope_uri`,`rset_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabelle riguardanti le deleghe.
--

--
-- Utente delega un altro utente per un insieme di scope,
-- fino a una certa data di scadenza.
--

drop table if exists `delegations`;
create table `delegations`(
        `delegation_id` int not null auto_increment,
        `delegator` varchar(500) not null,
        `delegate` varchar(500) not null,
        `scopes` varchar(1000) not null,
        `expiration_date` datetime,
        `code` bigint not null,
        `state` tinyint default 0,
        primary key(`delegation_id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Lista di ruoli
--

drop table if exists `roles`;
create table `roles`(
        `role_id` int not null auto_increment,
        `role_name` varchar(100) not null,
        `role_uri` varchar(200) not null,
        primary key(`role_id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Ruoli e relativi scope permessi
--

drop table if exists `role_scopes`;
create table `role_scopes`(
        `role_id` int not null references `roles`(`role_id`) on delete cascade on update cascade,
        `scopes` varchar(1000),
        primary key(`role_id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;
