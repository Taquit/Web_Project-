<?php
session_start();

// Determinar el origen de los datos (Prioriza el formulario POST, respalda con la Sesión)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['no_boleta'])) {
    $datos = $_POST;
} elseif (isset($_SESSION['Registro'])) {
    $datos = $_SESSION['Registro'];
} else {
    // Si no hay datos por ninguna vía, redirige al inicio de forma segura
    header("Location: ../../Front/Home_page/index.php");
    exit();
}

// Desactivar caché por completo para evitar PDFs cacheados viejos
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

require('../../Assets/FPDF_v186/fpdf.php');

class PDF extends FPDF {
    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(150, 150, 150);
        $texto_footer = '© 2026 Equipo 2 · Sistema de Registro de Alumnos de Nuevo Ingreso · U.A. Tecnologías para el Desarrollo de Aplicaciones Web';
        $this->Cell(0, 10, utf8_decode($texto_footer), 0, 0, 'C');
    }
}

// 1. Inicializar PDF en Vertical (Portrait), milímetros, tamaño A4
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetMargins(15, 15, 15);

// Definición de la paleta de colores del diseño (RGB)
$color_guinda = array(127, 20, 49);     // #7F1431 (IPN / Títulos)
$color_gris_label = array(110, 120, 130); // Etiquetas secundarias
$color_negro_txt = array(30, 30, 30);    // Valores principales
$color_naranja = array(230, 126, 34);    // #E67E22 (Recuadro de Examen)

// Encabezado superior estático
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
$pdf->Cell(8, 5, 'IPN', 0, 0, 'L');
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(3, 5, '|', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, 5, 'ESCOM', 0, 0, 'L');

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(160, 160, 160);
$pdf->Cell(0, 5, utf8_decode('U.A Tecnologías para el Desarrollo de Aplicaciones Web · Equipo No. 2 · Sistema de Registro'), 0, 1, 'L');

// Línea divisoria gris superior muy tenue
$pdf->SetDrawColor(230, 230, 230);
$pdf->Line(15, 22, 195, 22);

$alto_recuadro_50 = 148.5; 
$y_inicio_acuse = 40;      

$pdf->SetDrawColor(220, 224, 230);
$pdf->SetLineWidth(0.2);
$pdf->SetXY(15, $y_inicio_acuse);

// Logos internos del acuse
$pdf->Image('../../Assets/ipn_logo_pdf.png', 20, 41, 20);
$pdf->Image('../../Assets/escudoESCOM_pdf.png', 45, 44, 18);

// Título derecho: Acuse de Registro
$pdf->SetXY(80, $y_inicio_acuse + 7);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
$pdf->Cell(0, 5, utf8_decode('Acuse de Registro'), 0, 1, 'L');
$pdf->SetXY(80, $pdf->GetY() + 1);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, utf8_decode('Examen Diagnóstico · Nuevo Ingreso 2026'), 0, 1, 'L');

$pdf->Line(25, $y_inicio_acuse + 22, 185, $y_inicio_acuse + 22);

// Saludo al Estudiante (CORREGIDO: El utf8_decode ahora envuelve toda la cadena junta)
$pdf->SetXY(25, $y_inicio_acuse + 28);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
$nombre_completo = "Hola, " . $datos['name'] . " " . $datos['last_name_P'] . " " . $datos['last_name_M'];
$pdf->Cell(0, 6, utf8_decode($nombre_completo), 0, 1, 'L');
$pdf->Ln(14);

$y_datos = $pdf->GetY();

// --- FILA 1 ---
$pdf->SetXY(25, $y_datos);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('No. de Boleta'), 0, 1, 'L');
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['no_boleta']), 0, 1, 'L');

$pdf->SetXY(110, $y_datos);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('CURP'), 0, 1, 'L');
$pdf->SetXY(110, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['curp']), 0, 1, 'L');

$pdf->Ln(18); 
$y_fila2 = $pdf->GetY();

// --- FILA 2 ---
$pdf->SetXY(25, $y_fila2);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('Fecha de Nacimiento'), 0, 1, 'L');
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['birth_date']), 0, 1, 'L');

$pdf->SetXY(110, $y_fila2);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('Género'), 0, 1, 'L');
$pdf->SetXY(110, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['genero']), 0, 1, 'L');

$pdf->Ln(18); 
$y_fila3 = $pdf->GetY();

// --- FILA 3 ---
$pdf->SetXY(25, $y_fila3);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('Estado de Origen'), 0, 1, 'L');
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['state']), 0, 1, 'L');

$pdf->SetXY(110, $y_fila3);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('Escuela de Procedencia'), 0, 1, 'L');
$pdf->SetXY(110, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['school']), 0, 1, 'L');

$pdf->Ln(18); 
$y_fila4 = $pdf->GetY();

// --- FILA 4 ---
$pdf->SetXY(25, $y_fila4);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('Promedio'), 0, 1, 'L');
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['promedio']), 0, 1, 'L');

$pdf->SetXY(110, $y_fila4);
$pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(0, 4, utf8_decode('Correo Institucional'), 0, 1, 'L');
$pdf->SetXY(110, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
$pdf->Cell(0, 6, utf8_decode($datos['correo']), 0, 1, 'L');

// Pintamos el contenedor gris exterior
$pdf->SetDrawColor(215, 220, 225);
$pdf->Rect(15, $y_inicio_acuse, 180, $alto_recuadro_50);

// ======================================================================
// CUADRO NARANJA: ASIGNACIÓN DE EXAMEN DIAGNÓSTICO
// ======================================================================
$y_caja_examen = $y_inicio_acuse + $alto_recuadro_50 + 6;

$pdf->SetDrawColor($color_naranja[0], $color_naranja[1], $color_naranja[2]);
$pdf->SetLineWidth(0.4);
$pdf->Rect(15, $y_caja_examen, 180, 26);

// Título interno de la caja examen
$pdf->SetXY(20, $y_caja_examen + 3);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($color_naranja[0], $color_naranja[1], $color_naranja[2]);
$pdf->Cell(0, 4, utf8_decode('ASIGNACIÓN DE EXAMEN DIAGNÓSTICO'), 0, 1, 'L');

$y_detalles_examen = $pdf->GetY() + 2;
// Columna: Laboratorio
$pdf->SetXY(20, $y_detalles_examen);
$pdf->SetFont('Arial', '', 8); 
$pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(35, 4, utf8_decode('Laboratorio'), 0, 1, 'L');
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 11); 
$pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
$pdf->Cell(35, 5, utf8_decode($datos['laboratorio']), 0, 0, 'L');

// Columna: Horario
$pdf->SetXY(115, $y_detalles_examen);
$pdf->SetFont('Arial', '', 8); 
$pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
$pdf->Cell(60, 4, utf8_decode('Horario'), 0, 1, 'L');
$pdf->SetXY(115, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 10); 
$pdf->SetTextColor($color_naranja[0], $color_naranja[1], $color_naranja[2]);
$horario_completo = $datos['fecha'] . '  ·  ' . $datos['hora_ini'] . ' a ' . $datos['hora_fin'];
$pdf->Cell(60, 5, utf8_decode($horario_completo), 0, 1, 'L');

// Renderizar y enviar el archivo PDF al navegador
$pdf->SetTitle($datos['no_boleta'] . "_EXDG2026.pdf");
$pdf->Output('I', $datos['no_boleta'] . "_EXDG2026.pdf");
?>