<form action="process_receipt.php" method="post" enctype="multipart/form-data">
    Upload Receipt: <input type="file" name="receipt" required>
    <br>Select OCR Service:
    <select name="ocr_service">
        <option value="google">Google Cloud Vision</option>
        <option value="tesseract">Tesseract OCR</option>
        <option value="microsoft">Microsoft Azure Form Recognizer</option>
    </select>
    Vendor Name: <input type="text" name="vendor_name" required><br>
    Receipt Date: <input type="date" name="receipt_date" required><br>

    <br><input type="submit" value="Upload & Process">
</form>
