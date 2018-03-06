
create database payement_api /** Creating Database **/
 
use payement_api /** Selecting Database **/
 
-- DB_SERVER : 127.0.0.1
-- Version du serveur :  10.1.30-MariaDB
-- Version de PHP :  7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

create table users(
   id int(11) primary key auto_increment,
   unique_id varchar(23) not null unique,
   userName varchar(50) not null unique,
   email varchar(100) not null unique,
   encrypted_password varchar(80) not null,
   salt varchar(10) not null,
   created_at datetime,
   updated_at datetime null
); /** Creating Users Table **/

-- Structure de la table `orders`

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `encrypted_password` varchar(80) NOT NULL,
  `salt` varchar(10) DEFAULT NULL,
  `orderNumber` int(11) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `currency` varchar(255) NOT NULL,
  `returnUrl` varchar(255) NOT NULL,
  `failUrl` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `language` varchar(100) NOT NULL,
  `pageView` varchar(255) NOT NULL,
  `clientId` int(11) NOT NULL,
  `jsonParams` varchar(255) NOT NULL,
  `sessionTimeoutSecs` int(11) NOT NULL,
  `expirationDate` datetime NOT NULL,
  `bindingId` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8; /** Creating orders Table **/

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

-- AUTO_INCREMENT pour la table `orders`

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;