<?php
$fondo_login = ControladorConfiguracionSistema::ctrMostrarConfiguracionSistema($item = null, $valor = null);

// Extraer datos del array
$nombre = $fondo_login[0]['nombre'] ?? 'Nombre por defecto';
$img_login = (!empty($fondo_login[0]['img_login'])) ? substr($fondo_login[0]['img_login'], 3) : 'vistas/img/sistema/default_login.png';
$icon_login = (!empty($fondo_login[0]['icon_login'])) ? substr($fondo_login[0]['icon_login'], 3) : 'vistas/img/sistema/logo-small.png';

?>

<div class="main-wrapper" style="min-height: 100vh; display: flex; justify-content: center; align-items: center; background-image: url('<?php echo $img_login; ?>'); background-size: cover; background-position: center; position: relative;">
    <div class="account-content" style="background: white; padding: 30px; border-radius: 10px; max-width: 400px; width: 100%;">
        <div class="login-content">
            <div class="login-userset">
                
                <div class="login-userheading text-center mb-4">
                    <img src="<?php echo $icon_login; ?>" id="login_icon" width="100" alt="Icono de inicio de sesión">
                    <h3 style="margin-top: 15px; font-family: Arial, sans-serif; color: #333;"><?php echo $nombre; ?></h3>
                </div>

                <form id="login_form" method="POST">
                    <!-- INGRESO DE CORREO O CONTRASEÑA -->
                    <div class="form-login" style="margin-bottom: 15px;">
                        <label style="display: block; font-family: Arial, sans-serif; color: #333;">Ingrese su correo o usuario</label>
                        <div class="form-addons" style="position: relative;">
                            <input type="text" id="ingUsuario" name="ingUsuario" placeholder="Ingrese su correo o usuario" style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #ccc; border-radius: 5px; outline: none;">
                            <img src="vistas/assets/img/icons/mail.svg" alt="img" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px;">
                        </div>
                        <div id="errorIngUsuario"></div>
                    </div>

                    <!-- INGRESO DE CONTRASEÑA -->
                    <div class="form-login" style="margin-bottom: 15px;">
                        <label style="display: block; font-family: Arial, sans-serif; color: #333;">Ingrese su contraseña</label>
                        <div class="pass-group" style="position: relative;">
                            <input type="password" id="ingPassword" name="ingPassword" class="pass-input" placeholder="Ingrese su contraseña" style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #ccc; border-radius: 5px; outline: none;">
                            <span class="fas toggle-password fa-eye-slash" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                        </div>
                        <div id="errorIngPassword"></div>
                    </div>

                    <!-- RECORDAR CONTRASEÑA -->
                    <div class="form-login" style="margin-bottom: 15px;">
                        <div class="alreadyuser">
                            <p style="font-family: Arial, sans-serif; color: #555;"><a href="#" class="hover-a" style="color: #007bff; text-decoration: none;">¿Has olvidado tu contraseña?</a></p>
                        </div>
                    </div>

                    <!-- BOTON PARA INGRESAR -->
                    <div class="form-login">
                        <button type="submit" id="button_submit_login" class="btn btn-login btn-primary rounded-3" style="width: 100%; padding: 10px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Iniciar sesión</button>
                    </div>
                </form>

                <div class="signinform text-center" style="margin-top: 20px;">
                    <p style="font-family: Arial, sans-serif; color: #555;">¿No tienes una cuenta? <a href="#" class="hover-a" style="color: #007bff; text-decoration: none;">Inscribirse</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
