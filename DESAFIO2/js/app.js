$(document).ready(function () {
  cargar();
});

function mostrarToast(mensaje, tipo) {
  tipo = tipo || 'info';
  var colores = {
    exito: { borde: '#0F6E56', fondo: '#E1F5EE', texto: '#0F6E56', icono: '✓' },
    error: { borde: '#A32D2D', fondo: '#FCEBEB', texto: '#A32D2D', icono: '✗' },
    info:  { borde: '#185FA5', fondo: '#E6F1FB', texto: '#185FA5', icono: 'i' }
  };
  var c = colores[tipo] || colores.info;
  var toast = $('<div>').css({
    position: 'fixed', top: '80px', right: '20px',
    background: c.fondo, border: '1px solid ' + c.borde,
    borderLeft: '4px solid ' + c.borde, color: c.texto,
    padding: '12px 18px', borderRadius: '10px',
    fontFamily: "'DM Sans', sans-serif", fontSize: '14px', fontWeight: '500',
    zIndex: 9999, boxShadow: '0 4px 16px rgba(0,0,0,0.10)',
    display: 'flex', alignItems: 'center', gap: '10px',
    opacity: 0, transition: 'all 0.25s ease'
  }).html('<span style="font-size:16px">' + c.icono + '</span><span>' + mensaje + '</span>');

  $('body').append(toast);
  setTimeout(function () { toast.css({ opacity: 1 }); }, 10);
  setTimeout(function () {
    toast.css({ opacity: 0 });
    setTimeout(function () { toast.remove(); }, 300);
  }, 3500);
}

function crearTarea() {
  var titulo      = $('#titulo').val().trim();
  var descripcion = $('#descripcion').val().trim();
  var fecha       = $('#fecha').val();

  if (!titulo) { mostrarToast('Ingresa el título de la tarea.', 'error'); $('#titulo').focus(); return; }
  if (!fecha)  { mostrarToast('Selecciona una fecha límite.', 'error');   $('#fecha').focus();  return; }

  var formData = new FormData();
  formData.append('titulo',      titulo);
  formData.append('descripcion', descripcion);
  formData.append('fecha',       fecha);

  var imgFile = $('#imagenTarea')[0] && $('#imagenTarea')[0].files[0];
  if (imgFile) formData.append('imagen', imgFile);

  $.ajax({
    url: 'registrar_tarea.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (r) {
      if (r === 'ok') {
        mostrarToast('Tarea publicada correctamente.', 'exito');
        $('#titulo').val('');
        $('#descripcion').val('');
        $('#fecha').val('');
        $('#imagenTarea').val('');
        $('#previewImagen').hide();
        cargar();
      } else {
        mostrarToast('Error: ' + r, 'error');
      }
    },
    error: function () { mostrarToast('Error al crear la tarea.', 'error'); }
  });
}

function cargar() {
  if ($('#tareas').length) {
    $.get('listar_tareas.php', function (d) {
      $('#tareas').html(d);
      actualizarMetricasEstudiante();
    }).fail(function () {
      $('#tareas').html('<p style="color:#A32D2D;font-size:14px;padding:1rem">Error al cargar tareas.</p>');
    });
  }

  if ($('#panelProfesor').length) {
    $.get('ver_tareas_profesor.php', function (d) {
      $('#panelProfesor').html(d);
      actualizarMetricasProfesor();
    }).fail(function () {
      $('#panelProfesor').html('<p style="color:#A32D2D;font-size:14px;padding:1rem">Error al cargar panel.</p>');
    });

    $.get('resumen_estudiantes.php', function (d) {
      $('#resumen').html(d);
    }).fail(function () {
      $('#resumen').html('<p style="color:#A32D2D;font-size:14px;padding:1rem">Error al cargar resumen.</p>');
    });
  }
}

function actualizarMetricasProfesor() {
  animarNumero('#met-total',       $('.item-tarea').length);
  animarNumero('#met-pendientes',  $('.item-tarea.pendiente').length);
  animarNumero('#met-completadas', $('.item-tarea.completada').length);
}

