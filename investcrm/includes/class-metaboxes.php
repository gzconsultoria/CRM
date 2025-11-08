<?php
namespace InvestCRM;

class Metaboxes {
	private $client_fields = array(
		'profile'       => array( 'label' => 'Perfil do Investidor', 'type' => 'select', 'options' => array( 'conservador', 'moderado', 'arrojado' ) ),
		'aum'           => array( 'label' => 'AUM (R$)', 'type' => 'number' ),
		'rentability'   => array( 'label' => 'Rentabilidade (%)', 'type' => 'number' ),
		'status'        => array( 'label' => 'Status', 'type' => 'select', 'options' => array( 'ativo', 'inativo', 'potencial' ) ),
		'suitability'   => array( 'label' => 'Suitability', 'type' => 'textarea' ),
		'compliance'    => array( 'label' => 'Compliance', 'type' => 'textarea' ),
		'consultant'    => array( 'label' => 'Consultor Responsável', 'type' => 'user' ),
		'next_meeting'  => array( 'label' => 'Próxima Reunião', 'type' => 'datetime' ),
		'last_meeting'  => array( 'label' => 'Última Reunião', 'type' => 'datetime' ),
		'consent_token' => array( 'label' => 'Consentimento (Token)', 'type' => 'text' ),
	);

	private $opportunity_fields = array(
		'value'          => array( 'label' => 'Valor do Negócio', 'type' => 'number' ),
		'estimated_close' => array( 'label' => 'Fechamento Previsto', 'type' => 'date' ),
		'client_id'      => array( 'label' => 'Cliente Relacionado', 'type' => 'post', 'post_type' => 'invest_client' ),
		'consultant'     => array( 'label' => 'Consultor Responsável', 'type' => 'user' ),
	);

	private $task_fields = array(
		'due_at'        => array( 'label' => 'Prazo', 'type' => 'datetime' ),
		'assignee'      => array( 'label' => 'Responsável', 'type' => 'user' ),
		'client_id'     => array( 'label' => 'Cliente Relacionado', 'type' => 'post', 'post_type' => 'invest_client' ),
		'opportunity_id' => array( 'label' => 'Negócio Relacionado', 'type' => 'post', 'post_type' => 'invest_opportunity' ),
		'recurrence'    => array( 'label' => 'Recorrência', 'type' => 'select', 'options' => array( '', 'weekly', 'monthly', 'quarterly', 'yearly' ) ),
	);

	private $product_fields = array(
		'asset_class' => array( 'label' => 'Classe de Ativo', 'type' => 'text' ),
		'risk'        => array( 'label' => 'Risco', 'type' => 'select', 'options' => array( 'baixo', 'médio', 'alto' ) ),
		'return'      => array( 'label' => 'Retorno Esperado (%)', 'type' => 'number' ),
		'liquidity'   => array( 'label' => 'Liquidez', 'type' => 'text' ),
	);

	public function register_meta() {
		foreach ( $this->client_fields as $field => $args ) {
			register_post_meta(
				'invest_client',
				$field,
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => $this->field_type_to_meta_type( $args['type'] ),
					'auth_callback' => array( $this, 'can_edit_client_meta' ),
				)
			);
		}

