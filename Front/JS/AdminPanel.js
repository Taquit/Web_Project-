$(document).ready(() => {

  // ══════════════════════════════════════
  // Menú hamburguesa
  // ══════════════════════════════════════
  $("#menu").click(() => {
    $("#pantalla").toggleClass("pantalla-moderna");
    $("ul").addClass("activo");
  });

  $("#cerrar, #pantalla").click(() => {
    $("#pantalla").removeClass("pantalla-moderna");
    $("ul").removeClass("activo");
  });

  // ══════════════════════════════════════
  // SVG reutilizables
  // ══════════════════════════════════════
  const SVG_CLOCK = `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
  </svg>`;

  const SVG_EYE = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
  </svg>`;

  const SVG_PENCIL = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
  </svg>`;

  const SVG_TRASH = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
  </svg>`;

  // ══════════════════════════════════════
  // Helpers para badges de la tabla
  // ══════════════════════════════════════
  function badgeAsignacion(valor, clase) {
    if (!valor || valor === "N/A") {
      return `<span class="badge-pendiente">Por asignar</span>`;
    }
    return `<span class="${clase}">${valor}</span>`;
  }

  // ══════════════════════════════════════
  // Construcción de una fila de la tabla
  // ══════════════════════════════════════
  function crearFila(alumno) {
    const nombreCompleto = `${alumno.name} ${alumno.last_name_P} ${alumno.last_name_M || ""}`.trim();
    const escuela        = alumno.escuela_catalogo || alumno.other_school || "N/A";
    const horario        = alumno.horario  || null;
    const laboratorio    = alumno.laboratorio || null;

    return `
      <tr>
        <td class="boleta">${alumno.no_boleta}</td>
        <td class="nombre">${nombreCompleto}</td>
        <td class="curp-cell">${alumno.curp || "—"}</td>
        <td class="email">${alumno.email || "—"}</td>
        <td class="fecha">${alumno.birth_date || "—"}</td>
        <td class="genero">${alumno.gender || "—"}</td>
        <td class="entidad">${alumno.estado || "—"}</td>
        <td class="escuela-cell">${escuela}</td>
        <td class="lab">${badgeAsignacion(laboratorio, "badge-lab")}</td>
        <td class="horario-cell">
          ${horario
            ? `<span class="badge-horario">${SVG_CLOCK} <span class="hora">${horario}</span></span>`
            : `<span class="badge-pendiente">Por asignar</span>`}
        </td>
        <td class="acciones">
          <div class="acciones-wrap">
            <button class="btn-accion btn-ver"
                    data-bs-toggle="modal" data-bs-target="#modalAlumno"
                    title="Ver detalle">
              ${SVG_EYE} <span>Ver</span>
            </button>
            <button class="btn-accion btn-editar"
                    data-bs-toggle="modal" data-bs-target="#modalEditar"
                    title="Editar registro">
              ${SVG_PENCIL} <span>Editar</span>
            </button>
            <button class="btn-accion btn-eliminar"
                    data-bs-toggle="modal" data-bs-target="#modalEliminar"
                    title="Eliminar registro">
              ${SVG_TRASH} <span>Borrar</span>
            </button>
          </div>
        </td>
      </tr>`;
  }

  // ══════════════════════════════════════
  // Actualización de estadísticas
  // ══════════════════════════════════════
  function actualizarEstadisticas(alumnos) {
    const total      = alumnos.length;
    const asignados  = alumnos.filter(a => a.laboratorio && a.laboratorio !== "N/A").length;
    const pendientes = total - asignados;
    const labs       = new Set(alumnos.map(a => a.laboratorio).filter(v => v && v !== "N/A")).size;

    $("#stat-total").text(total);
    $("#stat-labs").text(labs || "—");
    $("#stat-asignados").text(asignados);
    $("#stat-pendientes").text(pendientes);
    $("#subtitulo-panel").text(`Gestión de alumnos registrados — ${total} registro(s)`);
  }

  // ══════════════════════════════════════
  // Carga de datos desde el backend
  // ══════════════════════════════════════
  function cargarAlumnos() {
    $.ajax({
      url: "../../Back/Controllers/StudentController.php",
      method: "GET",
      dataType: "json",
      success(alumnos) {
        const $tbody = $("#tabla-body");
        $tbody.empty();

        if (!alumnos || alumnos.length === 0) {
          $tbody.html(`<tr><td colspan="10" class="vacio">No hay registros disponibles.</td></tr>`);
          actualizarEstadisticas([]);
          return;
        }

        alumnos.forEach(alumno => $tbody.append(crearFila(alumno)));
        actualizarEstadisticas(alumnos);
      },
      error(_xhr, _status, err) {
        $("#tabla-body").html(
          `<tr><td colspan="10" class="vacio vacio--error">
            Error al cargar los datos. Intenta de nuevo.<br>
            <small>${err}</small>
          </td></tr>`
        );
      }
    });
  }

  cargarAlumnos();

  // ══════════════════════════════════════
  // Búsqueda en tiempo real
  // ══════════════════════════════════════
  $("#buscar").on("input", function () {
    const q = $(this).val().toLowerCase().trim();

    $("#tabla-body tr").each(function () {
      const boleta = $(this).find(".boleta").text().toLowerCase();
      const nombre = $(this).find(".nombre").text().toLowerCase();
      $(this).toggle(boleta.includes(q) || nombre.includes(q));
    });
  });

  // ══════════════════════════════════════
  // Variable compartida para el alumno activo en los modales
  // ══════════════════════════════════════
  let alumnoActivo = { boleta: "", nombre: "" };

  // ══════════════════════════════════════
  // Modal — Ver alumno
  // ══════════════════════════════════════
  $("#tabla-body").on("click", ".btn-ver", function () {
    const $fila          = $(this).closest("tr");
    const nombreCompleto = $fila.find(".nombre").text().trim();
    const partes         = nombreCompleto.split(" ");

    // Guardar en variable externa para que el flujo a modalEliminarCompleto funcione
    alumnoActivo.boleta = $fila.find(".boleta").text().trim();
    alumnoActivo.nombre = nombreCompleto;

    $("#m-boleta").text(alumnoActivo.boleta);
    $("#m-nombre").text(partes[0] || "");
    $("#m-apellidos").text(partes.slice(1).join(" ") || "N/A");
    $("#m-email").text($fila.find(".email").text().trim());
    $("#m-entidad").text($fila.find(".entidad").text().trim());
    $("#m-lab").text($fila.find(".lab").text().trim());
    $("#m-horario").text($fila.find(".hora").text().trim() || "Por asignar");
  });

  // ══════════════════════════════════════
  // Modal — Editar alumno
  // ══════════════════════════════════════
  $("#tabla-body").on("click", ".btn-editar", function () {
    const $fila          = $(this).closest("tr");
    const nombreCompleto = $fila.find(".nombre").text().trim();
    const partes         = nombreCompleto.split(" ");
    const horario        = $fila.find(".hora").text().trim().replace(/\s+/g, "");

    $("#e-boleta").val($fila.find(".boleta").text().trim());
    $("#e-nombre").val(partes[0] || "");
    $("#e-apellidos").val(partes.slice(1).join(" "));
    $("#e-email").val($fila.find(".email").text().trim());
    $("#e-entidad").val($fila.find(".entidad").text().trim());
    $("#e-lab").val($fila.find(".lab").text().trim());
    $("#e-horario").val(horario);
  });

  // ══════════════════════════════════════
  // Enviar formulario Editar → backend
  // ══════════════════════════════════════
  $("#formEditarAlumno").on("submit", function (e) {
    e.preventDefault();

    const payload = {
      no_boleta:   $("#e-boleta").val().trim(),
      name:        $("#e-nombre").val().trim(),
      last_name_P: $("#e-apellidos").val().trim().split(" ")[0] || "",
      last_name_M: $("#e-apellidos").val().trim().split(" ").slice(1).join(" ") || "",
      email:       $("#e-email").val().trim(),
      gender:      $("#e-genero").val ? $("#e-genero").val() : "",
      birth_date:  $("#e-nacimiento").val ? $("#e-nacimiento").val() : ""
    };

    $.ajax({
      url: "../../Back/Controllers/UpdateStudent.php",
      method: "POST",
      data: payload,
      dataType: "json",
      success(resp) {
        if (resp.error) {
          alert("Error: " + resp.mensaje);
          return;
        }
        bootstrap.Modal.getInstance(document.getElementById("modalEditar")).hide();
        cargarAlumnos();
      },
      error() {
        alert("No se pudo conectar con el servidor. Intenta de nuevo.");
      }
    });
  });

  // ══════════════════════════════════════
  // Modal — Eliminar asignación (horario/lab)
  // ══════════════════════════════════════
  $("#tabla-body").on("click", ".btn-eliminar", function () {
    const $fila  = $(this).closest("tr");
    const boleta = $fila.find(".boleta").text().trim();
    const nombre = $fila.find(".nombre").text().trim();

    $("#del-nombre").text(nombre);
    $("#del-boleta").text(boleta);

    $("#btnConfirmarEliminar").off("click").on("click", function () {
      const $btn = $(this);
      $btn.prop("disabled", true).html(`
        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
        Eliminando…
      `);

      $.ajax({
        url: "../../Back/Controllers/DeleteStudent.php",
        method: "POST",
        data: { no_boleta: boleta },
        dataType: "json",
        success(resp) {
          $btn.prop("disabled", false).html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
              <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
            </svg>
            Eliminar
          `);

          if (resp.error) {
            alert("Error: " + resp.mensaje);
            return;
          }

          bootstrap.Modal.getInstance(document.getElementById("modalEliminar")).hide();
          cargarAlumnos();
        },
        error() {
          $btn.prop("disabled", false).text("Eliminar");
          alert("No se pudo conectar con el servidor. Intenta de nuevo.");
        }
      });
    });
  });

  // ══════════════════════════════════════
  // Modal — Eliminar estudiante completo
  // ══════════════════════════════════════
  // Se abre desde el botón en el footer de modalAlumno.
  // Cuando modalEliminarCompleto se muestra, lee alumnoActivo.
  $("#modalEliminarCompleto").on("show.bs.modal", function () {
    $("#del-completo-nombre").text(alumnoActivo.nombre || "—");
  });

  $("#btnEliminarCompleto").on("click", function () {
    const boleta = alumnoActivo.boleta;

    if (!boleta) {
      alert("No se pudo identificar al estudiante. Intenta de nuevo.");
      return;
    }

    // Deshabilitar botón para evitar doble click
    $(this).prop("disabled", true).text("Eliminando…");

    $.ajax({
      url: "../../Back/Controllers/DeleteStudent.php",
      method: "POST",
      data: { no_boleta: boleta },
      dataType: "json",
      success: (resp) => {
        if (resp.error) {
          alert("Error: " + resp.mensaje);
          $(this).prop("disabled", false).html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
              <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
            </svg>
            Sí, eliminar estudiante
          `);
          return;
        }
        alumnoActivo = { boleta: "", nombre: "" };
        bootstrap.Modal.getInstance(document.getElementById("modalEliminarCompleto")).hide();
        cargarAlumnos();
      },
      error: () => {
        alert("No se pudo conectar con el servidor. Intenta de nuevo.");
        $(this).prop("disabled", false);
      }
    });
  });

  // ══════════════════════════════════════
  // Limpiar formulario al abrir modal Nuevo
  // ══════════════════════════════════════
  $("#modalNuevo").on("show.bs.modal", () => {
    $("#formNuevoAlumno")[0].reset();
  });

});
