<?php
include "conexion.php";

// Si ya tiene sesión, redirigir al dashboard
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Académico — Acceso</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --azul-institucional: #0B3D6B;
    --azul-medio: #185FA5;
    --dorado: #C9A84C;
    --gris-fondo: #F5F4F0;
    --gris-borde: #D8D6CE;
    --gris-texto: #6B6A65;
    --radio: 10px;
  }

  * { box-sizing: border-box; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--gris-fondo);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-card {
    background: #fff;
    border-radius: 16px;
    border: 0.5px solid var(--gris-borde);
    box-shadow: 0 4px 32px rgba(11,61,107,0.10);
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 380px;
  }

  .login-escudo {
    width: 56px; height: 56px;
    background: var(--azul-institucional);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
    border: 2px solid var(--dorado);
  }

  .login-titulo {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    color: var(--azul-institucional);
    text-align: center;
    margin-bottom: 4px;
  }

  .login-subtitulo {
    font-size: 13px;
    color: var(--gris-texto);
    text-align: center;
    margin-bottom: 1.75rem;
  }

  .form-label-login {
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gris-texto);
    margin-bottom: 5px;
    display: block;
  }

  .form-control-login {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    border: 1px solid var(--gris-borde);
    border-radius: var(--radio);
    background: var(--gris-fondo);
    color: #1A1A1A;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    margin-bottom: 14px;
  }

  .form-control-login:focus {
    border-color: var(--azul-medio);
    box-shadow: 0 0 0 3px rgba(24,95,165,0.1);
    background: #fff;
  }

  .btn-login {
    width: 100%;
    padding: 11px;
    background: var(--azul-institucional);
    color: #fff;
    border: none;
    border-radius: var(--radio);
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
    margin-top: 4px;
  }

  .btn-login:hover { background: var(--azul-medio); }

  .login-error {
    background: #FCEBEB;
    border: 1px solid rgba(163,45,45,0.25);
    border-left: 4px solid #A32D2D;
    color: #A32D2D;
    border-radius: var(--radio);
    padding: 10px 14px;
    font-size: 13px;
    margin-bottom: 14px;
    display: none;
  }

  .login-footer {
    text-align: center;
    font-size: 12px;
    color: var(--gris-texto);
    margin-top: 1.5rem;
  }

  .login-footer span { color: var(--dorado); }
</style>
</head>
<body>

<div class="login-card">
  <div class="login-escudo">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="1.8">
      <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
      <path d="M6 12v5c3 3 9 3 12 0v-5"/>
    </svg>
  </div>
  <div class="login-titulo">Sistema Académico</div>
  <div class="login-subtitulo">Ingresa tus credenciales para continuar</div>

  <div class="login-error" id="loginError">Carnet o contraseña incorrectos.</div>

  <div>
    <label class="form-label-login">Carnet</label>
    <input class="form-control-login" id="carnet" placeholder="Ej. RE253008" autocomplete="username">

    <label class="form-label-login">Contraseña</label>
    <input type="password" class="form-control-login" id="password" placeholder="••••••••" autocomplete="current-password">

    <button class="btn-login" id="btnLogin">Ingresar</button>
  </div>

  <div class="login-footer">Sistema Académico &mdash; <span>DSS</span></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function doLogin() {
  var carnet   = $('#carnet').val().trim();
  var password = $('#password').val().trim();
  if (!carnet || !password) { $('#loginError').text('Completa todos los campos.').show(); return; }

  $('#btnLogin').prop('disabled', true).text('Verificando…');
  $('#loginError').hide();

  $.post('login.php', { carnet: carnet, password: password }, function(r) {
    if (r === 'ok') {
      location.href = 'dashboard.php';
    } else {
      $('#loginError').text('Carnet o contraseña incorrectos.').show();
      $('#btnLogin').prop('disabled', false).text('Ingresar');
    }
  }).fail(function() {
    $('#loginError').text('Error de conexión. Intenta de nuevo.').show();
    $('#btnLogin').prop('disabled', false).text('Ingresar');
  });
}

$('#btnLogin').on('click', doLogin);
$('#password').on('keypress', function(e) { if (e.which === 13) doLogin(); });
$('#carnet').on('keypress',   function(e) { if (e.which === 13) doLogin(); });
</script>
</body>
</html>