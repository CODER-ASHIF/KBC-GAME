<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
require('db.php');
require('fpdf.php');

if (!isset($_SESSION['player_name']) || $_SESSION['balance'] <= 0) {
    header("Location: index.php");
    exit();
}

// Collect data from form and save to session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['acc_holder'] = strtoupper($_POST['acc_holder']);
    $_SESSION['bank_name'] = strtoupper($_POST['bank_name']);
    $_SESSION['ifsc'] = strtoupper($_POST['ifsc']);
    $_SESSION['acc_number'] = $_POST['acc_number'];
    $_SESSION['bank_details_submitted'] = true;

    // Save bank details to database
    $stmt = $conn->prepare("INSERT INTO players (player_id, name, amount, method, acc_holder, bank_name, ifsc, acc_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $method = 'bank_transfer';
    $stmt->bind_param("ssisssss", $_SESSION['player_id'], $_SESSION['player_name'], $_SESSION['balance'], $method, $_SESSION['acc_holder'], $_SESSION['bank_name'], $_SESSION['ifsc'], $_SESSION['acc_number']);
    $stmt->execute();
    $stmt->close();
}

// Get data from session for PDF generation
$name = strtoupper($_SESSION['player_name']);
$player_id = $_SESSION['player_id'];
$amount = $_SESSION['balance'];
$date = date('d-m-Y');

// AMOUNT IN WORDS FUNCTION
function numberToWords($number) {
    if ($number == 0) {
        return 'Zero';
    }
    
    $units = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine');
    $teens = array('Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
    $tens = array('', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
    $thousands = array('', 'Thousand', 'Lakh', 'Crore');
    
    $words = array();
    $num = (string)$number;
    $num = str_pad($num, strlen($num) + (3 - strlen($num) % 3) % 3, '0', STR_PAD_LEFT);
    $groups = str_split($num, 3);
    
    foreach ($groups as $i => $group) {
        $group = (int)$group;
        if ($group == 0) continue;
        
        $hundreds = floor($group / 100);
        $tens_units = $group % 100;
        
        if ($hundreds > 0) {
            $words[] = $units[$hundreds] . ' Hundred';
        }
        
        if ($tens_units > 0) {
            if ($tens_units < 10) {
                $words[] = $units[$tens_units];
            } elseif ($tens_units < 20) {
                $words[] = $teens[$tens_units - 10];
            } else {
                $tens_digit = floor($tens_units / 10);
                $units_digit = $tens_units % 10;
                $words[] = $tens[$tens_digit];
                if ($units_digit > 0) {
                    $words[] = $units[$units_digit];
                }
            }
        }
        
        if (isset($thousands[count($groups) - $i - 1])) {
            $words[] = $thousands[count($groups) - $i - 1];
        }
    }
    
    return implode(' ', $words);
}

$amount_words = numberToWords($amount) . ' Rupees Only';

// Dummy bank details for PDF if not provided in session
$bank_name_pdf = $_SESSION['bank_name'] ?? 'HDFC BANK';
$ifsc_pdf = $_SESSION['ifsc'] ?? 'HDFC0000123';
$account_number_pdf = $_SESSION['acc_number'] ?? '123456789012';

//PDF SECTION START--------------------------------------------------------------
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 18);

// Title
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 15, 'KBC FUND TRANSFER STATEMENT', 0, 1, 'C', true);
$pdf->Ln(5);

// Player details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Player ID:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, $player_id, 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Name:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, $name, 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Transfer Date:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, $date, 0, 1);

$pdf->Ln(8);

// Amount display
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(220, 230, 241);
$pdf->Cell(0, 12, 'TRANSFER DETAILS', 0, 1, 'C', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Transferred Amount:', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 100, 0);
$pdf->Cell(0, 10, 'INR ' . number_format($amount). "/-", 0, 1);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(5);

// Amount in words
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Amount in Words:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 8, $amount_words, 0, 1);

$pdf->Ln(10);

// Account details
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(220, 230, 241);
$pdf->Cell(0, 12, 'BANK ACCOUNT DETAILS', 0, 1, 'C', true);
$pdf->Ln(5);

$details = array(
    'Account Holder' => $name,
    'Bank Name' => $bank_name_pdf,
    'IFSC Code' => $ifsc_pdf,
    'Account Number' => $account_number_pdf
);

$pdf->SetFont('Arial', '', 12);
foreach ($details as $label => $value) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, $label . ':', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $value, 0, 1);
}

// Footer
$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 10, 'This is an auto-generated bank transfer statement for KBC Game Project.', 0, 1, 'C');
$pdf->Cell(0, 10, 'Generated on: ' . date('d-m-Y H:i:s'), 0, 1, 'C');

// --- SIGNATURE SECTION ---
// Current Y position after last content
$current_y = $pdf->GetY() + 20; 

$signature_path = 'images/signature.png';
$signature_width = 35; 
$signature_height = 15;

$pdf->SetXY(130, $current_y);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Verified By  ___________________________', 0, 1, 'L');

if (file_exists($signature_path)) {
    $pdf->Image($signature_path, 150, $current_y - 8, $signature_width, $signature_height);
} else {
    $pdf->SetXY(55, $current_y - 2);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell($signature_width, $signature_height, '[Signature Missing]', 1, 0, 'C');
}

// --- END SIGNATURE SECTION ---
//PDF SECTION END----------------------------------------------------------------------------

// --- Save PDF to server and update database ---
$upload_dir = 'bank_slips/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$filename = $player_id . '_' . str_replace(' ', '_', $_SESSION['player_name'])  . "_bank_statement.pdf";
$filepath = $upload_dir . $filename;

$pdf->Output('F', $filepath);

// Update database
$update_stmt = $conn->prepare("UPDATE players SET bank_file = ? WHERE player_id = ?");
if ($update_stmt) {
    $update_stmt->bind_param("ss", $filepath, $_SESSION['player_id']);
    $update_stmt->execute();
    $update_stmt->close();
} else {
    error_log("Failed to prepare update statement for bank_file: " . $conn->error);
}

// Output for immediate download
$pdf->Output('D', $player_id .'_' . str_replace(' ', '_', $_SESSION['player_name']). '_bank_statement.pdf');
?>
