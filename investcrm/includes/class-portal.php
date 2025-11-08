<?php
namespace InvestCRM;

class Portal {
	public function register_rewrite_rules() {
		add_rewrite_rule( '^portal-investidor/?$', 'index.php?investcrm_portal=1', 'top' );
	}

	public function add_query_vars( $vars ) {
		$vars[] = 'investcrm_portal';
		return $vars;
	}

	public function template_include( $template ) {
		if ( get_query_var( 'investcrm_portal' ) ) {
			return INVESTCRM_PATH . 'public/partials/portal.php';
		}

		return $template;
	}

	public function get_client_portal_data( $user_id ) {
		$client = $this->get_client_by_user( $user_id );
		if ( ! $client ) {
			return array();
		}

		return array(
			'client'       => $client,
			'aum'          => get_post_meta( $client->ID, 'aum', true ),
			'profile'      => get_post_meta( $client->ID, 'profile', true ),
			'next_meeting' => get_post_meta( $client->ID, 'next_meeting', true ),
			'last_meeting' => get_post_meta( $client->ID, 'last_meeting', true ),
			'rentability'  => get_post_meta( $client->ID, 'rentability', true ),
			'documents'    => get_attached_media( '', $client->ID ),
			'tasks'        => $this->get_tasks_for_client( $client->ID ),
			'consultant'   => get_post_meta( $client->ID, 'consultant', true ),
			'contact_link' => get_option( 'investcrm_whatsapp_link', '' ),
		);
	}

	private function get_client_by_user( $user_id ) {
		$query = new \WP_Query(
			array(
				'post_type'      => 'invest_client',
				'posts_per_page' => 1,
				'author'         => $user_id,
			)
		);

		return $query->post ? $query->post : null;
	}

	private function get_tasks_for_client( $client_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'investcrm_activities';

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE client_id = %d ORDER BY due_at DESC", $client_id ) );
	}
}
