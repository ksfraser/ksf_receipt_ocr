<?php

/****************************************************************
*
* I think this may be a hallucination.  I don't think this matches
* the receipt to the vendor and the amount that was paid to the CC.
*
*  AI says this can be automated :)
*	In Cron:
*		0 12 * * * php /path/to/payment_script.php
*
*/

require 'config.php'; // FA database connection

// Get the latest purchase order
$stmt = $pdo->query("SELECT supplier_id, price FROM fa_purch_orders WHERE status = 'Pending' ORDER BY order_no DESC LIMIT 1");
$purchaseOrder = $stmt->fetch();

if ($purchaseOrder) {
    $supplier_id = $purchaseOrder['supplier_id'];
    $amount = $purchaseOrder['price'];

    // Get supplier payment details
    $stmt = $pdo->prepare("SELECT bank_account FROM fa_suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);
    $supplier = $stmt->fetch();

    if ($supplier) {
        $bank_account = $supplier['bank_account'];

        // Create the vendor payment entry
        $stmt = $pdo->prepare("INSERT INTO fa_bank_trans (bank_act, trans_date, amount, person_id, type) VALUES (?, NOW(), ?, ?, 'Supplier Payment')");
        $stmt->execute([$bank_account, $amount, $supplier_id]);

        echo "Payment of $$amount processed to Supplier ID $supplier_id (Bank: $bank_account).";
    } else {
        echo "Supplier payment details not found!";
    }
} else {
    echo "No pending purchase orders found!";
}
?>



<?php
require 'config.php';

// Fetch latest receipt that hasn't been processed
$stmt = $pdo->query("SELECT * FROM fa_receipt_uploads WHERE processed = 0 ORDER BY uploaded_at DESC LIMIT 1");
$receipt = $stmt->fetch();

if (!$receipt) {
    die("No pending receipts found for payment!");
}

$vendorName = $receipt['vendor_name'];
$receiptTotal = $receipt['total'];
$receiptId = $receipt['id'];

// Match vendor to FA database
$stmt = $pdo->prepare("SELECT supplier_id, bank_account FROM fa_suppliers WHERE supp_name = ?");
$stmt->execute([$vendorName]);
$supplier = $stmt->fetch();

if (!$supplier) {
    die("Vendor not found in FA database!");
}

$supplierId = $supplier['supplier_id'];
$bankAccount = $supplier['bank_account'];

// Insert payment transaction
$stmt = $pdo->prepare("INSERT INTO fa_bank_trans (bank_act, trans_date, amount, person_id, type) VALUES (?, NOW(), ?, ?, 'Supplier Payment')");
$stmt->execute([$bankAccount, $receiptTotal, $supplierId]);

// Mark the receipt as processed
$stmt = $pdo->prepare("UPDATE fa_receipt_uploads SET processed = 1 WHERE id = ?");
$stmt->execute([$receiptId]);

echo "Payment of $$receiptTotal processed for $vendorName (Bank: $bankAccount)";
?>
