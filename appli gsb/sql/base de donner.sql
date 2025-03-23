

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS Utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    role ENUM('visiteur', 'comptable', 'administrateur') NOT NULL,
    dateInscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des états des fiches de frais
CREATE TABLE IF NOT EXISTS Etats (
    id VARCHAR(2) PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Table des fiches de frais
CREATE TABLE IF NOT EXISTS FichesFrais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateurId INT NOT NULL,
    mois VARCHAR(6) NOT NULL, -- Format AAAAMM
    montantValide DECIMAL(10, 2) DEFAULT 0.00,
    dateModif DATETIME DEFAULT CURRENT_TIMESTAMP,
    idEtat VARCHAR(2) DEFAULT 'CR', -- Par défaut : Créé
    FOREIGN KEY (utilisateurId) REFERENCES Utilisateurs(id),
    FOREIGN KEY (idEtat) REFERENCES Etats(id)
);

-- Table des éléments forfaitisés
CREATE TABLE IF NOT EXISTS ElementsForfaitises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficheFraisId INT NOT NULL,
    typeForfait VARCHAR(50) NOT NULL, -- Exemple : "Forfait étape", "Frais kilométriques"
    quantite INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (ficheFraisId) REFERENCES FichesFrais(id)
);

-- Table des éléments hors forfait
CREATE TABLE IF NOT EXISTS ElementsHorsForfait (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficheFraisId INT NOT NULL,
    date DATE NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    etat ENUM('Accepté', 'Refusé', 'En attente') DEFAULT 'En attente',
    FOREIGN KEY (ficheFraisId) REFERENCES FichesFrais(id)
);

-- Table des types de frais forfaitisés
CREATE TABLE IF NOT EXISTS FraisForfait (
    id VARCHAR(3) PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL,
    montant DECIMAL(10, 2) NOT NULL
);

-- Insertion des rôles par défaut
INSERT INTO Utilisateurs (nom, prenom, email, password, role) VALUES
('Administrateur', 'Principal', 'admin@example.com', '$2y$10$bjthGbIYNKMwI6K95I/gD.QAngh.X4gMJWRnb6PnBIsUQ7Vrm5i9m', 'administrateur'), -- Mot de passe : admin2024
('Louis', 'Boda', 'louis.boda@example.com', '$2y$10$wZH1TZ/tVpDm4CIqDNGgL.wJQYpO5Hx4Sro7eQl2yadcBIv4Y2wi2', 'visiteur'), -- Mot de passe : visiteur123
('Evan', 'CATTAROSSI', 'evan.cattarossi@example.com', '$2y$10$oai9myf7ozhZbr4XDw5LsOmIKTxqcQ9xxXkEpClW89KsVuaRsUumG', 'comptable'), -- Mot de passe : comptable123
('John', 'Jones', 'john.jones@example.com', '$2y$10$OmCUyM7EafTSejQ1wFLQEO/XvBBdMZNnNoMk7vJKImwnVSNs6PTJ6', 'visiteur'), -- Mot de passe : visiteur234
('George', 'Pierre', 'george.pierre@example.com', '$2y$10$7UC/mForWl/o0DzlW4g1YulIU8.STdR6fhx2K.h8Iknr14LPASbcq', 'visiteur'); -- Mot de passe : visiteur345

-- Insertion des états par défaut
INSERT INTO Etats (id, libelle) VALUES
('CR', 'Créé'),
('RB', 'Remboursé'),
('RF', 'Refusé');

-- Insertion des types de frais forfaitisés par défaut
INSERT INTO FraisForfait (id, libelle, montant) VALUES
('NUI', 'Nuitée Hôtel', 80.00),
('REP', 'Repas Restaurant', 25.00),
('KM', 'Indemnité Kilométrique', 0.62);

-- Exemple d'insertion de fiches de frais
INSERT INTO FichesFrais (utilisateurId, mois, dateModif, idEtat) VALUES
(2, '202311', NOW(), 'CR'),
(3, '202411', NOW(), 'VA');

-- Exemple d'insertion d'éléments forfaitisés
INSERT INTO ElementsForfaitises (ficheFraisId, typeForfait, quantite, montant) VALUES
(1, 'Forfait étape', 2, 100.00),
(1, 'Frais kilométriques', 100, 0.50),
(1, 'Nuitée hôtel', 1, 80.00),
(1, 'Repas restaurant', 2, 25.00);

-- Exemple d'insertion d'éléments hors forfait
INSERT INTO ElementsHorsForfait (ficheFraisId, date, libelle, montant, etat) VALUES
(1, '2023-11-10', 'Taxi pour conférence', 40.00, 'Accepté'),
(1, '2023-11-12', 'Parking aéroport', 15.00, 'Refusé'),
(2, '2024-11-15', 'Dépannage urgent', 100.00, 'En attente');
