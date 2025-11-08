<?php
namespace InvestCRM;

class Assets {
	public function load_textdomain() {
		load_plugin_textdomain( 'investcrm', false, dirname( plugin_basename( INVESTCRM_FILE ) ) . '/languages' );
	}

	public function enqueue_admin( $hook ) {
		if ( false === strpos( $hook, 'investcrm' ) ) {
			return;
		}

		wp_enqueue_style( 'investcrm-admin', INVESTCRM_URL . 'assets/css/admin.css', array(), INVESTCRM_VERSION );
		wp_enqueue_script( 'investcrm-kanban', INVESTCRM_URL . 'assets/js/kanban.js', array( 'jquery', 'wp-api-fetch' ), INVESTCRM_VERSION, true );

		wp_localize_script(
			'investcrm-kanban',
			'investcrmSettings',
			array(
				'apiUrl'   => esc_url_raw( rest_url( 'investcrm/v1/pipeline' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'messages' => array(
					'loading' => __( 'Carregando pipeline...', 'investcrm' ),
				),
			)
		);
	}

	public function enqueue_public() {
		if ( ! is_singular( 'invest_client' ) && ! get_query_var( 'investcrm_portal' ) ) {
			return;
		}

		wp_enqueue_style( 'investcrm-portal', INVESTCRM_URL . 'assets/css/portal.css', array(), INVESTCRM_VERSION );
		wp_enqueue_script( 'investcrm-portal', INVESTCRM_URL . 'assets/js/portal.js', array( 'jquery' ), INVESTCRM_VERSION, true );
	}
}