function actualizarMetricasEstudiante() {
  // Leer valores desde el data-attribute que emite listar_tareas.php
  var $met = $('[data-met-pendientes]');
  if ($met.length) {
    var pend  = parseInt($met.attr('data-met-pendientes'),  10) || 0;
    var entr  = parseInt($met.attr('data-met-entregadas'),  10) || 0;
    animarNumero('#met-pendientes-est',  pend);
    animarNumero('#met-completadas-est', entr);
  } else {
    // Fallback: contar clases del DOM
    animarNumero('#met-pendientes-est',  $('.seccion-label.naranja').length ? parseInt($('.seccion-label.naranja').text().match(/\d+/) || [0], 10) : 0);
    animarNumero('#met-completadas-est', $('.seccion-label.verde').length   ? parseInt($('.seccion-label.verde').text().match(/\d+/)   || [0], 10) : 0);
  }
}

function animarNumero(selector, destino) {
  if (!$(selector).length) return;
  var actual = 0, paso = Math.ceil(destino / 20) || 1;
  var timer = setInterval(function () {
    actual = Math.min(actual + paso, destino);
    $(selector).text(actual);
    if (actual >= destino) clearInterval(timer);
  }, 40);
}

function completar(id) {
  $.post('completar_tarea.php', { id: id }, function (r) {
    if (r === 'ok') { mostrarToast('Tarea marcada como completada.', 'exito'); cargar(); }
    else mostrarToast('Error: ' + r, 'error');
  }).fail(function () { mostrarToast('No se pudo actualizar el estado.', 'error'); });
}

function entregar(id) {
  $('#modalEntrega').remove();

  var modal = $('<div id="modalEntrega">').css({
    position: 'fixed', inset: 0, background: 'rgba(11,61,107,0.45)',
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    zIndex: 9998, fontFamily: "'DM Sans', sans-serif"
  }).html(
    '<div style="background:#fff;border-radius:14px;padding:28px;width:90%;max-width:460px;box-shadow:0 8px 32px rgba(0,0,0,0.15)">' +
      '<h6 style="font-family:Playfair Display,serif;font-size:17px;color:#0B3D6B;margin-bottom:16px">Entregar tarea</h6>' +

      '<label style="font-size:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;color:#6B6A65;display:block;margin-bottom:6px">Comentario (opcional si adjuntas archivo)</label>' +
      '<textarea id="textoEntrega" rows="3" placeholder="Escribe tu respuesta o enlace…" ' +
        'style="width:100%;padding:10px 14px;font-size:14px;border:1px solid #D8D6CE;border-radius:10px;background:#F5F4F0;font-family:DM Sans,sans-serif;resize:vertical;outline:none;margin-bottom:14px"></textarea>' +

      '<label style="font-size:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;color:#6B6A65;display:block;margin-bottom:6px">Adjuntar archivo (PDF, JPG o PNG — máx. 5MB)</label>' +
      '<input type="file" id="archivoEntrega" accept=".pdf,.jpg,.jpeg,.png" ' +
        'style="width:100%;padding:8px;font-size:13px;border:1px solid #D8D6CE;border-radius:10px;background:#F5F4F0;font-family:DM Sans,sans-serif;margin-bottom:18px">' +

      '<div style="display:flex;gap:10px;justify-content:flex-end">' +
        '<button onclick="$(\'#modalEntrega\').remove()" ' +
          'style="padding:8px 16px;border:1px solid #D8D6CE;border-radius:10px;background:transparent;color:#6B6A65;font-size:13px;cursor:pointer;font-family:DM Sans,sans-serif">Cancelar</button>' +
        '<button onclick="confirmarEntrega(' + id + ')" ' +
          'style="padding:8px 18px;border:none;border-radius:10px;background:#0B3D6B;color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:DM Sans,sans-serif">Enviar entrega</button>' +
      '</div>' +
    '</div>'
  );

  $('body').append(modal);
  $('#textoEntrega').focus();
}

function confirmarEntrega(id) {
  var texto   = $('#textoEntrega').val().trim();
  var archivo = $('#archivoEntrega')[0].files[0];

  if (!texto && !archivo) {
    mostrarToast('Escribe un comentario o adjunta un archivo.', 'error');
    return;
  }

  var formData = new FormData();
  formData.append('id', id);
  formData.append('entrega', texto);
  if (archivo) formData.append('archivo', archivo);

  $.ajax({
    url: 'entregar_tarea.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (r) {
      if (r === 'ok') {
        $('#modalEntrega').remove();
        mostrarToast('¡Entrega enviada! El profesor la revisará.', 'exito');
        cargar();
      } else {
        mostrarToast('Error: ' + r, 'error');
      }
    },
    error: function () { mostrarToast('Error al enviar la entrega.', 'error'); }
  });
}

