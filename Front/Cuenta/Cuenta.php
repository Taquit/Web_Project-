<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../Front/Account_page/Account.php");
    exit();
}

require_once '../../Back/Config/config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT 
                s.no_boleta, s.name, s.last_name_P, s.last_name_M, 
                s.birth_date, s.gender, s.curp_user AS curp, 
                s.avarage AS promedio,
                u.email_user AS correo,
                st.state_name AS estado,
                CASE WHEN s.id_school = 22 
                     THEN s.other_school_name 
                     ELSE sch.school_name 
                END AS escuela,
                l.name AS laboratorio,
                sc.exam_date AS fecha, 
                sc.start_time AS hora_ini, 
                sc.end_time AS hora_fin
              FROM Student s
              INNER JOIN User u ON s.id_user = u.id_user
              INNER JOIN State st ON s.id_state_origin = st.id_state
              LEFT JOIN School sch ON s.id_school = sch.id_school
              LEFT JOIN Allocation a ON s.no_boleta = a.no_boleta
              LEFT JOIN Schedule sc ON a.id_schedule = sc.id_schedule
              LEFT JOIN Lab l ON a.id_lab = l.id_lab
              WHERE u.id_user = :id_user LIMIT 1";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_user', $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt->execute();
    
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        echo "Error: No se encontraron los datos del estudiante.";
        exit();
    }
} catch (Exception $e) {
    echo "Error de conexión en el servidor: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../Assets/imgIndex/escom.webp" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <title>Cuenta</title>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="Cuenta.css">
    <script defer src="../JS/validarFormRegistro.js"></script>
    <script>
      $(document).ready(() => {
    
        $("#menu").click(() => {
          $("#pantalla").toggleClass("pantalla-moderna");
          $("ul").addClass("activo");
        });
        
        $("#cerrar").click(() => {
          $("#pantalla").removeClass("pantalla-moderna");
          $("ul").removeClass("activo");
        });

        $("#pantalla").click(() => {
          $("#pantalla").removeClass("pantalla-moderna");
          $("ul").removeClass("activo");
        });
      });
    </script>
</head>
<body class="p-0 m-0 border-0 bd-example">
    <header class="position-sticky">
      <div class="izq">
        <div class="header-imgs">
            <img class="ipn" src="../../Assets/imgIndex/ipn.webp" alt="IPN" title="Instituto Politécnico Nacional">
            <img class="escom" src="../../Assets/imgIndex/escom.webp" alt="ESCOM" title="ESCOM">
            <img class="fp" src="../../Assets/imgIndex/familiapev2.webp" alt="Logo del equipo 2" title="Logo del equipo 2">
        </div>
        <div class="titulo">
            <h4>IPN - ESCOM</h4>  
            <p>Sistema de Registro de Estudiantes</p>
        </div>
      </div>
        <button id="menu" type="button">☰</button>
        <div id="pantalla" class=""></div>
        <ul class="">
            <li class="asd"><button id="cerrar" type="button">✖</button></li>
            <a href="../Home_page/index.php"><li>Inicio</li></a>
            <a href="../Cuenta/Cuenta.php"><li>Cuenta</li></a>
            <a href="../../Back/Controllers/Logout.php"><li>Cerrar Sesión</li></a>
        </ul>
    </header>
    <main>
        <section id="info">
            <div class="acuse_registro">
                <div class="acuse_cabecera">
                    <img class="ipn1" src="../../Assets/imgIndex/ipn.webp" alt="IPN" title="Instituto Politécnico Nacional">
                    <img class="escom1" src="../../Assets/imgIndex/escom.webp" alt="ESCOM" title="ESCOM">
                    <div class="titulo-acuse">
                        <p class="acuse_titulo">Acuse de Registro</p>
                        <p class="acuse_texto">Examen Diagnóstico - Nuevo ingreso 2026</p>
                    </div>
                </div>
                <div class="acuse_cuerpo">
                    <p class="acuse_cuerpo_titulo">Hola, <?php echo htmlspecialchars($alumno['name'] . ' ' . $alumno['last_name_P'] . ' ' . $alumno['last_name_M']); ?></p>
                    <div class="acuse_contenido">
                        <div class="acuse_contenido_izquierda">
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">No. Boleta</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['no_boleta']); ?></p>
                            </div>
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">Fecha de Nacimiento</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['birth_date']); ?></p>
                            </div>
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">Entidad de Procedencia</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['estado']); ?></p>
                            </div>
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">Promedio</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['promedio']); ?></p>
                            </div>
                        </div>
                        <div class="acuse_contenido_derecha">
                             <div class="acuse_datos">
                                <p class="acuse_datos_texto">CURP</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['curp']); ?></p>
                            </div>
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">Género</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['gender']); ?></p>
                            </div>
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">Escuela de Procedencia</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['escuela']); ?></p>
                            </div>
                            <div class="acuse_datos">
                                <p class="acuse_datos_texto">Correo</p>
                                <p class="acuse_datos_bd"><?php echo htmlspecialchars($alumno['correo']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="acuse_asignacion">
                        <p class="acuse_asignacion_titulo">ASIGNACIÓN DE EXAMEN DIAGNÓSTICO</p>
                        <div class="acuse_asignacion_contenido">
                            <div class="acuse_asignacion_texto">
                                <p class="acuse_asignacion_subtitulo">Laboratorio</p> 
                                <p class="acuse_asignacion_dato"><?php echo htmlspecialchars($alumno['laboratorio'] ?? 'No asignado'); ?></p>
                            </div>
                            <div class="acuse_asignacion_texto">
                                <p class="acuse_asignacion_subtitulo">Horario</p>
                                <p class="acuse_asignacion_dato">
                                    <?php 
                                    if ($alumno['fecha']) {
                                        echo htmlspecialchars($alumno['fecha'] . ' · ' . $alumno['hora_ini'] . ' a ' . $alumno['hora_fin']);
                                    } else {
                                        echo 'Horario pendiente';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="acuse_asignacion_subtitulo">Presente este acuse el día del examen: Duración 90 min</p>
                </div>
            </div>
        </section>
        <section>
            <button type="button" class="button_acuse" onclick='generarPDF(<?php echo json_encode($alumno); ?>)'>
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5vw" height="auto" fill="currentColor" class="bi bi-printer" id="impresora" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                </svg>
               Imprimir acuse (PDF)
            </button>
        </section>
    </main>
    <footer class="text-center text-lg-start mt-auto py-4 border-top">
        <div class="container text-center">
            <span class="text-whited">© 2026 Instituto Politécnico Nacional - ESCOM - Equipo 2</span>
            <div class="mt-2">
                <a href="#">Registro de Datos Generales para Estudiantes de Nuevo Ingreso</a>
                <a href="#">Tecnologías para el Desarrollo de Aplicaciones Web</a>
            </div>
        </div>
    </footer>

    <script>
    function generarPDF(datos) {
        // Mapeo exacto de nombres de variables esperadas por el FPDF de Generator_PDF.php
        const mapeoPDF = {
            'no_boleta': datos.no_boleta,
            'name': datos.name,
            'last_name_P': datos.last_name_P,
            'last_name_M': datos.last_name_M,
            'birth_date': datos.birth_date,
            'genero': datos.gender,
            'curp': datos.curp,
            'promedio': datos.promedio,
            'correo': datos.correo,
            'state': datos.estado,
            'school': datos.escuela,
            'laboratorio': datos.laboratorio || 'No asignado',
            'fecha': datos.fecha || '',
            'hora_ini': datos.hora_ini || '',
            'hora_fin': datos.hora_fin || ''
        };

        // Crear formulario temporal oculto
        const form = document.createElement('form');
        form.method = 'POST';
        // Ajusta esta ruta si cambia de lugar tu Generator_PDF.php
        form.action = '../../Back/Controllers/Generator_PDF.php'; 
        form.target = '_blank'; // Abre el PDF en una pestaña nueva automáticamente

        // Adjuntar campos ocultos al formulario
        for (const clave in mapeoPDF) {
            if (mapeoPDF.hasOwnProperty(clave)) {
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = clave;
                hiddenField.value = mapeoPDF[clave];
                form.appendChild(hiddenField);
            }
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form); // Limpieza de DOM
    }
    </script>
</body>
</html>