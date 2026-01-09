<?php

// Ensure Composer autoloader is available when this model is used directly
if (!class_exists('\PHPMailer\\PHPMailer\\PHPMailer') || !class_exists('\Dompdf\\Dompdf')) {
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Dompdf\Dompdf;

class Tickets
{
    private $id;
    private $user_id;
    private $match_id;
    private $category_id;
    private $place_number;
    private $qr_code;
    private $db;
    private $matchData;

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
            $this->user_id = $userId;
            $this->match_id = $matchId;
            $this->category_id = $categoryId;
            $this->place_number = $placeNumber;
            $this->qr_code = $qrCode;

            $this->sendTicketEmail();
            return $this->id;
        }
        return false;
    }


    public function loadById($id)
    {
        $stmt = $this->db->prepare("
        SELECT 
            t.*, 
            m.date_match, m.lieu,
            t1.nom AS team1_name,
            t2.nom AS team2_name,
            c.nom AS category_name,
            c.prix,
            u.email AS user_email,
            u.nom AS user_nom,
            u.prenom AS user_prenom
        FROM tickets t
        JOIN matchs m ON t.match_id = m.id
        JOIN equipes t1 ON m.team1_id = t1.id
        JOIN equipes t2 ON m.team2_id = t2.id
        JOIN categories c ON t.category_id = c.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE t.id = ?
    ");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            $this->id = $data['id'];
            $this->user_id = $data['user_id'];
            $this->match_id = $data['match_id'];
            $this->category_id = $data['category_id'];
            $this->place_number = $data['place_number'];
            $this->qr_code = $data['qr_code'];
            $this->matchData = $data;
        }
        return $data;
    }

    public function generatePDF()
    {
        // Implement PDF generation logic here using a library like TCPDF or FPDF
        require_once BASE_PATH . '/vendor/autoload.php';

        $data = $this->loadById($this->id);
        if (!$data) {
            throw new Exception("Ticket not found.");
        }
        $dompdf = new Dompdf();
        $html = "
    <h2>{$this->matchData['team1_name']} vs {$this->matchData['team2_name']}</h2>
    <p><strong>Date:</strong> " . date('d/m/Y H:i', strtotime($this->matchData['date_match'])) . "</p>
    <p><strong>Lieu:</strong> {$this->matchData['lieu']}</p>
    <p><strong>Catégorie:</strong> {$this->matchData['category_name']}</p>
    <p><strong>Prix:</strong> {$this->matchData['prix']} DH</p>
    <p><strong>Place:</strong> {$this->place_number}</p>
    <p><strong>QR Code:</strong> {$this->qr_code}</p>
";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }


    private function sendTicketEmail()
    {
        $data = $this->loadById($this->id);

        if (!$data) {
            return false;
        }

        // If no user email is available, skip sending and log
        if (empty($data['user_email'])) {
            error_log('Tickets::sendTicketEmail: no user email for ticket ' . $this->id);
            return false;
        }

        // Utilisation de PHPMailer
        require_once BASE_PATH . '/vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'votre-email@gmail.com';
            $mail->Password = 'votre-mot-de-passe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire
            $mail->setFrom('noreply@billetterie.com', 'Billetterie Sportive');
            $mail->addAddress($data['user_email'] ?? '', trim(($data['user_nom'] ?? '') . ' ' . ($data['user_prenom'] ?? '')));

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Votre billet pour ' . ($data['home_team'] ?? '') . ' vs ' . ($data['away_team'] ?? '');
            $mail->Body = "
                <h2>Merci pour votre achat !</h2>
                <p>Votre billet pour le match " . ($data['home_team'] ?? '') . " vs " . ($data['away_team'] ?? '') . " est prêt.</p>
                <p><strong>Date:</strong> " . date('d/m/Y H:i', strtotime($data['date_match'])) . "</p>
                <p><strong>Place:</strong> " . htmlspecialchars($data['place_number'] ?? '') . "</p>
                <p>Veuillez trouver votre billet en pièce jointe.</p>
            ";


            $pdfContent = $this->generatePDF();
            $mail->addStringAttachment($pdfContent, 'billet.pdf');

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log("Erreur d'envoi d'email: " . $e->getMessage());
            return false;
        }
    }
}
