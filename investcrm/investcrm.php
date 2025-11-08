<?php
/**
 * Plugin Name: InvestCRM
 * Plugin URI:  https://example.com/investcrm
 * Description: CRM completo inspirado no Bitrix24 para consultorias de investimento.
 * Version:     0.1.0
 * Author:      InvestCRM Team
 * Author URI:  https://example.com
 * Text Domain: investcrm
 * Domain Path: /languages
 */

define( 'INVESTCRM_VERSION', '0.1.0' );
define( 'INVESTCRM_FILE', __FILE__ );
define( 'INVESTCRM_PATH', plugin_dir_path( __FILE__ ) );
define( 'INVESTCRM_URL', plugin_dir_url( __FILE__ ) );

autoload_investcrm();

/**
 * Simple autoloader for plugin classes.
 */
function autoload_investcrm() {
	static $registered = false;

	if ( $registered ) {
		return;
	}

	spl_autoload_register(
		function ( $class ) {
			if ( 0 !== strpos( $class, 'InvestCRM' ) ) {
				return;
			}

$filename = str_replace( 'InvestCRM\\', '', $class );
			$filename = str_replace( '\\', DIRECTORY_SEPARATOR, $filename );
			$filename = str_replace( '_', '-', $filename );
			$filename = preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $filename );
			$filename = strtolower( $filename );
			$path     = INVESTCRM_PATH . 'includes/class-' . $filename . '.php';

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	);

	$registered = true;
}

register_activation_hook( __FILE__, array( 'InvestCRM\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'InvestCRM\Deactivator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	function () {
		$plugin = new InvestCRM\Plugin();
		$plugin->run();
	}
);
