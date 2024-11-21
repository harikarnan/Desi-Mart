<?php
require 'fpdf/fpdf.php';

function generateInvoice($orderId, $user, $cartItems, $totalAmount) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Header
    $pdf->Cell(0, 10, 'DesiMart Invoice', 0, 1, 'C');
    $pdf->Ln(10);

    // User Details
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Customer Name: " . $user['name'], 0, 1);
    $pdf->Cell(0, 10, "Email: " . $user['email'], 0, 1);
    $pdf->Cell(0, 10, "Address: " . $user['address'], 0, 1);
    $pdf->Ln(10);

    // Order Details
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 10, 'Product', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Total', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    foreach ($cartItems as $item) {
        $pdf->Cell(80, 10, $item['name'], 1);
        $pdf->Cell(30, 10, $item['quantity'], 1);
        $pdf->Cell(30, 10, '$' . number_format($item['price'], 2), 1);
        $pdf->Cell(30, 10, '$' . number_format($item['price'] * $item['quantity'], 2), 1);
        $pdf->Ln();
    }

    // Total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(140, 10, 'Total:', 1);
    $pdf->Cell(30, 10, '$' . number_format($totalAmount, 2), 1);
    $pdf->Ln();

    // Save and Output
    $filePath = "invoices/invoice_$orderId.pdf";
    $pdf->Output('F', $filePath); // Save to file
    return $filePath;
}
?>
