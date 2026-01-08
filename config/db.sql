CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(200) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'organizer', 'admin') DEFAULT 'user' NOT NULL,

) 

CREATE TABLE equipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logo VARCHAR(255) 
);

CREATE TABLE matchs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipe INT NOT NULL,
    date_match DATETIME NOT NULL,
    lieu VARCHAR(150) NOT NULL,
    duration INT DEFAULT 90,
    capacity INT NOT NULL CHECK (capacity <= 2000),
    statut ENUM('pending', 'valid', 'rejected', 'published') DEFAULT 'pending',
    organizer_id INT NOT NULL,
    FOREIGN KEY (equipe) REFERENCES equipes(id) ON DELETE RESTRICT,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TRIGGER limit_categories
BEFORE INSERT ON categories
FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM categories WHERE match_id = NEW.match_id) >= 3 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Max 3 categories allowed per match';
    END IF;
END;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prix DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE,
  
);

-- ============================================================================
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    category_id INT NOT NULL,
    place_number VARCHAR(10) NOT NULL,
    qr_code VARCHAR(255) UNIQUE NOT NULL
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_place (match_id, place_number),
) 

-- ============================================================================
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    comment TEXT NOT NULL,
    note INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE,
    CONSTRAINT chk_note CHECK (note BETWEEN 1 AND 5),

) 


-- Insertion des utilisateurs de test
-- ==================== users ====================
INSERT INTO users (fullname, email, password, role) VALUES
('Alice Smith', 'alice@mail.com', 'pass123', 'user'),
('Bob Johnson', 'bob@mail.com', 'pass123', 'user'),
('Charlie Brown', 'charlie@mail.com', 'pass123', 'organizer'),
('Diana Prince', 'diana@mail.com', 'pass123', 'organizer'),
('Ethan Hunt', 'ethan@mail.com', 'pass123', 'admin'),
('Fiona Glenanne', 'fiona@mail.com', 'pass123', 'user'),
('George Martin', 'george@mail.com', 'pass123', 'user');

-- ==================== equipes ====================
INSERT INTO equipes (nom, logo) VALUES
('Red Tigers', 'red_tigers.png'),
('Blue Sharks', 'blue_sharks.png'),
('Golden Eagles', 'golden_eagles.png'),
('Silver Wolves', 'silver_wolves.png'),
('Black Panthers', 'black_panthers.png'),
('White Lions', 'white_lions.png'),
('Green Dragons', 'green_dragons.png');

-- ==================== matchs ====================
INSERT INTO matchs (equipe, date_match, lieu, duration, capacity, statut, organizer_id) VALUES
(1, '2026-01-15 18:00:00', 'Stadium A', 90, 1500, 'pending', 3),
(2, '2026-01-16 20:00:00', 'Stadium B', 90, 1200, 'valid', 4),
(3, '2026-01-17 19:30:00', 'Stadium C', 90, 1800, 'published', 3),
(4, '2026-01-18 17:00:00', 'Stadium D', 90, 2000, 'pending', 4),
(5, '2026-01-19 21:00:00', 'Stadium E', 90, 1700, 'valid', 3),
(6, '2026-01-20 16:00:00', 'Stadium F', 90, 1600, 'rejected', 4),
(7, '2026-01-21 19:00:00', 'Stadium G', 90, 1400, 'published', 3);

-- ==================== categories ====================
INSERT INTO categories (match_id, nom, prix) VALUES
(1, 'VIP', 100.00),
(1, 'Standard', 50.00),
(1, 'Economy', 30.00),
(2, 'VIP', 120.00),
(2, 'Standard', 60.00),
(2, 'Economy', 35.00),
(3, 'VIP', 150.00);

-- ==================== tickets ====================
INSERT INTO tickets (user_id, match_id, category_id, place_number, qr_code) VALUES
(1, 1, 1, 'A1', 'QR001'),
(2, 1, 2, 'B1', 'QR002'),
(3, 1, 3, 'C1', 'QR003'),
(4, 2, 4, 'A2', 'QR004'),
(5, 2, 5, 'B2', 'QR005'),
(6, 3, 7, 'A3', 'QR006'),
(7, 3, 7, 'A4', 'QR007');

-- ==================== reviews ====================
INSERT INTO reviews (user_id, match_id, comment, note) VALUES
(1, 1, 'Great match!', 5),
(2, 1, 'Pretty good.', 4),
(3, 2, 'Exciting game!', 5),
(4, 2, 'Could be better.', 3),
(5, 3, 'Loved it!', 5),
(6, 3, 'Not bad.', 4),
(7, 1, 'Amazing experience.', 5);
