// Flag para saber si ya fue validado y solo falta enviar
var formularioValidado = false;

function fmt(fechaISO) {
  if (!fechaISO) return "—";
  var p = fechaISO.split("-");
  return p[2] + "/" + p[1] + "/" + p[0];
}


function obtenerPrimeraVocalInterna(palabra) {
  const vocales = "aeiouáéíóúAEIOUÁÉÍÓÚ";
  for (let i = 1; i < palabra.length; i++) {
    if (vocales.includes(palabra[i])) {
      return palabra[i].toUpperCase();
    }
  }
  return "X"; 
}


function obtenerConsonante(palabra) {
  const vocales = "aeiouáéíóúAEIOUÁÉÍÓÚ";
  
  for (let i = 1; i < palabra.length; i++) {
    let letra = palabra[i];
    // Comprobar que sea una letra y que NO sea una vocal
    if (/[a-zA-ZñÑ]/.test(letra) && !vocales.includes(letra)) {
      return letra.toUpperCase();
    }
  }

  return "X";
}

function mostrarResumen() {
  const nombre    = document.forms.formu.nombre.value;
  const apPat     = document.forms.formu.apellidopaterno.value;
  const apMat     = document.forms.formu.apellidomaterno.value;
  const boleta    = document.forms.formu.boleta.value;
  const curp      = document.forms.formu.CURP.value;
  const tel       = document.forms.formu.telefono.value;
  const fecha     = document.forms.formu["fecha-nacimiento"].value;
  const correo    = document.forms.formu.correo.value;
  const promedio  = document.forms.formu.promedio.value;
  const genero    = document.querySelector("input[name='genero']:checked")?.value || "—";
  const estadoSel = document.getElementById("estado-origen");
  const estado    = estadoSel.options[estadoSel.selectedIndex].text;
  const escuelaSel = document.getElementById("escuela-procedencia");
  const escuela   = escuelaSel.options[escuelaSel.selectedIndex].text;

  document.getElementById("modal-saludo").textContent = `Hola, ${nombre} ${apPat}`;

  document.getElementById("modal-datos").innerHTML = `
    <p><strong>Boleta:</strong> ${boleta}</p>
    <p><strong>Nombre:</strong> ${nombre} ${apPat} ${apMat}</p>
    <p><strong>Fecha de nacimiento:</strong> ${fmt(fecha)}</p>
    <p><strong>CURP:</strong> ${curp}</p>
    <p><strong>Teléfono:</strong> ${tel}</p>
    <p><strong>Género:</strong> ${genero}</p>
    <p><strong>Estado de origen:</strong> ${estado}</p>
    <p><strong>Escuela de procedencia:</strong> ${escuela}</p>
    <p><strong>Promedio:</strong> ${promedio}</p>
    <p><strong>Correo:</strong> ${correo}</p>
  `;

  document.getElementById("modal-resumen").style.display = "flex";
}
function cerrarModal() {
  document.getElementById("modal-resumen").style.display = "none";
}

function confirmarEnvio() {
  formularioValidado = true;
  document.getElementById("formu").submit();
}

function validarFormulario() {
  // Si ya fue validado (viene de confirmarEnvio), dejar pasar el submit
  if (formularioValidado) return true;

  var errores = [];

  var p = document.forms.formu.boleta.value;
  if (!/^\d{10}$|^[A-Z]{2}\d{8}$/.test(p))
    errores.push("Número de boleta no válido. Debe contener exactamente 10 dígitos.");

  var nom = document.forms.formu.nombre.value;
  if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nom))
    errores.push("Nombre no válido. Solo se permiten letras y espacios.");

  var app = document.forms.formu.apellidopaterno.value;
  if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(app))
    errores.push("Apellido paterno no válido. Solo se permiten letras y espacios.");

  var apm = document.forms.formu.apellidomaterno.value;
  if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apm))
    errores.push("Apellido materno no válido. Solo se permiten letras y espacios.");

  var fecha = document.forms.formu["fecha-nacimiento"].value;
  if (!fecha) {
    errores.push("Fecha de nacimiento requerida.");
  } else {
    var anioNacimiento = parseInt(fecha.split("-")[0], 10);
    if (anioNacimiento < 2000 || anioNacimiento > 2009) {
      errores.push("Fecha de nacimiento no válida. Debes haber nacido entre el año 2000 y el 2009.");
    }
  }

  var genero = document.querySelector("input[name='genero']:checked");
  if (!genero)
    errores.push("Selecciona un género.");

  var estadoNumerico = document.forms.formu["estado-origen"].value;
  const mapaEstadosCurp = {
    "1":  "AS", "2":  "BC", "3":  "BS", "4":  "CC", "5":  "CS", "6":  "CH",
    "7":  "DF", "8":  "CL", "9":  "CM", "10": "DG", "11": "MC", "12": "GT",
    "13": "GR", "14": "HG", "15": "JC", "16": "MN", "17": "MS", "18": "NT",
    "19": "NL", "20": "OC", "21": "PL", "22": "QT", "23": "QR", "24": "SP",
    "25": "SL", "26": "SR", "27": "TC", "28": "TS", "29": "TL", "30": "VZ",
    "31": "YN", "32": "ZS"
  };
  var estado = mapaEstadosCurp[estadoNumerico] || "";

  if (!estado)
    errores.push("Selecciona un estado de origen válido.");

  if (errores.length === 0 && fecha && genero) {
    var anio2  = fecha.substring(2, 4);
    var mes    = fecha.substring(5, 7);
    var dia    = fecha.substring(8, 10);

    const partenom        = nom.trim().charAt(0).toUpperCase();
    const appLimpio       = app.trim();
    const primeraLetraApp = appLimpio.charAt(0).toUpperCase();
    const primeraVocalApp = obtenerPrimeraVocalInterna(appLimpio);
    
    const primerosdosapp  = primeraLetraApp + primeraVocalApp;
    const parteapm        = apm.trim().charAt(0).toUpperCase();
    const fechaNacimiento = anio2 + mes + dia;
    const inicialgenero   = genero.value.charAt(0).toUpperCase();

    const consonanteApp = obtenerConsonante(app.trim());
    const consonanteApm = obtenerConsonante(apm.trim());
    const consonanteNom = obtenerConsonante(nom.trim());

    var curp = document.forms.formu.CURP.value;
    const curpGenerada = new RegExp(
      `^${primerosdosapp}${parteapm}${partenom}${fechaNacimiento}${inicialgenero}${estado}${consonanteApp}${consonanteApm}${consonanteNom}[A-Z0-9][0-9]$`
    );

    console.log("Patrón CURP esperado:", curpGenerada.source);
    console.log("CURP ingresada:", curp);

    if (!curpGenerada.test(curp))
      errores.push("Tu CURP no coincide con los datos ingresados.");
  }

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

  if (errores.length > 0) {
    alert(errores.join("\n\n"));
    return false;
  }

  mostrarResumen();
  return false;
}