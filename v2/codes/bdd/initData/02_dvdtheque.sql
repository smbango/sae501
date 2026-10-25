CREATE DATABASE IF NOT EXISTS dvdtheque;
USE dvdtheque;

-- 1. Création de la table Realisateur en premier (table parente)
CREATE TABLE Realisateur (
    NoRealisateur INTEGER PRIMARY KEY AUTO_INCREMENT,
    Nom VARCHAR(40) NOT NULL,
    Prenom VARCHAR(40) NOT NULL,
    Pays VARCHAR(40) NOT NULL
);

-- 2. Création de la table Film ensuite (table enfant avec clé étrangère)
CREATE TABLE Film (
    NoFilm INTEGER PRIMARY KEY AUTO_INCREMENT,
    Titre VARCHAR(100) NOT NULL,
    DateSortie DATE NOT NULL,
    Genre VARCHAR(30) NOT NULL CHECK (Genre in ('Action','Aventure','Comédie','Drame','Fantastique','Horreur','Policier','Science fiction','Thriller')),
    NoRealisateur INTEGER NOT NULL,
    LienAffiche VARCHAR(100),
    CONSTRAINT fk_NoRealisateur FOREIGN KEY (NoRealisateur) REFERENCES Realisateur (NoRealisateur) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Insertion des réalisateurs
INSERT INTO Realisateur VALUES(1,'Hazanavicius','Michel','France');
INSERT INTO Realisateur VALUES(2,'Baudry','Antonin','France');
INSERT INTO Realisateur VALUES(3,'Scott','Tony','Royaume-Uni');
INSERT INTO Realisateur VALUES(4,'Kosinski','Joseph','États-Unis');
INSERT INTO Realisateur VALUES(5,'Stahelski','Chad','États-Unis');
INSERT INTO Realisateur VALUES(6,'Soderbergh','Steven','États-Unis');
INSERT INTO Realisateur VALUES(7,'Spielberg','Steven','États-Unis');
INSERT INTO Realisateur VALUES(8,'Campbell','Martin','Nouvelle-Zélande');
INSERT INTO Realisateur VALUES(9,'Nolan','Christopher','Royaume-Uni');
INSERT INTO Realisateur VALUES(10,'Fincher','David','États-Unis');

-- Insertion des films
INSERT INTO Film VALUES(1,'OSS 117 : Le Caire, nid d''espions','2006-04-19','Comédie',1,'images/1.jpg');
INSERT INTO Film VALUES(2,'OSS 117 : Rio ne répond plus','2009-04-15','Comédie',1,'images/2.jpg');
INSERT INTO Film VALUES(3,'OSS 117 : Alerte rouge en Afrique noire','2021-08-04','Comédie',1,'images/3.jpg');
INSERT INTO Film VALUES(4,'Le Chant du loup','2019-02-20','Thriller',2,'images/4.jpg');
INSERT INTO Film VALUES(5,'Top Gun','1986-05-16','Action',3,'images/5.jpg');
INSERT INTO Film VALUES(6,'Top Gun: Maverick','2022-05-27','Action',4,'images/6.jpg');
INSERT INTO Film VALUES(7,'John Wick','2014-10-24','Action',5,'images/7.jpg');
INSERT INTO Film VALUES(8,'Ocean''s Eleven','2001-12-07','Policier',6,'images/8.jpg');
INSERT INTO Film VALUES(9,'La Liste de Schindler','1993-12-15','Drame',7,'images/9.jpg');
INSERT INTO Film VALUES(10,'Casino Royale','2006-11-22','Action',8,'images/10.jpg');
INSERT INTO Film VALUES(11,'Interstellar','2014-11-05','Science fiction',9,'images/11.jpg');
INSERT INTO Film VALUES(12,'Fight Club','1999-11-10','Thriller',10,'images/12.jpg');
INSERT INTO Film VALUES(16,'Inception','2010-07-16','Science fiction',9,NULL);
