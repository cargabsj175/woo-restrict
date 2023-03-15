<?php
/**
 * Plugin Name: Restringir Woocommerce
 * Plugin URI: https://github.com/cargabsj175/woo-restrict
 * Description: Plugin que restringe el acceso a productos y carrito en WooCommerce a usuarios no logueados.
 * Version: 1.1.3
 * Author: cargabsj175
 * Author URI: https://github.com
 * License: GPL3
 **/

// Agregar un menú al panel de WordPress
add_action('admin_menu', 'woo_rest_menu');

function woo_rest_menu() {
    add_menu_page(
        'Restringir Woocommerce', // Título de la página
        'Restringir Woocommerce', // Título del menú
        'manage_options', // Capacidad requerida para acceder al menú
        'mi-plugin', // Slug del menú
        'woo_rest_pagina_principal', // Función que renderiza la página
        'dashicons-cart' // Icono del menú
    );
}

// Página principal del plugin
function woo_rest_pagina_principal() {
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos para acceder a esta página.'));
    }

    // Código HTML de la página principal del plugin
    ?>
    <div class="wrap">
        <h1>Restringir Woocommerce</h1>
        <p>Plugin que restringe el acceso a productos y carrito en WooCommerce a usuarios no logueados.</p>
        <p>Activa o desactiva el plugin:</p>
        <form method="post" action="options.php">
            <?php settings_fields('woo_rest_configuracion'); ?>
            <?php do_settings_sections('woo_rest'); ?>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar cambios">
        </form>
    </div>
    <?php
}

// Registrar las opciones de configuración del plugin
add_action('admin_init', 'woo_rest_registrar_configuracion');

function woo_rest_registrar_configuracion() {
    register_setting('woo_rest_configuracion', 'woo_rest_activo');
    add_settings_section('woo_rest_seccion_principal', 'Configuración del plugin', 'woo_rest_seccion_principal_cb', 'woo_rest');
    add_settings_field('woo_rest_activo', 'Activar', 'woo_rest_activo_cb', 'woo_rest', 'woo_rest_seccion_principal');
}

// Renderizar la sección principal de la página de configuración
function woo_rest_seccion_principal_cb() {
    echo '<p>Activa o desactiva el plugin:</p>';
}

// Renderizar la opción de activar/desactivar del plugin
function woo_rest_activo_cb() {
    $activo = get_option('woo_rest_activo');
    echo '<input type="checkbox" name="woo_rest_activo" value="1" ' . checked(1, $activo, false) . '/>';
}

// Activar o desactivar el plugin según la opción seleccionada
function woo_rest_activar_desactivar() {
    $activo = get_option('woo_rest_activo');
    if ($activo) {
        add_action( 'pre_get_posts', 'ocultar_productos_no_logueado' );

function ocultar_productos_no_logueado( $query ) {
    if ( ! is_user_logged_in() && ( $query->is_post_type_archive( 'product' ) || $query->is_tax( 'product_cat' ) || $query->is_tax( 'product_tag' ) ) ) {
        $query->set( 'post__in', array( -1 ) );
        add_action( 'woocommerce_before_main_content', 'mensaje_iniciar_sesion' );
    }
}

function mensaje_iniciar_sesion() {
    // Si el usuario no ha iniciado sesión, muestra el mensaje personalizado
    if ( ! is_user_logged_in() ) {
        echo '<p>Para ver los detalles del producto, por favor <a href="' . wp_login_url( get_permalink() ) . '">inicia sesión</a>.</p>';
    }
}

add_action( 'template_redirect', 'restringir_acceso_productos' );

function restringir_acceso_productos() {
    if ( ! is_user_logged_in() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        wp_redirect( wp_login_url(), 301 );
        exit;
    }
}

add_action( 'template_redirect', 'redirigir_carrito_a_login' );

function redirigir_carrito_a_login() {
    if ( is_cart() && ! is_user_logged_in() ) {
        wp_redirect( get_permalink( get_option('woocommerce_myaccount_page_id') ) );
        exit();
    }
}

    }
}

// Ejecutar el código del plugin si está activo
if (get_option('woo_rest_activo')) {
    woo_rest_activar_desactivar();
}
