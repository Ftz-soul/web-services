-- Crea il database
CREATE DATABASE IF NOT EXISTS catalogo;
USE catalogo;
 
-- Crea la tabella libri
CREATE TABLE IF NOT EXISTS libri (
    id INT PRIMARY KEY,
    titolo VARCHAR(255) NOT NULL,
    autore VARCHAR(255) NOT NULL,
    anno INT NOT NULL
);
 
-- Dati di esempio
INSERT INTO libri (id, titolo, autore, anno) VALUES
(1,'Il nome della rosa','Umberto Eco', 1980),
(2,'Il Signore degli Anelli','J.R.R. Tolkien',1954),
(3,'Il Piccolo Principe','Antoine de SaintExupery', 1943);