<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use Dompdf\Dompdf;

echo "Testing PHPMailer and Dompdf initialization...\n";

try {
    $m = new PHPMailer(true);
    echo "PHPMailer instantiated\n";
} catch (Exception $e) {
    echo "PHPMailer error: " . $e->getMessage() . "\n";
}

try {
    $d = new Dompdf();
    echo "Dompdf instantiated\n";
} catch (Exception $e) {
    echo "Dompdf error: " . $e->getMessage() . "\n";
}

echo "Done\n";
