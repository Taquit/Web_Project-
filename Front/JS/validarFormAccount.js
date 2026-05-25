function validarCorreoInstitucional(){
    var correo = document.forms.LoginEst.email.value;
    var rCorreoInstitucional = /^\w{5,}@alumno\.ipn\.mx$/;

    return rCorreoInstitucional.test(correo);        
}

function validarContraseña(){
    var password = document.forms.LoginEst.password.value;
    var rpassword = /^(?=.*[A-Z])(?=.*\d)(?=.*[%#$&/()?¿.:,;*\-+=_!<>\[\]{}])[A-Za-z\d%#$&/()?¿.:,;*\-+=_!<>\[\]{}]{6,}$/;

    return rpassword.test(password); 
}

function validar(){
    var bandCorreo = validarCorreoInstitucional();
    var bandPassw = validarContraseña();

    if(!bandCorreo && bandPassw){
        alert("Correo institucional inváido.");
        return false;
    } else if(!bandPassw && bandCorreo){
        alert("Contraseña inválida.\nAsegúrese que sea de mínimo de 6 caracteres, que incluya al menos una letra mayúscula, un número y un caracter especial.")
        return false;
    } else if(!bandPassw && !bandCorreo){
        alert("Correo institucional inváido.\nContraseña inválida. Asegúrese que sea de mínimo de 6 caracteres, que incluya al menos una letra mayúscula, un número y un caracter especial.");
        return false;
    } else {
        alert("Inicio de sesión exitoso.")
        return true;
    }
}