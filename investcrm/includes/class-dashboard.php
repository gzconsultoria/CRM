<?php
namespace InvestCRM;

class Dashboard {
	public function register_menu() {
		add_menu_page(
			__( 'Central da Consultoria', 'investcrm' ),
			__( 'InvestCRM', 'investcrm' ),
			'edit_investcrm_clients',
			'investcrm-dashboard',
			array( $this, 'render_dashboard' ),
			'dashicons-chart-area',
			2
		);

		add_submenu_page( 'investcrm-dashboard', __( 'Clientes', 'investcrm' ), __( 'Clientes', 'investcrm' ), 'edit_investcrm_clients', 'edit.php?post_type=invest_client' );
		add_submenu_page( 'investcrm-dashboard', __( 'Negócios', 'investcrm' ), __( 'Negócios', 'investcrm' ), 'edit_investcrm_opportunities', 'edit.php?post_type=invest_opportunity' );
		add_submenu_page( 'investcrm-dashboard', __( 'Tarefas', 'investcrm' ), __( 'Tarefas', 'investcrm' ), 'edit_investcrm_tasks', 'edit.php?post_type=invest_task' );
		add_submenu_page( 'investcrm-dashboard', __( 'Produtos', 'investcrm' ), __( 'Produtos', 'investcrm' ), 'edit_investcrm_products', 'edit.php?post_type=invest_product' );
		add_submenu_page( 'investcrm-dashboard', __( 'Automação', 'investcrm' ), __( 'Automação', 'investcrm' ), 'assign_investcrm_tasks', 'investcrm-automation', array( $this, 'render_automation' ) );
	}

	public function render_dashboard() {
		$clients       = wp_count_posts( 'invest_client' );
		$opportunities = wp_count_posts( 'invest_opportunity' );
		$tasks         = wp_count_posts( 'invest_task' );

		include INVESTCRM_PATH . 'admin/partials/dashboard.php';
	}

	public function render_automation() {
		include INVESTCRM_PATH . 'admin/partials/automation.php';
	}
}
