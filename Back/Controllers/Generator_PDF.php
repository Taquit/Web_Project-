<?php
session_start();
if(isset($_SESSION['Registro'])){
    // Desactivar caché por completo para este script (útil en desarrollo)
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

    $Registro_user = $_SESSION['Registro'];

    require('../../Assets/FPDF_v186/fpdf.php');

    class PDF extends FPDF {
    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(150, 150, 150);
        $texto_footer = '© 2026 Equipo 2 Sistema de Registro de Alumnos de Nuevo Ingreso  U.A. Tecnologías para el Desarrollo de Aplicaciones Web';
        $this->Cell(0, 10, utf8_decode($texto_footer), 0, 0, 'C');
    }
    }

    // 1. Inicializar PDF en Vertical (Portrait), milímetros, tamaño A4 (210mm x 297mm)
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetMargins(15, 15, 15);

    // Definición de la paleta de colores del diseño (RGB)
    $color_guinda = array(127, 20, 49);     // #7F1431 (IPN / Títulos)
    $color_gris_label = array(110, 120, 130); // Etiquetas secundarias
    $color_negro_txt = array(30, 30, 30);    // Valores principales
    $color_naranja = array(230, 126, 34);    // #E67E22 (Recuadro de Examen)

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
    $pdf->Cell(8, 5, 'IPN', 0, 0, 'L');
    $pdf->SetTextColor(120, 120, 120);
    $pdf->Cell(3, 5, '|', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(15, 5, 'ESCOM', 0, 0, 'L');

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(160, 160, 160);
    $pdf->Cell(0, 5, 'U.A Tecnologias para el Desarrollo de Aplicaciones Web Equipo No. 2 Sistema de Registro de Estudiantes', 0, 1, 'L');

    // Línea divisoria gris superior muy tenue
    $pdf->SetDrawColor(230, 230, 230);
    $pdf->Line(15, 22, 195, 22);


    $alto_recuadro_50 = 148.5; 
    $y_inicio_acuse = 40;      

    $pdf->SetDrawColor(220, 224, 230);
    $pdf->SetLineWidth(0.2);
    $pdf->SetXY(15, $y_inicio_acuse);

    // 1. Mini Encabezado interno del Acuse
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
    $pdf->SetXY(25, $y_inicio_acuse + 8);
    $pdf->Image('../../Assets/ipn_logo_pdf.png', 20, 41, 20);
    $pdf->SetTextColor(180, 180, 180);
    $pdf->Cell(5, 5, '|', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->Image('../../Assets/escudoESCOM_pdf.png', 45, 44, 18);

    // Título derecho: Acuse de Registro
    $pdf->SetXY(80, $y_inicio_acuse + 7);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
    $pdf->Cell(0, 5, utf8_decode('Acuse de Registro'), 0, 1, 'L');
    $pdf->SetXY(80, $pdf->GetY() + 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 5, utf8_decode('Examen Diagnóstico  Nuevo Ingreso 2025'), 0, 1, 'L');


    $pdf->Line(25, $y_inicio_acuse + 22, 185, $y_inicio_acuse + 22);


    $pdf->SetXY(25, $y_inicio_acuse + 28);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
    $pdf->Cell(0, 6, utf8_decode("Hola, ".$_SESSION['Registro']['name']." ".$_SESSION['Registro']['last_name_P'])." ".$_SESSION['Registro']['last_name_M'], 0, 1, 'L');
    $pdf->Ln(14);

    // 3. Bloque de Datos en Dos Columnas (Distribución uniforme)
    $y_datos = $pdf->GetY();

    // --- FILA 1 ---
    $pdf->SetXY(25, $y_datos);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('No. de Boleta'), 0, 1, 'L');
    $pdf->SetX(25);
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['no_boleta']), 0, 1, 'L');

    $pdf->SetXY(110, $y_datos);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('CURP'), 0, 1, 'L');
    $pdf->SetXY(110, $pdf->GetY());
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['curp']), 0, 1, 'L');

    $pdf->Ln(18); 
    $y_fila2 = $pdf->GetY();

    // --- FILA 2 ---
    $pdf->SetXY(25, $y_fila2);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('Fecha de Nacimiento'), 0, 1, 'L');
    $pdf->SetX(25);
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['birth_date']), 0, 1, 'L');

    $pdf->SetXY(110, $y_fila2);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('Género'), 0, 1, 'L');
    $pdf->SetXY(110, $pdf->GetY());
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['genero']), 0, 1, 'L');

    $pdf->Ln(18); 
    $y_fila3 = $pdf->GetY();

    // --- FILA 3 ---
    $pdf->SetXY(25, $y_fila3);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('Estado de Origen'), 0, 1, 'L');
    $pdf->SetX(25);
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['state']), 0, 1, 'L');

    $pdf->SetXY(110, $y_fila3);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('Escuela de Procedencia'), 0, 1, 'L');
    $pdf->SetXY(110, $pdf->GetY());
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['school']), 0, 1, 'L');

    $pdf->Ln(18); 
    $y_fila4 = $pdf->GetY();

    // --- FILA 4 ---
    $pdf->SetXY(25, $y_fila4);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('Promedio'), 0, 1, 'L');
    $pdf->SetX(25);
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['promedio']), 0, 1, 'L');

    $pdf->SetXY(110, $y_fila4);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(0, 4, utf8_decode('Correo Institucional'), 0, 1, 'L');
    $pdf->SetXY(110, $pdf->GetY());
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_negro_txt[0], $color_negro_txt[1], $color_negro_txt[2]);
    $pdf->Cell(0, 6, utf8_decode($_SESSION['Registro']['correo']), 0, 1, 'L');

    // Pintamos el contenedor gris exterior
    $pdf->SetDrawColor(215, 220, 225);
    $pdf->Rect(15, $y_inicio_acuse, 180, $alto_recuadro_50);


    // ======================================================================
    // CUADRO NARANJA: ABAJO Y FUERA DEL RECUADRO DE INFORMACIÓN
    // ======================================================================
    // Se desplaza dinámicamente según el nuevo $y_inicio_acuse
    $y_caja_examen = $y_inicio_acuse + $alto_recuadro_50 + 6;

    $pdf->SetDrawColor($color_naranja[0], $color_naranja[1], $color_naranja[2]);
    $pdf->SetLineWidth(0.4);
    $pdf->Rect(15, $y_caja_examen, 180, 26);

    // Título interno de la caja examen
    $pdf->SetXY(20, $y_caja_examen + 3);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor($color_naranja[0], $color_naranja[1], $color_naranja[2]);
    $pdf->Cell(0, 4, utf8_decode('ASIGNACIÓN DE EXAMEN DIAGNÓSTICO'), 0, 1, 'L');

    // Datos en tres columnas
    $y_detalles_examen = $pdf->GetY() + 2;

    // Sub-columna 2: Laboratorio
    $pdf->SetXY(20, $y_detalles_examen);
    $pdf->SetFont('Arial', '', 8); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(35, 4, utf8_decode('Laboratorio'), 0, 1, 'L');
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 11); $pdf->SetTextColor($color_guinda[0], $color_guinda[1], $color_guinda[2]);
    $pdf->Cell(35, 5, utf8_decode($_SESSION['Registro']['laboratorio']), 0, 0, 'L');

    // Sub-columna 3: Horario
    $pdf->SetXY(115, $y_detalles_examen);
    $pdf->SetFont('Arial', '', 8); $pdf->SetTextColor($color_gris_label[0], $color_gris_label[1], $color_gris_label[2]);
    $pdf->Cell(60, 4, utf8_decode('Horario'), 0, 1, 'L');
    $pdf->SetX(115);
    $pdf->SetFont('Arial', 'B', 10); $pdf->SetTextColor($color_naranja[0], $color_naranja[1], $color_naranja[2]);
    $pdf->Cell(60, 5, utf8_decode($_SESSION['Registro']['fecha'].' · '.$_SESSION['Registro']['hora_ini']).' a '.$_SESSION['Registro']['hora_fin'], 0, 1, 'L');

    // 4. Renderizar y enviar el archivo PDF al navegador
    $pdf->SetTitle($_SESSION['Registro']['no_boleta']."_EXDG2026.pdf");
    $pdf->Output();
} else {
    header("Location: ../../Front/Home_page/index.html");
    session_destroy();
    exit();
}

?>