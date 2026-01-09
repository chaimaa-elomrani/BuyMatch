<?php

// CREATE TABLE tickets (
//     id INT PRIMARY KEY AUTO_INCREMENT,
//     user_id INT NOT NULL,
//     match_id INT NOT NULL,
//     category_id INT NOT NULL,
//     place_number VARCHAR(10) NOT NULL,
//     qr_code VARCHAR(255) UNIQUE NOT NULL
//     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
//     FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE,
//     FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
//     UNIQUE KEY unique_place (match_id, place_number),
// ) 

class Tickets {
    private $id;
    private $user_id;
    private $match_id;
    private $category_id;
    private $place_number;
    private $qr_code;
    private $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function buyTicket($userId, $matchId, $categoryId, $placeNumber, $qrCode){
        $qrCode = uniqid('TICKET-', true);// this will generate a unique qr code by appending a unique identifier to 'TICKET-'
        $stmt = $this->db->prepare("
            INSERT INTO tickets (user_id, match_id, category_id, place_number, qr_code) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $result =  $stmt->execute([
            $userId,
            $matchId,
            $categoryId,
            $placeNumber,
            $qrCode
        ]);

        if($result){
            $this->id = $this->db->lastInsertId(); // Get the last inserted ID to set the ticket ID
            $this->userId = $userId;
            $this->matchId = $matchId;
            $this->categoryId = $categoryId;
            $this->placeNumber = $placeNumber;
            $this->qrCode = $qrCode;

            $this->sendTicketEmail();
            return $this->id; 
        }
        return false;
    }
}
