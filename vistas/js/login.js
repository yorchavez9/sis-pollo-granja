$(document).ready(function () {
  $("#button_submit_login").click(function (event) {
    // Previene el envío del formulario de inmediato
    event.preventDefault();

    let ingUsuario = $("#ingUsuario").val();
    let ingPassword = $("#ingPassword").val();

    // Limpia los mensajes de error previos
    $("#errorIngUsuario").empty();
    $("#errorIngPassword").empty();

    let valido = true;

    // Validación del campo "ingUsuario"
    if (ingUsuario === "") {
      $("#errorIngUsuario")
        .text("Por favor, ingrese su correo o usuario")
        .css("color", "red");
      valido = false;
    }

    // Validación del campo "ingPassword"
    if (ingPassword === "") {
      $("#errorIngPassword")
        .text("Por favor, ingrese su contraseña")
        .css("color", "red");
      valido = false;
    }

    // Si todo es válido, se envía el formulario
    if (valido) {
      $("#login_form").submit();
    }
  });
});
