<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Datamatrix' => $vendorDir . '/tecnickcom/tcpdf/include/barcodes/datamatrix.php',
    'Dec\\acciones\\AdminstradorAcciones' => $baseDir . '/app/acciones/AdministradorAcciones.php',
    'Dec\\config\\ConfigData' => $baseDir . '/app/config/config.php',
    'Dec\\controller\\ClientesController' => $baseDir . '/app/controller/ClientesController.php',
    'Dec\\controller\\FirmantesController' => $baseDir . '/app/controller/FirmantesController.php',
    'Dec\\controller\\OperaDocumentosController' => $baseDir . '/app/controller/OperaDocumentosController.php',
    'Dec\\controller\\PerfilesController' => $baseDir . '/app/controller/PerfilesController.php',
    'Dec\\controller\\RolesController' => $baseDir . '/app/controller/RolesController.php',
    'Dec\\controller\\SubTipoDocumentosController' => $baseDir . '/app/controller/SubTipoDocumentosController.php',
    'Dec\\controller\\SubTipoDocumentosFirmantesController' => $baseDir . '/app/controller/SubTipoDocumentosFirmantesController.php',
    'Dec\\controller\\TestController' => $baseDir . '/app/controller/TestController.php',
    'Dec\\controller\\TipoDocumentosClienteController' => $baseDir . '/app/controller/TipoDocumentosClienteController.php',
    'Dec\\controller\\TipoDocumentosController' => $baseDir . '/app/controller/TipoDocumentosController.php',
    'Dec\\controller\\UsuariosController' => $baseDir . '/app/controller/UsuariosController.php',
    'Dec\\database\\DBConn' => $baseDir . '/app/database/Database.php',
    'Dec\\database\\MongoDBConn' => $baseDir . '/app/database/Database.php',
    'Dec\\error\\MensajeError' => $baseDir . '/app/error/MensajeError.php',
    'Dec\\lib\\Api' => $baseDir . '/app/lib/api.php',
    'Dec\\lib\\DecApi' => $baseDir . '/app/lib/api.php',
    'Dec\\model\\Clientes' => $baseDir . '/app/model/Clientes.php',
    'Dec\\model\\Documentos' => $baseDir . '/app/model/Documentos.php',
    'Dec\\model\\Firmantes' => $baseDir . '/app/model/Firmantes.php',
    'Dec\\model\\FirmantesClientes' => $baseDir . '/app/model/FirmantesClientes.php',
    'Dec\\model\\Logging' => $baseDir . '/app/model/Logging.php',
    'Dec\\model\\OperaDocumentos' => $baseDir . '/app/model/OperaDocumentos.php',
    'Dec\\model\\Perfilamientos' => $baseDir . '/app/model/Perfilamientos.php',
    'Dec\\model\\Perfiles' => $baseDir . '/app/model/Perfiles.php',
    'Dec\\model\\PerfilesClientes' => $baseDir . '/app/model/PerfilesClientes.php',
    'Dec\\model\\Roles' => $baseDir . '/app/model/Roles.php',
    'Dec\\model\\Salida' => $baseDir . '/app/model/Salida.php',
    'Dec\\model\\SubTipoDocumentos' => $baseDir . '/app/model/SubTipoDocumentos.php',
    'Dec\\model\\SubTipoDocumentosFirmantes' => $baseDir . '/app/model/SubTipoDocumentosFirmantes.php',
    'Dec\\model\\TipoDocumentos' => $baseDir . '/app/model/TipoDocumentos.php',
    'Dec\\model\\TipoDocumentosCliente' => $baseDir . '/app/model/TipoDocumentosCliente.php',
    'Dec\\model\\Usuarios' => $baseDir . '/app/model/Usuarios.php',
    'Dec\\models\\DbUtils' => $baseDir . '/app/model/DBUtils.php',
    'Dec\\utils\\Funciones' => $baseDir . '/app/utils/Funciones.php',
    'Dec\\utils\\SimpleMail' => $baseDir . '/app/utils/SimpleMail.php',    
    'EasyPeasyICS' => $vendorDir . '/phpmailer/phpmailer/extras/EasyPeasyICS.php',
    'FPDF_TPL' => $vendorDir . '/setasign/fpdi/fpdf_tpl.php',
    'FPDI' => $vendorDir . '/setasign/fpdi/fpdi.php',
    'FilterASCII85' => $vendorDir . '/setasign/fpdi/filters/FilterASCII85.php',
    'FilterASCIIHexDecode' => $vendorDir . '/setasign/fpdi/filters/FilterASCIIHexDecode.php',
    'FilterLZW' => $vendorDir . '/setasign/fpdi/filters/FilterLZW.php',
    'PDF417' => $vendorDir . '/tecnickcom/tcpdf/include/barcodes/pdf417.php',
    'PHPMailer' => $vendorDir . '/phpmailer/phpmailer/class.phpmailer.php',
    'PHPMailerOAuth' => $vendorDir . '/phpmailer/phpmailer/class.phpmaileroauth.php',
    'PHPMailerOAuthGoogle' => $vendorDir . '/phpmailer/phpmailer/class.phpmaileroauthgoogle.php',
    'POP3' => $vendorDir . '/phpmailer/phpmailer/class.pop3.php',
    'QRcode' => $vendorDir . '/tecnickcom/tcpdf/include/barcodes/qrcode.php',
    'SMTP' => $vendorDir . '/phpmailer/phpmailer/class.smtp.php',
    'TCPDF' => $vendorDir . '/tecnickcom/tcpdf/tcpdf.php',
    'TCPDF2DBarcode' => $vendorDir . '/tecnickcom/tcpdf/tcpdf_barcodes_2d.php',
    'TCPDFBarcode' => $vendorDir . '/tecnickcom/tcpdf/tcpdf_barcodes_1d.php',
    'TCPDF_COLORS' => $vendorDir . '/tecnickcom/tcpdf/include/tcpdf_colors.php',
    'TCPDF_FILTERS' => $vendorDir . '/tecnickcom/tcpdf/include/tcpdf_filters.php',
    'TCPDF_FONTS' => $vendorDir . '/tecnickcom/tcpdf/include/tcpdf_fonts.php',
    'TCPDF_FONT_DATA' => $vendorDir . '/tecnickcom/tcpdf/include/tcpdf_font_data.php',
    'TCPDF_IMAGES' => $vendorDir . '/tecnickcom/tcpdf/include/tcpdf_images.php',
    'TCPDF_IMPORT' => $vendorDir . '/tecnickcom/tcpdf/tcpdf_import.php',
    'TCPDF_PARSER' => $vendorDir . '/tecnickcom/tcpdf/tcpdf_parser.php',
    'TCPDF_STATIC' => $vendorDir . '/tecnickcom/tcpdf/include/tcpdf_static.php',
    'fpdi_bridge' => $vendorDir . '/setasign/fpdi-tcpdf/fpdi_bridge.php',
    'fpdi_pdf_parser' => $vendorDir . '/setasign/fpdi/fpdi_pdf_parser.php',
    'ntlm_sasl_client_class' => $vendorDir . '/phpmailer/phpmailer/extras/ntlm_sasl_client.php',
    'pdf_context' => $vendorDir . '/setasign/fpdi/pdf_context.php',
    'phpmailerException' => $vendorDir . '/phpmailer/phpmailer/class.phpmailer.php',
);
