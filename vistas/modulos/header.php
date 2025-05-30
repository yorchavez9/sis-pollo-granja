<div class="header">
    <?php
    $item = null;
    $valor = null;
    $images = ControladorConfiguracionSistema::ctrMostrarConfiguracionSistema($item, $valor);

    // Comprobar si no hay registros
    if (empty($images)) {
    ?>
        <div class="header-left active">
            <a href="#" class="logo">
                <img src="vistas/img/sistema/logo-apuuray.png" alt="">
            </a>
            <a href="#" class="logo-small">
                <img src="vistas/img/sistema/logo-small.png" alt="">
            </a>
            <a id="toggle_btn" href="javascript:void(0);"></a>
        </div>
        <?php
    } else {
        foreach ($images as $key => $value) {
        ?>
            <div class="header-left active">
                <a href="#" class="logo">
                    <?php
                    if ($value["img_sidebar"] !== '' && $value["img_sidebar"] !== null) {
                        // Recortar los tres primeros caracteres de la URL
                        $imageUrl = substr($value["img_sidebar"], 3);
                    ?>
                        <img src="<?php echo $imageUrl; ?>" alt="">
                    <?php
                    } else {
                        echo '<img src="vistas/img/sistema/logo-apuuray.png" alt="">';
                    }
                    ?>
                </a>
                <a href="#" class="logo-small">
                    <?php
                    if ($value["img_sidebar_min"] !== '' && $value["img_sidebar_min"] !== null) {
                        // Recortar los tres primeros caracteres de la URL
                        $imageUrl = substr($value["img_sidebar_min"], 3);
                    ?>
                        <img src="<?php echo $imageUrl; ?>" alt="">
                    <?php
                    } else {
                        echo '<img src="vistas/img/sistema/logo-small.png" alt="">';
                    }
                    ?>
                </a>
                <a id="toggle_btn" href="javascript:void(0);"></a>
            </div>
    <?php
        }
    }
    ?>

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <ul class="nav user-menu">

        <li class="nav-item">
            <div class="top-nav-search">
                <a href="javascript:void(0);" class="responsive-search">
                    <i class="fa fa-search"></i>
                </a>
                <form action="#">
                    <div class="searchinputs">
                        <input type="text" placeholder="Buscar ...">
                        <div class="search-addon">
                            <span><img src="vistas/assets/img/icons/closes.svg" alt="img"></span>
                        </div>
                    </div>
                    <a class="btn" id="searchdiv"><img src="vistas/assets/img/icons/search.svg" alt="img"></a>
                </form>
            </div>
        </li>


        <li class="nav-item dropdown has-arrow flag-nav">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                <img src="vistas/assets/img/flags/us1.png" alt="" height="20">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="vistas/assets/img/flags/us.png" alt="" height="16"> Ingles
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="vistas/assets/img/flags/fr.png" alt="" height="16"> Frances
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="vistas/assets/img/flags/es.png" alt="" height="16"> Español
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="vistas/assets/img/flags/de.png" alt="" height="16"> Aleman
                </a>
            </div>
        </li>


        <li class="nav-item dropdown">
            <?php
            $item = null;
            $valor = null;
            $productosPorVencer = ControladorProducto::ctrMostrarProductosFechaVencimientos($item, $valor);
            $cantidadNotificaciones = count($productosPorVencer); // Contar el número de notificaciones
            ?>
            <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <img src="vistas/assets/img/icons/notification-bing.svg" alt="img">
                <span class="badge rounded-pill" id="cantidad_notificacion">
                    <?php echo $cantidadNotificaciones; ?>
                </span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notificaciones</span>
                    <a href="javascript:void(0)" class="clear-noti" id="clear-noti">Limpiar todo</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list" id="notification-list">
                        <?php if ($cantidadNotificaciones > 0): ?>
                            <?php foreach ($productosPorVencer as $producto): ?>
                                <li class="notification-message">
                                    <a href="activities.html">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img alt="" src="<?php echo substr($producto["imagen_producto"], 3); ?>">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details">
                                                    <span class="noti-title"><?php echo $producto["nombre_producto"]; ?></span>
                                                    <?php echo $producto["nombre_categoria"]; ?>
                                                </p>
                                                <p class="noti-time">
                                                    <span class="notification-time"><?php echo $producto["fecha_vencimiento"]; ?></span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="notification-message m-3 justify-content-center">
                                <p class="noti-details text-center">No hay notificaciones disponibles.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="productos">Ver todas las notificaciones</a>
                </div>
            </div>
        </li>


        <li class="nav-item dropdown has-arrow main-drop">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                <span class="user-img">
                    <?php if (!empty($_SESSION["usuario"]["imagen_usuario"])): ?>
                        <img src="<?php echo "vistas/img/usuarios/".$_SESSION["usuario"]["imagen_usuario"]; ?>" alt="">
                    <?php else: ?>
                        <img src="vistas/img/usuarios/default.jpeg" alt="Usuario sin imagen">
                    <?php endif; ?>
                    <span class="status online"></span>
                </span>
            </a>

            <div class="dropdown-menu menu-drop-user">
                <div class="profilename">
                    <div class="profileset">
                        <span class="user-img"><img src="<?php echo "vistas/img/usuarios/".$_SESSION["usuario"]["imagen_usuario"]; ?>" alt="">
                            <span class="status online"></span></span>
                        <div class="profilesets">
                            <h6> <?php
                                    $nombreUsuario = explode(" ", $_SESSION["usuario"]["nombre_usuario"]);
                                    echo $nombreUsuario[0];
                                    ?>
                            </h6>
                            <h5>
                                <?php
                                echo $_SESSION["roles"][1];
                                ?>
                            </h5>

                        </div>
                    </div>
                    <hr class="m-0">
                    <a class="dropdown-item" href="usuarios"> <i class="me-2" data-feather="user"></i>Mi perfil</a>
                    <hr class="m-0">
                    <a class="dropdown-item logout pb-0" href="salir"><img src="vistas/assets/img/icons/log-out.svg" class="me-2" alt="img">Salir</a>
                </div>
            </div>
        </li>
    </ul>


    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="usuarios">Mi perfil</a>
            <a class="dropdown-item" href="usuarios">Configuración</a>
            <a class="dropdown-item" href="salir">Salir</a>
        </div>
    </div>

</div>

<script>
    document.getElementById('clear-noti').addEventListener('click', function() {
        document.getElementById('notification-list').innerHTML = '';
        document.getElementById('cantidad_notificacion').innerHTML = '0';
    });
</script>
