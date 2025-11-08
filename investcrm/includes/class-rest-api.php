<?php
namespace InvestCRM;

class RestApi {
	public function register_routes() {
		register_rest_route(
			'investcrm/v1',
			'/clients/(?P<id>\d+)/recommendations',
			array(
				'callback'            => array( $this, 'get_recommendations' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'validate_callback' => 'is_numeric',
					),
				),
			)
		);

		register_rest_route(
			'investcrm/v1',
			'/pipeline',
			array(
				'callback'            => array( $this, 'get_pipeline' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

	public function permissions_check() {
		return current_user_can( 'edit_investcrm_clients' );
	}

	public function get_recommendations( $request ) {
		$client_id = absint( $request['id'] );
		$client    = get_post( $client_id );

		if ( ! $client || 'invest_client' !== $client->post_type ) {
			return new \WP_Error( 'not_found', __( 'Cliente não encontrado', 'investcrm' ), array( 'status' => 404 ) );
		}

		$profile   = get_post_meta( $client_id, 'profile', true );
		$risk_meta = array(
			'conservador' => array( 'baixo' ),
			'moderado'    => array( 'baixo', 'médio' ),
			'arrojado'    => array( 'médio', 'alto' ),
		);

		$risks = isset( $risk_meta[ $profile ] ) ? $risk_meta[ $profile ] : array( 'baixo', 'médio', 'alto' );

		$query = new \WP_Query(
			array(
				'post_type'      => 'invest_product',
				'posts_per_page' => 10,
				'meta_query'     => array(
					array(
						'key'     => 'risk',
						'value'   => $risks,
						'compare' => 'IN',
					),
				),
			)
		);

		$data = array();

		foreach ( $query->posts as $product ) {
			$data[] = array(
				'id'              => $product->ID,
				'title'           => $product->post_title,
				'risk'            => get_post_meta( $product->ID, 'risk', true ),
				'asset_class'     => get_post_meta( $product->ID, 'asset_class', true ),
				'expected_return' => get_post_meta( $product->ID, 'return', true ),
				'liquidity'       => get_post_meta( $product->ID, 'liquidity', true ),
			);
		}

		return rest_ensure_response( $data );
	}

	public function get_pipeline() {
		$stages = get_terms(
			array(
				'taxonomy'   => 'invest_opportunity_stage',
				'hide_empty' => false,
			)
		);

		$pipeline = array();

		foreach ( $stages as $stage ) {
			$opportunities = get_posts(
				array(
					'post_type'      => 'invest_opportunity',
					'posts_per_page' => -1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'invest_opportunity_stage',
							'terms'    => $stage->term_id,
						),
					),
				)
			);

			$pipeline[] = array(
				'id'           => $stage->term_id,
				'name'         => $stage->name,
				'opportunities' => array_map( array( $this, 'format_opportunity' ), $opportunities ),
			);
		}

		return rest_ensure_response( $pipeline );
	}

	private function format_opportunity( $post ) {
		return array(
			'id'             => $post->ID,
			'title'          => $post->post_title,
			'value'          => (float) get_post_meta( $post->ID, 'value', true ),
			'client_id'      => (int) get_post_meta( $post->ID, 'client_id', true ),
			'estimated_close' => get_post_meta( $post->ID, 'estimated_close', true ),
		);
	}
}
