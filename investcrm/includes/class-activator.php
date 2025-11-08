<?php
namespace InvestCRM;

use wpdb;

class Activator {
	public static function activate() {
		self::create_tables();
		self::register_roles();
		self::create_taxonomies();
		self::flush_rewrite();
	}

	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$tables = array(
			"CREATE TABLE {$wpdb->prefix}investcrm_activities (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(255) NOT NULL,
				description LONGTEXT NULL,
				client_id BIGINT UNSIGNED NULL,
				opportunity_id BIGINT UNSIGNED NULL,
				assignee BIGINT UNSIGNED NULL,
				due_at DATETIME NULL,
				recurrence VARCHAR(50) NULL,
				status VARCHAR(20) DEFAULT 'pending',
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;",
			"CREATE TABLE {$wpdb->prefix}investcrm_audit_log (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				actor BIGINT UNSIGNED NOT NULL,
				action VARCHAR(100) NOT NULL,
				object_type VARCHAR(50) NOT NULL,
				object_id BIGINT UNSIGNED NULL,
				payload LONGTEXT NULL,
				created_at DATETIME NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;",
			"CREATE TABLE {$wpdb->prefix}investcrm_consent (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				client_id BIGINT UNSIGNED NOT NULL,
				consent_type VARCHAR(50) NOT NULL,
				token VARCHAR(191) NULL,
				granted_at DATETIME NOT NULL,
				revoked_at DATETIME NULL,
				PRIMARY KEY (id),
				KEY consent_client (client_id)
			) $charset_collate;"
		);

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		foreach ( $tables as $sql ) {
			dbDelta( $sql );
		}
	}

	private static function register_roles() {
		add_role(
			'investcrm_consultant',
			__( 'Consultor InvestCRM', 'investcrm' ),
			array(
				'read'                        => true,
				'edit_investcrm_clients'      => true,
				'edit_investcrm_opportunities' => true,
				'edit_investcrm_tasks'        => true,
				'edit_investcrm_products'     => false,
			)
		);

		add_role(
			'investcrm_manager',
			__( 'Gestor InvestCRM', 'investcrm' ),
			array(
				'read'                           => true,
				'edit_investcrm_clients'         => true,
				'edit_others_investcrm_clients'  => true,
				'edit_investcrm_opportunities'   => true,
				'edit_others_investcrm_opportunities' => true,
				'edit_investcrm_tasks'           => true,
				'edit_investcrm_products'        => true,
				'assign_investcrm_tasks'         => true,
				'manage_investcrm_settings'      => true,
			)
		);

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$caps = array(
				'edit_investcrm_clients',
				'edit_others_investcrm_clients',
				'edit_investcrm_opportunities',
				'edit_others_investcrm_opportunities',
				'edit_investcrm_tasks',
				'edit_investcrm_products',
				'assign_investcrm_tasks',
				'manage_investcrm_settings',
			);

			foreach ( $caps as $cap ) {
				$admin->add_cap( $cap );
			}
		}
	}

	private static function create_taxonomies() {
		register_taxonomy(
			'invest_opportunity_stage',
			'invest_opportunity',
			array(
				'public'       => false,
				'show_ui'      => true,
				'hierarchical' => false,
			)
		);

		$stages = array( 'Captação', 'Proposta', 'Fechamento', 'Pós-venda' );
		foreach ( $stages as $stage ) {
			if ( ! term_exists( $stage, 'invest_opportunity_stage' ) ) {
				wp_insert_term( $stage, 'invest_opportunity_stage' );
			}
		}
	}

	private static function flush_rewrite() {
		flush_rewrite_rules();
	}
}