function editarEntrega(id, textoActual) {
  $('#modalEntrega').remove();

  var modal = $('<div id="modalEntrega">').css({
    position: 'fixed', inset: 0, background: 'rgba(11,61,107,0.45)',
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    zIndex: 9998, fontFamily: "'DM Sans', sans-serif"
  }).html(
    '<div style="background:#fff;border-radius:14px;padding:28px;width:90%;max-width:460px;box-shadow:0 8px 32px rgba(0,0,0,0.15)">' +
      '<h6 style="font-family:Playfair Display,serif;font-size:17px;color:#0B3D6B;margin-bottom:16px">Editar entrega</h6>' +

      '<label style="font-size:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;color:#6B6A65;display:block;margin-bottom:6px">Comentario</label>' +
      '<textarea id="textoEntrega" rows="3" placeholder="Actualiza tu respuesta…" ' +
        'style="width:100%;padding:10px 14px;font-size:14px;border:1px solid #D8D6CE;border-radius:10px;background:#F5F4F0;font-family:DM Sans,sans-serif;resize:vertical;outline:none;margin-bottom:14px">' + textoActual + '</textarea>' +

      '<label style="font-size:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;color:#6B6A65;display:block;margin-bottom:6px">Reemplazar archivo (opcional — PDF, JPG o PNG)</label>' +
      '<input type="file" id="archivoEntrega" accept=".pdf,.jpg,.jpeg,.png" ' +
        'style="width:100%;padding:8px;font-size:13px;border:1px solid #D8D6CE;border-radius:10px;background:#F5F4F0;font-family:DM Sans,sans-serif;margin-bottom:18px">' +

      '<div style="display:flex;gap:10px;justify-content:flex-end">' +
        '<button onclick="$(\'#modalEntrega\').remove()" ' +
          'style="padding:8px 16px;border:1px solid #D8D6CE;border-radius:10px;background:transparent;color:#6B6A65;font-size:13px;cursor:pointer;font-family:DM Sans,sans-serif">Cancelar</button>' +
        '<button onclick="confirmarEntrega(' + id + ')" ' +
          'style="padding:8px 18px;border:none;border-radius:10px;background:#BA7517;color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:DM Sans,sans-serif">Guardar cambios</button>' +
      '</div>' +
    '</div>'
  );

  $('body').append(modal);
  $('#textoEntrega').focus();
}

function anularEntrega(id) {
  $('#modalConfirm').remove();

  var modal = $('<div id="modalConfirm">').css({
    position: 'fixed', inset: 0, background: 'rgba(11,61,107,0.45)',
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    zIndex: 9998, fontFamily: "'DM Sans', sans-serif"
  }).html(
    '<div style="background:#fff;border-radius:14px;padding:28px;width:90%;max-width:380px;box-shadow:0 8px 32px rgba(0,0,0,0.15)">' +
      '<h6 style="font-family:Playfair Display,serif;font-size:17px;color:#A32D2D;margin-bottom:10px">¿Anular entrega?</h6>' +
      '<p style="font-size:14px;color:#6B6A65;margin-bottom:20px">Se eliminará tu entrega y el archivo adjunto. Esta acción no se puede deshacer.</p>' +
      '<div style="display:flex;gap:10px;justify-content:flex-end">' +
        '<button onclick="$(\'#modalConfirm\').remove()" ' +
          'style="padding:8px 16px;border:1px solid #D8D6CE;border-radius:10px;background:transparent;color:#6B6A65;font-size:13px;cursor:pointer;font-family:DM Sans,sans-serif">Cancelar</button>' +
        '<button onclick="confirmarAnular(' + id + ')" ' +
          'style="padding:8px 18px;border:none;border-radius:10px;background:#A32D2D;color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:DM Sans,sans-serif">Sí, anular</button>' +
      '</div>' +
    '</div>'
  );

  $('body').append(modal);
}

function confirmarAnular(id) {
  $.post('anular_entrega.php', { id: id }, function (r) {
    $('#modalConfirm').remove();
    if (r === 'ok') { mostrarToast('Entrega anulada correctamente.', 'info'); cargar(); }
    else mostrarToast('Error: ' + r, 'error');
  }).fail(function () { mostrarToast('Error al anular la entrega.', 'error'); });
}