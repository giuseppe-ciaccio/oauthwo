-- MySQL dump 10.13  Distrib 5.1.63, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: authserver_zf
-- ------------------------------------------------------
-- Server version	5.1.63-0+squeeze1


USE `authserver_zf`;


LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES ('dichred_clientid1','7aba9979f96d797400ed5e6503f85972','web','https://localhost/client/index/process','Dichiarazione dei redditi client','L\'applicazione che permette di automatizzare (in parte) la compilazione della dichiarazione dei redditi 730.', DEFAULT/*,'2012-05-09 08:20:45'*/);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `resource_server` WRITE;
/*!40000 ALTER TABLE `resource_server` DISABLE KEYS */;
INSERT INTO `resource_server` VALUES 
('comune_rs_id','4ba2f6b2c0c992e46f294170d5fd87e6b87fd1f409405319fd716cd9588c8088','cf','Comune Resource Server','https://localhost/comune/server/data'),
('agenziaentrate_rs_id', '936630ba1ecfb6e37803bd90140a8c93d61d4068d281c44acc428ea44701074c', 'cf', 'Agenzia delle Entrate Server', 'https://localhost/agenziaentrate/server/data'),
('agterritorio_rs_id', 'b2be45a711f322755f04dc06b597941954bbafdb83512c781ce5cd786d350dc5', 'cf', 'Agenzia del Territorio (e Catasto) Server', 'https://localhost/agenziaterritorio/server/data');
/*!40000 ALTER TABLE `resource_server` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `scope` WRITE;
/*!40000 ALTER TABLE `scope` DISABLE KEYS */;
INSERT INTO `scope` VALUES
('comune_rs_id','lettura_dati_anagrafici','Lettura dati anagrafici'),
('comune_rs_id','aggiornamento_dati_anagrafici','Aggiornamento dati anagrafici'),
('comune_rs_id','scrittura_dati_anagrafici','Scrittura dati anagrafici'),
('comune_rs_id','cancellazione_dati_anagrafici','Cancellazione dati anagrafici'),
('agenziaentrate_rs_id', 'lettura_dati_sostituto_imposta', 'Lettura dati del sostituto d\'imposta'),
('agenziaentrate_rs_id', 'lettura_dati_contratti_locazione_fabbricati', 'Lettura dati dei contratti di locazione dei fabbricati'),
('agenziaentrate_rs_id', 'lettura_dati_cud', 'Lettura dati del CUD'),
('agenziaentrate_rs_id', 'lettura_dati_spese_mediche', 'Lettura dati delle spese mediche'),
('agterritorio_rs_id', 'lettura_dati_fabbricati', 'Lettura dati dei fabbricati in possesso');
/*!40000 ALTER TABLE `scope` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
('mario.rossi@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99', DEFAULT),
('artur.tolstenco@gmail.com', '5f4dcc3b5aa765d61d8327deb882cf99', DEFAULT),
('bianchi@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99', DEFAULT);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user_reference` WRITE;
/*!40000 ALTER TABLE `user_reference` DISABLE KEYS */;
INSERT INTO `user_reference` VALUES
('mario.rossi@gmail.com', 'comune_rs_id', 'RSSMRA85T10A562S'),
('mario.rossi@gmail.com', 'agenziaentrate_rs_id', 'RSSMRA85T10A562S'),
('mario.rossi@gmail.com', 'agterritorio_rs_id', 'RSSMRA85T10A562S');
/*!40000 ALTER TABLE `user_reference` ENABLE KEYS */;
UNLOCK TABLES;

insert into `roles` (`role_name`,`role_uri`) values
('ispettore ag. entrate','https://localhost/ruoloispettori/');

insert into `role_scopes` (`role_id`, `scopes`) values
(1, 'lettura_dati_anagrafici lettura_dati_fabbricati lettura_dati_contratti_locazione_fabbricati');
