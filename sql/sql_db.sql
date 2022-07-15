DROP DATABASE IF EXISTS weatherstation;
CREATE DATABASE weatherstation;
USE weatherstation;

CREATE TABLE stazione(
	id int NOT NULL PRIMARY KEY,
	descrizione text DEFAULT NULL
);

CREATE TABLE temperatura(
	id_stazione int NOT NULL,
	valore float NOT NULL,
	data datetime NOT NULL,
	FOREIGN KEY (id_stazione) REFERENCES stazione(id),
	PRIMARY KEY (id_stazione, data)
);

CREATE TABLE umidita(
	id_stazione int NOT NULL,
	valore float NOT NULL,
	data datetime NOT NULL,
	FOREIGN KEY (id_stazione) REFERENCES stazione(id),
	PRIMARY KEY (id_stazione, data)
);

CREATE TABLE pressione(
	id_stazione int NOT NULL,
	valore int NOT NULL,
	data datetime NOT NULL,
	FOREIGN KEY (id_stazione) REFERENCES stazione(id),
	PRIMARY KEY (id_stazione, data)
);

CREATE TABLE luce(
	id_stazione int NOT NULL,
	valore int NOT NULL,
	data datetime NOT NULL,
	FOREIGN KEY (id_stazione) REFERENCES stazione(id),
	PRIMARY KEY (id_stazione, data)
);

CREATE TABLE pioggia(
	id_stazione int NOT NULL,
	valore int NOT NULL,
	data datetime NOT NULL,
	FOREIGN KEY (id_stazione) REFERENCES stazione(id),
	PRIMARY KEY (id_stazione, data)
);

CREATE VIEW pioggia_mensile_1 AS 
	SELECT id_stazione, DATE_FORMAT(data, "%Y-%m") as mese, AVG(valore) as media  
	FROM pioggia 
	WHERE id_stazione = 1 
	GROUP BY MONTH(data), YEAR(data);

CREATE VIEW pioggia_mensile_2 AS 
	SELECT id_stazione, DATE_FORMAT(data, "%Y-%m") as mese, AVG(valore) as media  
	FROM pioggia 
	WHERE id_stazione = 2 
	GROUP BY MONTH(data), YEAR(data);
	
CREATE VIEW pioggia_annuale_1 AS 
	SELECT id_stazione, YEAR(data) as anno, AVG(valore) as media 
	FROM pioggia 
	WHERE id_stazione = 1 
	GROUP BY YEAR(data);
	
CREATE VIEW pioggia_annuale_2 AS 
	SELECT id_stazione, YEAR(data) as anno, AVG(valore) as media 
	FROM pioggia 
	WHERE id_stazione = 2 
	GROUP BY YEAR(data);
	
INSERT INTO stazione (id, descrizione)
VALUES(1, "Zheng"), 
(2, "Sassoli");
