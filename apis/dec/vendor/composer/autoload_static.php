<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3db5e92c3db050b497024770f01220f5
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Dec\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Dec\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Datamatrix' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/barcodes/datamatrix.php',
        'Dec\\acciones\\AdminstradorAcciones' => __DIR__ . '/../..' . '/app/acciones/AdministradorAcciones.php',
        'Dec\\config\\ConfigData' => __DIR__ . '/../..' . '/app/config/config.php',
        'Dec\\controller\\ClientesController' => __DIR__ . '/../..' . '/app/controller/ClientesController.php',
        'Dec\\controller\\FirmantesController' => __DIR__ . '/../..' . '/app/controller/FirmantesController.php',
        'Dec\\controller\\OperaDocumentosController' => __DIR__ . '/../..' . '/app/controller/OperaDocumentosController.php',
        'Dec\\controller\\PerfilesController' => __DIR__ . '/../..' . '/app/controller/PerfilesController.php',
        'Dec\\controller\\RolesController' => __DIR__ . '/../..' . '/app/controller/RolesController.php',
        'Dec\\controller\\SubTipoDocumentosController' => __DIR__ . '/../..' . '/app/controller/SubTipoDocumentosController.php',
        'Dec\\controller\\SubTipoDocumentosFirmantesController' => __DIR__ . '/../..' . '/app/controller/SubTipoDocumentosFirmantesController.php',
        'Dec\\controller\\TestController' => __DIR__ . '/../..' . '/app/controller/TestController.php',
        'Dec\\controller\\TipoDocumentosClienteController' => __DIR__ . '/../..' . '/app/controller/TipoDocumentosClienteController.php',
        'Dec\\controller\\TipoDocumentosController' => __DIR__ . '/../..' . '/app/controller/TipoDocumentosController.php',
        'Dec\\controller\\UsuariosController' => __DIR__ . '/../..' . '/app/controller/UsuariosController.php',
        'Dec\\database\\DBConn' => __DIR__ . '/../..' . '/app/database/Database.php',
        'Dec\\database\\MongoDBConn' => __DIR__ . '/../..' . '/app/database/Database.php',
        'Dec\\error\\MensajeError' => __DIR__ . '/../..' . '/app/error/MensajeError.php',
        'Dec\\lib\\Api' => __DIR__ . '/../..' . '/app/lib/api.php',
        'Dec\\lib\\DecApi' => __DIR__ . '/../..' . '/app/lib/api.php',
        'Dec\\model\\Clientes' => __DIR__ . '/../..' . '/app/model/Clientes.php',
        'Dec\\model\\Documentos' => __DIR__ . '/../..' . '/app/model/Documentos.php',
        'Dec\\model\\Firmantes' => __DIR__ . '/../..' . '/app/model/Firmantes.php',
        'Dec\\model\\FirmantesClientes' => __DIR__ . '/../..' . '/app/model/FirmantesClientes.php',
        'Dec\\model\\Logging' => __DIR__ . '/../..' . '/app/model/Logging.php',
        'Dec\\model\\OperaDocumentos' => __DIR__ . '/../..' . '/app/model/OperaDocumentos.php',
        'Dec\\model\\Perfilamientos' => __DIR__ . '/../..' . '/app/model/Perfilamientos.php',
        'Dec\\model\\Perfiles' => __DIR__ . '/../..' . '/app/model/Perfiles.php',
        'Dec\\model\\PerfilesClientes' => __DIR__ . '/../..' . '/app/model/PerfilesClientes.php',
        'Dec\\model\\Roles' => __DIR__ . '/../..' . '/app/model/Roles.php',
        'Dec\\model\\Salida' => __DIR__ . '/../..' . '/app/model/Salida.php',
        'Dec\\model\\SubTipoDocumentos' => __DIR__ . '/../..' . '/app/model/SubTipoDocumentos.php',
        'Dec\\model\\SubTipoDocumentosFirmantes' => __DIR__ . '/../..' . '/app/model/SubTipoDocumentosFirmantes.php',
        'Dec\\model\\TipoDocumentos' => __DIR__ . '/../..' . '/app/model/TipoDocumentos.php',
        'Dec\\model\\TipoDocumentosCliente' => __DIR__ . '/../..' . '/app/model/TipoDocumentosCliente.php',
        'Dec\\model\\Usuarios' => __DIR__ . '/../..' . '/app/model/Usuarios.php',
        'Dec\\models\\DbUtils' => __DIR__ . '/../..' . '/app/model/DBUtils.php',
        'Dec\\utils\\Funciones' => __DIR__ . '/../..' . '/app/utils/Funciones.php',
        'Dec\\utils\\SimpleMail' => __DIR__ . '/../..' . '/app/utils/SimpleMail.php',
        'EasyPeasyICS' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/EasyPeasyICS.php',
        'FPDF_TPL' => __DIR__ . '/..' . '/setasign/fpdi/fpdf_tpl.php',
        'FPDI' => __DIR__ . '/..' . '/setasign/fpdi/fpdi.php',
        'FilterASCII85' => __DIR__ . '/..' . '/setasign/fpdi/filters/FilterASCII85.php',
        'FilterASCIIHexDecode' => __DIR__ . '/..' . '/setasign/fpdi/filters/FilterASCIIHexDecode.php',
        'FilterLZW' => __DIR__ . '/..' . '/setasign/fpdi/filters/FilterLZW.php',
        'PDF417' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/barcodes/pdf417.php',
        'PHPMailer' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
        'PHPMailerOAuth' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauth.php',
        'PHPMailerOAuthGoogle' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauthgoogle.php',
        'POP3' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.pop3.php',
        'QRcode' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/barcodes/qrcode.php',
        'SMTP' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.smtp.php',
        'TCPDF' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf.php',
        'TCPDF2DBarcode' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_barcodes_2d.php',
        'TCPDFBarcode' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_barcodes_1d.php',
        'TCPDF_COLORS' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_colors.php',
        'TCPDF_FILTERS' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_filters.php',
        'TCPDF_FONTS' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_fonts.php',
        'TCPDF_FONT_DATA' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_font_data.php',
        'TCPDF_IMAGES' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_images.php',
        'TCPDF_IMPORT' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_import.php',
        'TCPDF_PARSER' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_parser.php',
        'TCPDF_STATIC' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_static.php',
        'fpdi_bridge' => __DIR__ . '/..' . '/setasign/fpdi-tcpdf/fpdi_bridge.php',
        'fpdi_pdf_parser' => __DIR__ . '/..' . '/setasign/fpdi/fpdi_pdf_parser.php',
        'ntlm_sasl_client_class' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/ntlm_sasl_client.php',
        'pdf_context' => __DIR__ . '/..' . '/setasign/fpdi/pdf_context.php',
        'phpmailerException' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3db5e92c3db050b497024770f01220f5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3db5e92c3db050b497024770f01220f5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3db5e92c3db050b497024770f01220f5::$classMap;

        }, null, ClassLoader::class);
    }
}
