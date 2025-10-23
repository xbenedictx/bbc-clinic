<?php
include 'config.php';
require 'vendor/autoload.php';  // For FPDF and phpqrcode

use setasign\Fpdi;
use QRcode;

// Example: Generate invoice PDF
$input = json_decode(file_get_contents('php://input'), true);
$invoiceId = $input['invoice_id'];

// Fetch data (simplified)
$stmt = $pdo->prepare("SELECT * FROM invoices i JOIN appointments a ON i.appointment_id = a.id JOIN pets p ON a.pet_id = p.id WHERE i.id = ?");
$stmt->execute([$invoiceId]);
$invoice = $stmt->fetch();

// Generate QR (with transaction_id)
QRcode::png($invoice['transaction_id'], 'qr.png', 'L', 4, 2);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'BBC Clinic Invoice');
$pdf->Ln(10);
// Add details...
$pdf->Cell(40, 10, 'Amount: $' . $invoice['amount']);
$pdf->Ln(10);
// Add QR image
$pdf->Image('qr.png', 10, 100, 33, 0, 'PNG');
unlink('qr.png');  // Cleanup

$pdf->Output('D', 'invoice.pdf');  // Download
?>