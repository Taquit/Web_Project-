function fmt(fechaISO) {
    if (!fechaISO) return "—";
    var p = fechaISO.split("-");
    return p[2] + "/" + p[1] + "/" + p[0];
  }

function obtenerConsonante(palabra) {
    const vocales = "aeiouáéíóúAEIOUÁÉÍÓÚ";
    const consonantes = [];


    for (let i = 0; i < palabra.length; i++) {
        let letra = palabra[i];
        if (/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/.test(letra) && !vocales.includes(letra)) {
            consonantes.push(letra);
        }
    }

    
    if (vocales.includes(palabra[0])) {
        return consonantes[0].toUpperCase(); 
    } else {
        return consonantes[1].toUpperCase(); 
    }
}

  function validarFormulario() {
    var errores = [];

    var p = document.forms.formu.numbol.value;
    if (!/^\d{10}$|^[A-Z]{2}\d{8}$/.test(p))
      errores.push("Número de boleta no válido. Debe contener exactamente 10 dígitos.");

    var nom = document.forms.formu.nombre.value;
    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nom))
      errores.push("Nombre no válido. Solo se permiten letras y espacios.");

  
    var app = document.forms.formu.apellidopaterno.value;
    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(app))
      errores.push("Apellido no válido. Solo se permiten letras y espacios.");

    var apm = document.forms.formu.apellidomaterno.value;
    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apm))
      errores.push("Apellido no válido. Solo se permiten letras y espacios.");

    var fecha = document.forms.formu["fecha-nacimiento"].value;
    
    var anio2 = fecha.substring (2, 4);
    var mes = fecha.substring(5, 7);
    var dia = fecha.substring(8, 10);

    var genero = document.querySelector("input[name='genero']:checked");
    var estado = document.forms.formu["estado-origen"].value;
    


    //Aquí se obtienen el primer caracter del nombre y del apellido materno, mientras que del apellido paterno se obtienen los dos primeros caracteres
    const partenom = nom.charAt(0).toUpperCase();
    const primerosdosapp = app.substr(0, 2).toUpperCase();
    const parteapm = apm.charAt(0).toUpperCase();
    const fechanaciemiento = anio2 + mes + dia;
    const inicialgenero = genero.value.charAt(0).toUpperCase();

    const consonanteApp = obtenerConsonante(app);
    const consonanteApm = obtenerConsonante(apm);
    const consonanteNom = obtenerConsonante(nom);

    console.log(primerosdosapp, parteapm, partenom, fechanaciemiento, inicialgenero, estado, consonanteApp, consonanteApm, consonanteNom);







   
    var curp = document.forms.formu.CURP.value;
    const curpGenerada = new RegExp(`^${primerosdosapp}${parteapm}${partenom}${fechanaciemiento}${inicialgenero}${estado}${consonanteApp}${consonanteApm}${consonanteNom}[A-Z0-9][0-9]$`);
    if (!curpGenerada.test(curp))
      errores.push("Tu CURP no coincide con los datos ingresados.");

    var tel = document.forms.formu.telefono.value;
    if (!/^\d{10}$/.test(tel))
      errores.push("Teléfono no válido. Debe tener exactamente 10 dígitos.");

    var correo = document.forms.formu.correo.value;
    if (!/^[a-zA-Z]+[0-9]{4}@alumno\.ipn\.mx$/.test(correo))
      errores.push("Correo no válido. Debe ser correo institucional (@alumno.ipn.mx).");

    var contrasena = document.forms.formu.contrasena.value;
    if (!/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>_-])[A-Za-z\d!@#$%^&*(),.?":{}|<>_-]{6,}$/.test(contrasena))
      errores.push("Contraseña no válida. Mínimo 6 caracteres, una mayúscula, un número y un carácter especial.");

    var prom = parseFloat(document.forms.formu.promedio.value);
    if (isNaN(prom) || prom < 6 || prom > 10)
      errores.push("Promedio no válido. Debe estar entre 6.0 y 10.0.");

    if (errores.length > 0) { alert(errores.join("\n\n")); return false; }

    mostrarResumen();
    return false;
  }

  function mostrarResumen() {
    var f = document.forms.formu;
    var nombre   = f.nombre.value.trim();
    var boleta   = f.numbol.value.trim();
    var fecha    = fmt(f["fecha-nacimiento"].value);
    var curp     = f.CURP.value.trim();
    var telefono = f.telefono.value.trim();
    var genero   = (document.querySelector("input[name='genero']:checked") || {}).value || "No seleccionado";
    var estadoSel  = document.getElementById("estado-origen");
    var estado     = estadoSel.options[estadoSel.selectedIndex].text;
    var escuelaSel = document.getElementById("escuela-procedencia");
    var escuela    = escuelaSel.options[escuelaSel.selectedIndex].text;
    if (escuelaSel.value === "22") escuela = "Otra: " + f["nombre-escuela"].value;
    var promedio   = f.promedio.value;
    var correo     = f.correo.value.trim();
    var contrasena = f.contrasena.value;

    document.getElementById("modal-saludo").textContent =
      "Hola " + nombre + ", verifica que los datos que ingresaste sean correctos:";

    document.getElementById("modal-datos").innerHTML = `
      <div class="seccion-titulo">Datos Personales</div>
      <div class="dato-fila"><span class="dato-label">No. de Boleta</span><span class="dato-valor">${boleta}</span></div>
      <div class="dato-fila"><span class="dato-label">Nombre Completo</span><span class="dato-valor">${nombre}</span></div>
      <div class="dato-fila"><span class="dato-label">Fecha de Nacimiento</span><span class="dato-valor">${fecha}</span></div>
      <div class="dato-fila"><span class="dato-label">CURP</span><span class="dato-valor">${curp}</span></div>
      <div class="dato-fila"><span class="dato-label">Teléfono</span><span class="dato-valor">${telefono}</span></div>
      <div class="dato-fila"><span class="dato-label">Género</span><span class="dato-valor">${genero}</span></div>
      <div class="dato-fila"><span class="dato-label">Estado de Origen</span><span class="dato-valor">${estado}</span></div>
      <div class="seccion-titulo">Escuela de Procedencia</div>
      <div class="dato-fila"><span class="dato-label">Escuela</span><span class="dato-valor">${escuela}</span></div>
      <div class="dato-fila"><span class="dato-label">Promedio</span><span class="dato-valor">${promedio}</span></div>
      <div class="seccion-titulo">Datos de la Cuenta</div>
      <div class="dato-fila"><span class="dato-label">Correo</span><span class="dato-valor">${correo}</span></div>
      <div class="dato-fila"><span class="dato-label">Contraseña</span><span class="dato-valor oculta">${"●".repeat(contrasena.length)}</span></div>
    `;

    document.getElementById("modal-resumen").classList.add("visible");
  }

  function cerrarModal() { document.getElementById("modal-resumen").classList.remove("visible"); }
  function confirmarEnvio() {
      cerrarModal();
      var f = document.getElementById("formu");
      f.submit();            
  }
  $(document).ready(function () {
    $('#escuela-procedencia').change(function () {
      if ($(this).val() === "22") {
        $('#contenedor-otra-escuela').fadeIn();
        $('#nombre-escuela').prop('required', true);
      } else {
        $('#contenedor-otra-escuela').fadeOut();
        $('#nombre-escuela').prop('required', false).val('');
      }
    });
  });