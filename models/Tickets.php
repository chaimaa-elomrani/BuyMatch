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

    public function generatePDF($ticketId = null)
    {
        $ticketId = $ticketId ?? $this->id;
        if (!$ticketId) {
            throw new Exception("Ticket ID is required.");
        }
        
        $data = $this->loadById($ticketId);
        if (!$data) {
            throw new Exception("Ticket not found.");
        }
        
        $this->id = $ticketId;
        $this->matchData = $data;

        $dompdf = new Dompdf();
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .ticket { border: 2px solid #667eea; padding: 20px; max-width: 600px; margin: 0 auto; }
                .header { text-align: center; color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-bottom: 20px; }
                .match-info { margin: 15px 0; }
                .info-row { margin: 10px 0; }
                .label { font-weight: bold; color: #333; }
                .qr-code { text-align: center; margin-top: 20px; font-family: monospace; font-size: 18px; padding: 10px; background: #f5f5f5; }
            </style>
        </head>
        <body>
            <div class='ticket'>
                <div class='header'>
                    <h1>BuyMatch - Billet</h1>
                </div>
                <div class='match-info'>
                    <h2>{$data['team1_name']} vs {$data['team2_name']}</h2>
                    <div class='info-row'><span class='label'>Date:</span> " . date('d/m/Y à H:i', strtotime($data['date_match'])) . "</div>
                    <div class='info-row'><span class='label'>Lieu:</span> {$data['lieu']}</div>
                    <div class='info-row'><span class='label'>Catégorie:</span> {$data['category_name']}</div>
                    <div class='info-row'><span class='label'>Prix:</span> {$data['prix']} DH</div>
                    <div class='info-row'><span class='label'>Place:</span> {$data['place_number']}</div>
                    <div class='info-row'><span class='label'>Acheteur:</span> {$data['user_prenom']} {$data['user_nom']}</div>
                </div>
                <div class='qr-code'>
                    <div>QR Code:</div>
                    <div style='font-size: 24px; margin-top: 10px;'>{$data['qr_code']}</div>
                </div>
            </div>
        </body>
        </html>";

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
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration (you can move this to config file)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Change this
            $mail->Password = 'your-app-password'; // Change this (use app password for Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Destinataire
            $mail->setFrom('noreply@buymatch.com', 'BuyMatch');
            $mail->addAddress($data['user_email'], trim($data['user_prenom'] . ' ' . $data['user_nom']));

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Votre billet pour ' . $data['team1_name'] . ' vs ' . $data['team2_name'];
            $mail->Body = "
                <h2>Merci pour votre achat !</h2>
                <p>Votre billet pour le match <strong>{$data['team1_name']} vs {$data['team2_name']}</strong> est prêt.</p>
                <p><strong>Date:</strong> " . date('d/m/Y à H:i', strtotime($data['date_match'])) . "</p>
                <p><strong>Lieu:</strong> {$data['lieu']}</p>
                <p><strong>Place:</strong> {$data['place_number']}</p>
                <p><strong>Catégorie:</strong> {$data['category_name']}</p>
                <p>Veuillez trouver votre billet en pièce jointe.</p>
            ";

            $pdfContent = $this->generatePDF();
            $mail->addStringAttachment($pdfContent, 'billet-' . $this->id . '.pdf', 'base64', 'application/pdf');

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log("Erreur d'envoi d'email: " . $e->getMessage());
            return false;
        }
    }
}
