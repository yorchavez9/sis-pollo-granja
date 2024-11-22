<div class="main-wrapper" style="min-height: 100vh; display: flex; justify-content: center; align-items: center; background-image: url('vistas/img/sistema/login2.png'); background-size: cover; background-position: center; position: relative;">
    <div class="account-content" style="background: rgba(255, 255, 255, 0.9); padding: 30px; border-radius: 10px; max-width: 400px; width: 100%; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);">
        <div class="login-content">
            <div class="login-userset">
                <div class="login-logo text-center">
                    <!-- <img src="vistas/assets/img/logo.png" class="text-center" alt="img"> -->
                </div>
                <div class="login-userheading text-center mb-4">
                    <img src="vistas/img/sistema/login-logo.png" width="100" alt="">
                    <h3 style="margin-top: 15px; font-family: Arial, sans-serif; color: #333;">Iniciar sesión</h3>
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

                    <?php
                    $ingresousuario = new ControladorUsuarios();
                    $ingresousuario->ctrIngresoUsuario();
                    ?>
                </form>

                <div class="signinform text-center" style="margin-top: 20px;">
                    <p style="font-family: Arial, sans-serif; color: #555;">¿No tienes una cuenta? <a href="#" class="hover-a" style="color: #007bff; text-decoration: none;">Inscribirse</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
