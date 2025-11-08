<?php
namespace InvestCRM;

class Settings {
	public function register( Loader $loader ) {
		$loader->add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_settings() {
		register_setting( 'investcrm_automation', 'investcrm_auto_followup_days', array( 'type' => 'integer', 'sanitize_callback' => 'absint', 'default' => 3 ) );
		register_setting( 'investcrm_automation', 'investcrm_whatsapp_link', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ) );

		add_settings_section( 'investcrm_automation_main', __( 'Gatilhos de automação', 'investcrm' ), '__return_false', 'investcrm_automation' );

		add_settings_field(
			'investcrm_auto_followup_days',
			__( 'Criar tarefa pós-proposta após X dias', 'investcrm' ),
			array( $this, 'render_followup_field' ),
			'investcrm_automation',
			'investcrm_automation_main'
		);

		add_settings_field(
			'investcrm_whatsapp_link',
			__( 'Link padrão de WhatsApp', 'investcrm' ),
			array( $this, 'render_whatsapp_field' ),
			'investcrm_automation',
			'investcrm_automation_main'
		);
	}

	public function render_followup_field() {
		$value = get_option( 'investcrm_auto_followup_days', 3 );
		printf( '<input type="number" min="1" name="investcrm_auto_followup_days" value="%d" />', $value );
	}

	public function render_whatsapp_field() {
		$value = get_option( 'investcrm_whatsapp_link', '' );
		printf( '<input type="text" class="regular-text" name="investcrm_whatsapp_link" value="%s" />', esc_attr( $value ) );
	}
}
