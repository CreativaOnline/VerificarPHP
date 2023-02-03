<?php
/**
 * Plugin Name: VerificarPHP
 * Plugin URI: https://creativaonline.es/
 * Description: Derifica si los plugins e temas instalados son compatibles con la versión actual de PHP
 * Version: 1.0.2
 * Author: Creativaonline
 * Author URI: https://creativaonline.es/
 * License: GPL2
 */

// Verifica la compatibilidad de los plugins e temas instalados con la versión actual de PHP
function verificador_compatibilidad_verificar_compatibilidad() {
    $elementos_incompatibles = array();
    $plugins = get_plugins();
    foreach ($plugins as $plugin) {
        $datos_plugin = get_plugin_data($plugin['PluginURI']);
        if (!empty($datos_plugin['RequiresPHP']) && version_compare(PHP_VERSION, $datos_plugin['RequiresPHP'], '<')) {
            $elementos_incompatibles[] = array(
                'tipo' => 'plugin',
                'nombre' => $datos_plugin['Name'],
                'versión_requerida' => $datos_plugin['RequiresPHP'],
                'versión_instalada' => PHP_VERSION,
            );
        }
    }
    $temas = wp_get_themes();
    foreach ($temas as $tema) {
        $datos_tema = $tema->get('RequiresPHP');
        if (!empty($datos_tema) && version_compare(PHP_VERSION, $datos_tema, '<')) {
            $elementos_incompatibles[] = array(
                'tipo' => 'tema',
                'nombre' => $tema->get('Name'),
                'versión_requerida' => $datos_tema,
                'versión_instalada' => PHP_VERSION,
            );
        }
    }
    update_option('verificador_compatibilidad_elementos_incompatibles', $elementos_incompatibles);
}
add_action('admin_init', 'verificador_compatibilidad_verificar_compatibilidad');

// Muestra una advertencia si hay elementos incompatibles
function verificador_compatibilidad_aviso_administrador() {
    $elementos_incompatibles = get_option('verificador_compatibilidad_elementos_incompatibles');
    if (!empty($elementos_incompatibles)) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Los siguientes plugins o temas no son compatibles con la versión actual de PHP:', 'verificador-compatibilidad'); ?></p>
            <ul>
                    <?php foreach ($elementos_incompatibles as $elemento): ?>
        <li><?php printf(__('%s (%s requerido, %s instalado)', 'verificador-compatibilidad'), $elemento['nombre'], $elemento['versión_requerida'], $elemento['versión_instalada']); ?></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php
    } else { echo 'Todos los plugins y temas instalados son compatibles con la versión actual de PHP.';
  }
}
add_action('admin_notices', 'verificador_compatibilidad_aviso_administrador');


