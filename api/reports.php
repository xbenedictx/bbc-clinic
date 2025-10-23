<?php
include 'config.php';
require 'vendor/autoload.php'; // Composer: setasign/fpdf, phpqrcode/qrcode

use setasign\Fpdi\Fpdi;
use QRcode;

$input = json_decode(file_get_contents('php://input'), true);
$invoice_id = $input['invoice_id'] ?? '';

$stmt = $pdo->prepare("SELECT i.*, a.date, p.name AS pet_name, c.name AS client_name 
                       FROM invoices i 
                       JOIN appointments a ON i.appointment_id = a.id 
                       JOIN pets p ON a.pet_id = p.id 
                       JOIN clients c ON p.client_id = c.id 
                       WHERE i.id = ?");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    echo json_encode(['success' => false, 'message' => 'Invoice not found']);
    exit;
}

// Generate QR code
$qr_file = 'qr_' . $invoice_id . '.png';
QRcode::png($invoice['transaction_id'], $qr_file, 'L', 4, 2);

// Create PDF
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'BBC Clinic Invoice', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Client: ' . $invoice['client_name'], 0, 1);
$pdf->Cell(0, 10, 'Pet: ' . $invoice['pet_name'], 0, 1);
$pdf->Cell(0, 10, 'Date: ' . $invoice['date'], 0, 1);
$pdf->Cell(0, 10, 'Amount: $' . $invoice['amount'], 0, 1);
$pdf->Cell(0, 10, 'Status: ' . $invoice['status'], 0, 1);
$pdf->Image($qr_file, 10, 60, 33, 33, 'PNG');
unlink($qr_file); // Cleanup

$pdf->Output('D', 'invoice_' . $invoice_id . '.pdf');
?>