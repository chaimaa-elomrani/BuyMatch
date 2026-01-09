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

class Tickets
{
    private $id;
    private $user_id;
    private $match_id;
    private $category_id;
    private $place_number;
    private $qr_code;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $matchId, $categoryId, $placeNumber, $qrCode)
    {
        $qrCode = uniqid('TICKET-', true);// this will generate a unique qr code by appending a unique identifier to 'TICKET-'
        $stmt = $this->db->prepare("
            INSERT INTO tickets (user_id, match_id, category_id, place_number, qr_code) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $result = $stmt->execute([
            $userId,
            $matchId,
            $categoryId,
            $placeNumber,
            $qrCode
        ]);

        if ($result) {
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


    public function loadById($id)
    {
        $stmt = $this->db->prepare(
            " SELECT r.*, m.lieu, m.date_match, e.nom AS equipe_nom 
        FROM reviews r 
        JOIN matchs m ON r.match_id = m.id 
        JOIN equipes e ON m.equipe = e.id 
        WHERE r.user_id = ? 
        ORDER BY m.date_match DESC"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch(); // why fetch instead of fetchAll , because we are fetching a single review by user id
        if ($data) {
            $this->id = $data['id'];
            $this->user_id = $data['user_id'];
            $this->match_id = $data['match_id'];
            $this->category_id = $data['category_id'];
            $this->place_number = $data['place_number'];
            $this->qr_code = $data['qr_code'];

        }
        return $data;// return the data for further use if needed , we did the condition before to avoid errors

    }


    public function generatePDF()
    {
        // Implement PDF generation logic here using a library like TCPDF or FPDF
        $data = $this->loadById($this->id);
        if (!$data) {
            throw new Exception("Ticket not found.");
        }
        $dompdf = new \Dompdf\Dompdf();
         $html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .ticket { border: 2px solid #000; padding: 20px; width: 600px; margin: 0 auto; }
                    .header { text-align: center; background: #007bff; color: white; padding: 15px; }
                    .info { margin: 20px 0; }
                    .qr-code { text-align: center; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class='ticket'>
                    <div class='header'>
                        <h1>BILLET DE MATCH</h1>
                    </div>
                    <div class='info'>
                        <h2>{$data['equipe_domicile']} vs {$data['equipe_exterieur']}</h2>
                        <p><strong>Date:</strong> " . date('d/m/Y H:i', strtotime($data['date_match'])) . "</p>
                        <p><strong>Lieu:</strong> {$data['lieu']}</p>
                        <p><strong>Catégorie:</strong> {$data['categorie_nom']}</p>
                        <p><strong>Place:</strong> {$data['numero_place']}</p>
                        <p><strong>Prix:</strong> {$data['prix']} DH</p>
                    </div>
                    <div class='qr-code'>
                        <p><strong>Code QR:</strong> {$data['qr_code']}</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }
}
