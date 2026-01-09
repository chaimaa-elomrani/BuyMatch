-- =============================================
-- Base de données BuyMatch - Version finale avec team1/team2
-- =============================================

DROP DATABASE IF EXISTS buymatch;
CREATE DATABASE buymatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE buymatch;

-- ==================== TABLE USERS ====================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'organizer', 'admin') DEFAULT 'user' NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active' NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==================== TABLE EQUIPES ====================
CREATE TABLE equipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logo VARCHAR(255) NULL
);

-- ==================== TABLE MATCHS ====================
CREATE TABLE matchs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team1_id INT NOT NULL,          -- Première équipe
    team2_id INT NOT NULL,          -- Seconde équipe
    date_match DATETIME NOT NULL,
    lieu VARCHAR(150) NOT NULL,
    duration INT DEFAULT 90,
    capacity INT NOT NULL CHECK (capacity <= 2000),
    statut ENUM('pending', 'validated', 'rejected', 'published') DEFAULT 'pending',
    organizer_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (team1_id) REFERENCES equipes(id) ON DELETE RESTRICT,
    FOREIGN KEY (team2_id) REFERENCES equipes(id) ON DELETE RESTRICT,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    
    CONSTRAINT chk_different_teams CHECK (team1_id != team2_id)
);

-- ==================== TABLE CATEGORIES ====================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prix DECIMAL(8,2) NOT NULL,
    
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE
);

-- Trigger : Maximum 3 catégories par match
DROP TRIGGER IF EXISTS limit_categories;
DELIMITER $$
CREATE TRIGGER limit_categories
BEFORE INSERT ON categories
FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM categories WHERE match_id = NEW.match_id) >= 3 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Maximum 3 catégories autorisées par match';
    END IF;
END$$
DELIMITER ;

-- ==================== TABLE TICKETS ====================
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    category_id INT NOT NULL,
    place_number VARCHAR(10) NOT NULL,
    qr_code VARCHAR(255) UNIQUE NOT NULL,
    purchased_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_place_per_match (match_id, place_number)
);

-- ==================== TABLE REVIEWS ====================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    comment TEXT NOT NULL,
    note TINYINT NULL CHECK (note BETWEEN 1 AND 5),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_review_per_user_match (user_id, match_id)
);

-- ==================== VUE SQL (Stats organisateur) ====================
CREATE VIEW view_organizer_stats AS
SELECT 
    o.id AS organizer_id,
    CONCAT(o.prenom, ' ', o.nom) AS organizer_name,
    m.id AS match_id,
    m.date_match,
    m.lieu,
    CONCAT(t1.nom, ' vs ', t2.nom) AS equipes,
    COUNT(t.id) AS billets_vendus,
    COALESCE(SUM(c.prix), 0) AS chiffre_affaires
FROM users o
JOIN matchs m ON m.organizer_id = o.id
JOIN equipes t1 ON m.team1_id = t1.id
JOIN equipes t2 ON m.team2_id = t2.id
LEFT JOIN tickets t ON t.match_id = m.id
LEFT JOIN categories c ON t.category_id = c.id
WHERE o.role = 'organizer' AND m.statut = 'published'
GROUP BY m.id;

-- ==================== PROCÉDURE STOCKÉE (Stats globales) ====================
DELIMITER $$
CREATE PROCEDURE get_global_stats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM matchs WHERE statut = 'published') AS total_matchs_published,
        (SELECT COUNT(*) FROM tickets) AS total_billets_vendus,
        COALESCE(SUM(c.prix), 0) AS chiffre_affaires_total,
        COALESCE(AVG(r.note), 0) AS note_moyenne_generale
    FROM tickets t
    LEFT JOIN categories c ON t.category_id = c.id
    LEFT JOIN reviews r ON r.match_id IN (SELECT id FROM matchs WHERE statut = 'published');
END$$
DELIMITER ;

-- =============================================
-- Données de test
-- =============================================

INSERT INTO users (nom, prenom, email, password, role) VALUES
('Smith', 'Alice', 'alice@mail.com', '$2y$10$dummyhash', 'user'),
('Johnson', 'Bob', 'bob@mail.com', '$2y$10$dummyhash', 'user'),
('Brown', 'Charlie', 'charlie@mail.com', '$2y$10$dummyhash', 'organizer'),
('Prince', 'Diana', 'diana@mail.com', '$2y$10$dummyhash', 'organizer'),
('Hunt', 'Ethan', 'ethan@mail.com', '$2y$10$dummyhash', 'admin');

INSERT INTO equipes (nom, logo) VALUES
('Red Tigers', 'red_tigers.png'),
('Blue Sharks', 'blue_sharks.png'),
('Golden Eagles', 'golden_eagles.png'),
('Silver Wolves', 'silver_wolves.png'),
('Black Panthers', 'black_panthers.png');

INSERT INTO matchs (team1_id, team2_id, date_match, lieu, capacity, statut, organizer_id) VALUES
(1, 2, '2026-01-15 18:00:00', 'Stade Casablanca', 1800, 'pending', 3),
(3, 4, '2026-01-20 20:00:00', 'Stade Marrakech', 2000, 'published', 4),
(5, 1, '2026-01-25 19:00:00', 'Stade Rabat', 1500, 'validated', 3);

INSERT INTO categories (match_id, nom, prix) VALUES
(1, 'VIP', 150.00),
(1, 'Standard', 80.00),
(1, 'Économie', 40.00),
(2, 'VIP', 200.00),
(2, 'Standard', 100.00);