		foreach ( $this->opportunity_fields as $field => $args ) {
			register_post_meta(
				'invest_opportunity',
				$field,
				array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => $this->field_type_to_meta_type( $args['type'] ),
				)
			);
		}

		foreach ( $this->task_fields as $field => $args ) {
			register_post_meta(
				'invest_task',
				$field,
				array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => $this->field_type_to_meta_type( $args['type'] ),
				)
			);
		}

		foreach ( $this->product_fields as $field => $args ) {
			register_post_meta(
				'invest_product',
				$field,
				array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => $this->field_type_to_meta_type( $args['type'] ),
				)
			);
		}
	}

	public function add_meta_boxes() {
		add_meta_box( 'investcrm_client_meta', __( 'Detalhes do Cliente', 'investcrm' ), array( $this, 'render_client_box' ), 'invest_client', 'normal', 'high' );
		add_meta_box( 'investcrm_opportunity_meta', __( 'Detalhes do Negócio', 'investcrm' ), array( $this, 'render_opportunity_box' ), 'invest_opportunity', 'normal', 'high' );
		add_meta_box( 'investcrm_task_meta', __( 'Detalhes da Tarefa', 'investcrm' ), array( $this, 'render_task_box' ), 'invest_task', 'normal', 'high' );
		add_meta_box( 'investcrm_product_meta', __( 'Detalhes do Produto', 'investcrm' ), array( $this, 'render_product_box' ), 'invest_product', 'normal', 'high' );
	}

	public function save_meta( $post_id, $post ) {
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return;
		}

		if ( 'invest_client' === $post->post_type ) {
			$this->save_fields( $post_id, $this->client_fields, 'investcrm_client_meta_nonce' );
		} elseif ( 'invest_opportunity' === $post->post_type ) {
			$this->save_fields( $post_id, $this->opportunity_fields, 'investcrm_opportunity_meta_nonce' );
		} elseif ( 'invest_task' === $post->post_type ) {
			$this->save_fields( $post_id, $this->task_fields, 'investcrm_task_meta_nonce' );
		} elseif ( 'invest_product' === $post->post_type ) {
			$this->save_fields( $post_id, $this->product_fields, 'investcrm_product_meta_nonce' );
		}
	}

	public function render_client_box( $post ) {
		wp_nonce_field( 'investcrm_save_client', 'investcrm_client_meta_nonce' );
		$this->render_fields( $post, $this->client_fields );
	}

	public function render_opportunity_box( $post ) {
		wp_nonce_field( 'investcrm_save_opportunity', 'investcrm_opportunity_meta_nonce' );
		$this->render_fields( $post, $this->opportunity_fields );
	}

	public function render_task_box( $post ) {
		wp_nonce_field( 'investcrm_save_task', 'investcrm_task_meta_nonce' );
		$this->render_fields( $post, $this->task_fields );
	}

	public function render_product_box( $post ) {
		wp_nonce_field( 'investcrm_save_product', 'investcrm_product_meta_nonce' );
		$this->render_fields( $post, $this->product_fields );
	}

	private function render_fields( $post, $fields ) {
		echo '<table class="form-table">';
		foreach ( $fields as $key => $field ) {
			$value = get_post_meta( $post->ID, $key, true );
			echo '<tr><th><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
			switch ( $field['type'] ) {
				case 'select':
					echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="regular-text">';
					foreach ( $field['options'] as $option ) {
						echo '<option value="' . esc_attr( $option ) . '" ' . selected( $value, $option, false ) . '>' . esc_html( ucfirst( $option ) ) . '</option>';
					}
					echo '</select>';
					break;
				case 'user':
					wp_dropdown_users(
						array(
							'name'             => $key,
							'id'               => $key,
							'selected'         => $value,
							'show_option_none' => __( 'Selecione um usuário', 'investcrm' ),
						)
					);
					break;
				case 'post':
					$posts = get_posts( array( 'post_type' => $field['post_type'], 'numberposts' => -1 ) );
					echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="regular-text">';
					echo '<option value="">' . esc_html__( 'Selecione', 'investcrm' ) . '</option>';
					foreach ( $posts as $related ) {
						echo '<option value="' . esc_attr( $related->ID ) . '" ' . selected( $value, $related->ID, false ) . '>' . esc_html( $related->post_title ) . '</option>';
					}
					echo '</select>';
					break;
				case 'textarea':
					echo '<textarea name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="large-text" rows="3">' . esc_textarea( $value ) . '</textarea>';
					break;
				case 'datetime':
					echo '<input type="datetime-local" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $this->format_datetime_local( $value ) ) . '" class="regular-text" />';
					break;
				case 'date':
					echo '<input type="date" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
					break;
				case 'number':
					echo '<input type="number" step="0.01" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
					break;
				default:
					echo '<input type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}

	private function save_fields( $post_id, $fields, $nonce_name ) {
		$nonce_action = str_replace( '_meta_nonce', '', $nonce_name );
		if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $nonce_action ) ) {
			return;
		}

		foreach ( $fields as $key => $field ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				delete_post_meta( $post_id, $key );
				continue;
			}

			$value = wp_unslash( $_POST[ $key ] );

			switch ( $field['type'] ) {
				case 'number':
					$value = (float) $value;
					break;
				case 'datetime':
				case 'date':
					$value = sanitize_text_field( $value );
					break;
				case 'textarea':
					$value = sanitize_textarea_field( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
			}

			update_post_meta( $post_id, $key, $value );

			if ( 'consent_token' === $key && ! empty( $value ) ) {
				$this->log_consent( $post_id, $value );
			}
		}
	}

	private function log_consent( $post_id, $token ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'investcrm_consent',
			array(
				'client_id'   => $post_id,
				'consent_type' => 'lgpd',
				'token'        => $token,
				'granted_at'   => current_time( 'mysql' ),
			)
		);
	}

	private function can_edit_client_meta() {
		return current_user_can( 'edit_investcrm_clients' );
	}

	private function field_type_to_meta_type( $type ) {
		switch ( $type ) {
			case 'number':
				return 'number';
			case 'datetime':
			case 'date':
				return 'string';
			default:
				return 'string';
		}
	}

	private function format_datetime_local( $value ) {
		if ( empty( $value ) ) {
			return '';
		}

		$timestamp = strtotime( $value );
		if ( ! $timestamp ) {
			return '';
		}

		return gmdate( 'Y-m-d\TH:i', $timestamp + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
	}
}
