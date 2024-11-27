<?php
require_once 'db.php';
require_once 'fpdf/fpdf.php';

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("Invalid order ID.");
}

$db = (new Database())->getConnection();

// Fetch order details
$query = "SELECT o.order_id, o.order_date, o.total_amount, o.address, o.email, o.mobile_number, o.city, o.province, o.country, o.pincode, u.name
          FROM orders o
          JOIN users u ON o.user_id = u.user_id
          WHERE o.order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$query = "SELECT p.name, oi.quantity, oi.price
          FROM order_items oi
          JOIN products p ON oi.product_id = p.product_id
          WHERE oi.order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure the invoices directory exists
$invoiceDir = "invoices/";
if (!is_dir($invoiceDir)) {
    mkdir($invoiceDir, 0755, true); // Create the directory with appropriate permissions
}

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, "Order ID: {$order['order_id']}", 0, 1);
$pdf->Cell(0, 10, "Customer: {$order['name']}", 0, 1);
$pdf->Cell(0, 10, "Email: {$order['email']}", 0, 1);
$pdf->Cell(0, 10, "Mobile: {$order['mobile_number']}", 0, 1);
$pdf->Cell(0, 10, "Address: {$order['address']}, City: {$order['city']}, Province: {$order['province']}, Country: {$order['country']}, Pincode: {$order['pincode']}", 0, 1);
$pdf->Cell(0, 10, "Order Date: {$order['order_date']}", 0, 1);
$pdf->Ln(10);

$pdf->Cell(60, 10, 'Product', 1);
$pdf->Cell(30, 10, 'Quantity', 1);
$pdf->Cell(30, 10, 'Price', 1);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

foreach ($orderItems as $item) {
    $total = $item['quantity'] * $item['price'];
    $pdf->Cell(60, 10, $item['name'], 1);
    $pdf->Cell(30, 10, $item['quantity'], 1);
    $pdf->Cell(30, 10, "$" . number_format($item['price'], 2), 1);
    $pdf->Cell(30, 10, "$" . number_format($total, 2), 1);
    $pdf->Ln();
}

$pdf->Ln();
$pdf->Cell(0, 10, "Grand Total: $" . number_format($order['total_amount'], 2), 0, 1, 'R');

// Save PDF to file
$invoicePath = $invoiceDir . "invoice_{$order['order_id']}.pdf";
$pdf->Output('F', $invoicePath);

// Update invoice path in the database
$query = "UPDATE orders SET invoice_path = :invoice_path WHERE order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->execute([
    ':invoice_path' => $invoicePath,
    ':order_id' => $order_id
]);

// Output PDF to browser
$pdf->Output('I', "Invoice_{$order['order_id']}.pdf");
?>