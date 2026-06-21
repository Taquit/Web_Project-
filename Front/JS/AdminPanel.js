$(document).ready(() => {

  // ══════════════════════════════════════
  // Menú hamburguesa
  // ══════════════════════════════════════
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

  // ══════════════════════════════════════
  // SVG reutilizables
  // ══════════════════════════════════════
  const SVG_CLOCK = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
  </svg>`;

  const SVG_EYE = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
  </svg>`;

  const SVG_PENCIL = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
  </svg>`;

  const SVG_TRASH = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
  </svg>`;

  // ══════════════════════════════════════
  // Construcción de una fila de la tabla
  // ══════════════════════════════════════
  function crearFila(alumno) {
    const horario = `${alumno.start_time} – ${alumno.end_time}`;
    const turno   = alumno.turno || "—";

    return `
      <tr>
        <td class="boleta">${alumno.no_boleta}</td>
        <td class="nombre">${alumno.name} ${alumno.last_name_P} ${alumno.last_name_M || ""}</td>
        <td class="email">${alumno.email || "—"}</td>
        <td class="entidad">${alumno.state_name || "—"}</td>
        <td class="lab">${alumno.lab_name || "—"}</td>
        <td class="horario">
          <span>
            ${SVG_CLOCK}
            <span class="hora">${horario}</span>
          </span>
        </td>
        <td class="grupo"><span>${alumno.grupo || "—"}</span></td>
        <td class="acciones">
          <div>
            <button class="btn-ver" data-bs-toggle="modal" data-bs-target="#modalAlumno">${SVG_EYE}</button>
            <button class="btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditar">${SVG_PENCIL}</button>
            <button class="btn-eliminar" data-bs-toggle="modal" data-bs-target="#modalEliminar">${SVG_TRASH}</button>
          </div>
        </td>
      </tr>`;
  }

  // ══════════════════════════════════════
  // Actualización de estadísticas
  // ══════════════════════════════════════
  function actualizarEstadisticas(alumnos) {
    const total      = alumnos.length;
    const matutinos  = alumnos.filter(a => a.turno === "Matutino").length;
    const vespertinos = alumnos.filter(a => a.turno === "Vespertino").length;
    const labs       = new Set(alumnos.map(a => a.lab_name).filter(Boolean)).size;

    $("#stat-total").text(total);
    $("#stat-matutino").text(matutinos);
    $("#stat-vespertino").text(vespertinos);
    $("#stat-labs").text(labs);
    $("#subtitulo-panel").text(`Gestión de alumnos registrados — ${total} registro(s)`);
  }

  // ══════════════════════════════════════
  // Carga de datos desde el backend
  // ══════════════════════════════════════
  function cargarAlumnos() {
    $.ajax({
      url: "/Proyecto_Web/Web_Project-/Back/Controllers/GetAlumnos.php",
      method: "GET",
      dataType: "json",
      success: (data) => {
        const tbody = $("#tabla-body");
        tbody.empty();

        if (!data || data.length === 0) {
          tbody.html(`
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No hay registros disponibles.</td>
            </tr>`);
          actualizarEstadisticas([]);
          return;
        }

        data.forEach(alumno => tbody.append(crearFila(alumno)));
        actualizarEstadisticas(data);
      },
      error: () => {
        $("#tabla-body").html(`
          <tr>
            <td colspan="8" class="text-center text-danger py-4">Error al cargar los datos. Intenta de nuevo.</td>
          </tr>`);
      }
    });
  }

  cargarAlumnos();

  // ══════════════════════════════════════
  // Búsqueda en tiempo real
  // ══════════════════════════════════════
  $("#buscar").on("input", (e) => {
    const valorBusqueda = $(e.target).val().toLowerCase().trim();

    $("#tabla-body tr").each((index, fila) => {
      const boleta = $(fila).find(".boleta").text().toLowerCase();
      const nombre = $(fila).find(".nombre").text().toLowerCase();

      if (boleta.indexOf(valorBusqueda) !== -1 || nombre.indexOf(valorBusqueda) !== -1) {
        $(fila).show();
      } else {
        $(fila).hide();
      }
    });
  });

  // ══════════════════════════════════════
  // Modal Ver alumno
  // ══════════════════════════════════════
  $("#tabla-body").on("click", ".btn-ver", function () {
    const fila = $(this).closest("tr");

    const nombreCompleto = fila.find(".nombre").text().trim();
    const partes         = nombreCompleto.split(" ");
    const nombre         = partes[0] || "";
    const apellidos      = partes.slice(1).join(" ") || "N/A";
    const grupo          = fila.find(".grupo span").text().trim();

    $("#m-boleta").text(fila.find(".boleta").text().trim());
    $("#m-nombre").text(nombre);
    $("#m-apellidos").text(apellidos);
    $("#m-email").text(fila.find(".email").text().trim());
    $("#m-entidad").text(fila.find(".entidad").text().trim());
    $("#m-lab").text(fila.find(".lab").text().trim());
    $("#m-horario").text(fila.find(".hora").text().trim());
    $("#m-grupo").text(grupo);
    $("#m-turno").text(fila.find(".grupo span").text().trim().includes("A") || fila.find(".grupo span").text().trim().includes("B") ? "Matutino" : "Vespertino");
  });

  // ══════════════════════════════════════
  // Modal Editar alumno
  // ══════════════════════════════════════
  $("#tabla-body").on("click", ".btn-editar", function () {
    const fila = $(this).closest("tr");

    const nombreCompleto = fila.find(".nombre").text().trim();
    const partes         = nombreCompleto.split(" ");
    const nombre         = partes[0] || "";
    const apellidos      = partes.slice(1).join(" ");
    const grupo          = fila.find(".grupo span").text().trim();
    const horario        = fila.find(".hora").text().trim().replace(/\s+/g, "");
    const turno          = (grupo.includes("A") || grupo.includes("B")) ? "Matutino" : "Vespertino";

    $("#e-boleta").val(fila.find(".boleta").text().trim());
    $("#e-nombre").val(nombre);
    $("#e-apellidos").val(apellidos);
    $("#e-email").val(fila.find(".email").text().trim());
    $("#e-entidad").val(fila.find(".entidad").text().trim());
    $("#e-lab").val(fila.find(".lab").text().trim());
    $("#e-horario").val(horario);
    $("#e-grupo").val(grupo);
    $("#e-turno").val(turno);
  });

  // ══════════════════════════════════════
  // Modal Eliminar alumno
  // ══════════════════════════════════════
  $("#tabla-body").on("click", ".btn-eliminar", function () {
    const fila           = $(this).closest("tr");
    const boleta         = fila.find(".boleta").text().trim();
    const nombreCompleto = fila.find(".nombre").text().trim();

    $("#del-nombre").text(nombreCompleto);
    $("#del-boleta").text(boleta);

    $("#btnConfirmarEliminar").off("click").on("click", () => {
      // TODO: conectar con endpoint DELETE del backend
    });
  });

  // ══════════════════════════════════════
  // Limpiar modal Nuevo alumno al abrir
  // ══════════════════════════════════════
  $("#modalNuevo").on("show.bs.modal", () => {
    $("#formNuevoAlumno")[0].reset();
  });

});
