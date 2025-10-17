document.getElementById('backButton').addEventListener('click', function() {
    window.history.back();
});

document.getElementById('email').addEventListener('click',function(){
    var email = 'diegorojas9051@gmail.com'; // Reemplaza con el correo electrónico del destinatario
    var subject = 'Tiempo de mantenimiento'; // Reemplaza con el asunto del correo
    var body = 'Deseo conocer el tiempo que estimara el mantenimiento del siguiente apartado: "Ingrese el nombre del apartado"'; // Reemplaza con el cuerpo del correo

    var mailtoLink = 'mailto:' + email + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);

    window.location.href = mailtoLink;
});