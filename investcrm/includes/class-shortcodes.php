<?php
namespace InvestCRM;

class Shortcodes {
	private $portal;

	public function __construct( Portal $portal ) {
		$this->portal = $portal;
	}

	public function register() {
		add_shortcode( 'investcrm_portal', array( $this, 'render_portal' ) );
		add_shortcode( 'investcrm_client_kpis', array( $this, 'render_client_kpis' ) );
	}

	public function render_portal() {
		if ( ! is_user_logged_in() ) {
			return '<p>' . esc_html__( 'É necessário estar logado para acessar o portal.', 'investcrm' ) . '</p>';
		}

		$data = $this->portal->get_client_portal_data( get_current_user_id() );

		if ( empty( $data ) ) {
			return '<p>' . esc_html__( 'Nenhum cliente associado a este usuário.', 'investcrm' ) . '</p>';
		}

		ob_start();
		include INVESTCRM_PATH . 'public/partials/portal-widget.php';
		return ob_get_clean();
	}

	public function render_client_kpis( $atts ) {
		$atts = shortcode_atts( array( 'consultant' => 0 ), $atts );

		$args = array(
			'post_type'      => 'invest_client',
			'posts_per_page' => -1,
		);

		if ( $atts['consultant'] ) {
			$args['meta_key']   = 'consultant';
			$args['meta_value'] = absint( $atts['consultant'] );
		}

		$query = new \WP_Query( $args );

		$total_aum = 0;
		$active    = 0;
		foreach ( $query->posts as $client ) {
			$total_aum += (float) get_post_meta( $client->ID, 'aum', true );
			$status     = get_post_meta( $client->ID, 'status', true );
			if ( 'ativo' === $status ) {
				$active++;
			}
		}

		$rentability = $query->post_count ? round( $total_aum / max( 1, $query->post_count ), 2 ) : 0;

		return sprintf(
			'<div class="investcrm-kpis"><span class="investcrm-kpi">%s: <strong>R$ %s</strong></span><span class="investcrm-kpi">%s: <strong>%d</strong></span><span class="investcrm-kpi">%s: <strong>%s</strong></span></div>',
			esc_html__( 'AUM Total', 'investcrm' ),
			number_format_i18n( $total_aum, 2 ),
			esc_html__( 'Clientes Ativos', 'investcrm' ),
			$active,
			esc_html__( 'Rentabilidade Média', 'investcrm' ),
			number_format_i18n( $rentability, 2 )
		);
	}
}
