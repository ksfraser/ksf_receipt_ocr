<?php
class hooks_ksf_receipt_ocr extends hooks {
    var $module_name = 'ksf_receipt_ocr';

    // Register module in the UI
    function install_options($app) {
        global $path_to_root;

        if ($app->name == 'AP') { // Accounts Payable section
            $app->add_lapp_function(
                0,
                _('Receipt OCR Processing'),
                $path_to_root . '/modules/ksf_receipt_ocr/process_receipt.php',
                'SA_SUPPLIER'
            );
        }
    }

    // Define database tables required for the module
    function install_access() {
        $security_sections = array('SA_KSF_RECEIPT_OCR');
        return $security_sections;
    }

    // Allow configuration settings
    function install_tabs($app) {
        if ($app->name == 'system') {
            $app->add_rapp_function(0, _('Receipt OCR Settings'), '/modules/ksf_receipt_ocr/settings.php', 'SA_SETUP');
        }
    }
}
?>


<?php
class hooks_ksf_receipt_ocr extends hooks {
    var $module_name = 'ksf_receipt_ocr';

    // Define security areas
    const SS_RECEIPT_OCR = "SS_KSF_RECEIPT_OCR"; // Security Section
    const SA_RECEIPT_OCR = "SA_KSF_RECEIPT_OCR"; // Access Role

    // Register module in FA's UI
    function install_options($app) {
        global $path_to_root;

        if ($app->name == 'AP') { // Accounts Payable section
            $app->add_lapp_function(
                0,
                _('Receipt OCR Processing'),
                $path_to_root . '/modules/ksf_receipt_ocr/process_receipt.php',
                self::SA_RECEIPT_OCR
            );
            $app->add_lapp_function(
                0,
                _('Manage OCR Receipts'),
                $path_to_root . '/modules/ksf_receipt_ocr/manage_receipts.php',
                self::SA_RECEIPT_OCR
            );
        }
    }

    // Define access permissions
    function install_access() {
        return array(self::SA_RECEIPT_OCR);
    }
}
?>
