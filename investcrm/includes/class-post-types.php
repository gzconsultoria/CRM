<?php
namespace InvestCRM;

class PostTypes {
	public function register() {
		$this->clients();
		$this->opportunities();
		$this->tasks();
		$this->products();
	}

	private function clients() {
		$labels = array(
			'name'          => __( 'Clientes', 'investcrm' ),
			'singular_name' => __( 'Cliente', 'investcrm' ),
			'add_new'       => __( 'Adicionar Cliente', 'investcrm' ),
			'add_new_item'  => __( 'Novo Cliente', 'investcrm' ),
			'edit_item'     => __( 'Editar Cliente', 'investcrm' ),
			'new_item'      => __( 'Novo Cliente', 'investcrm' ),
			'view_item'     => __( 'Ver Cliente', 'investcrm' ),
			'not_found'     => __( 'Nenhum cliente encontrado', 'investcrm' ),
			'menu_name'     => __( 'Clientes', 'investcrm' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'supports'        => array( 'title', 'editor', 'author' ),
			'capability_type' => array( 'investcrm_client', 'investcrm_clients' ),
			'map_meta_cap'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'clients',
		);

		register_post_type( 'invest_client', $args );
	}

	private function opportunities() {
		$labels = array(
			'name'          => __( 'Negócios', 'investcrm' ),
			'singular_name' => __( 'Negócio', 'investcrm' ),
			'add_new_item'  => __( 'Novo Negócio', 'investcrm' ),
			'edit_item'     => __( 'Editar Negócio', 'investcrm' ),
			'view_item'     => __( 'Ver Negócio', 'investcrm' ),
			'menu_name'     => __( 'Negócios', 'investcrm' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'supports'        => array( 'title', 'editor', 'author' ),
			'capability_type' => array( 'investcrm_opportunity', 'investcrm_opportunities' ),
			'map_meta_cap'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'opportunities',
		);

		register_post_type( 'invest_opportunity', $args );

		register_taxonomy(
			'invest_opportunity_stage',
			'invest_opportunity',
			array(
				'labels'       => array(
					'name'          => __( 'Estágios', 'investcrm' ),
					'singular_name' => __( 'Estágio', 'investcrm' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'hierarchical' => false,
			)
		);
	}

	private function tasks() {
		$labels = array(
			'name'          => __( 'Tarefas', 'investcrm' ),
			'singular_name' => __( 'Tarefa', 'investcrm' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'supports'        => array( 'title', 'editor', 'author' ),
			'capability_type' => array( 'investcrm_task', 'investcrm_tasks' ),
			'map_meta_cap'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'tasks',
		);

		register_post_type( 'invest_task', $args );
	}

	private function products() {
		$labels = array(
			'name'          => __( 'Produtos', 'investcrm' ),
			'singular_name' => __( 'Produto', 'investcrm' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'supports'        => array( 'title', 'editor', 'author' ),
			'capability_type' => array( 'investcrm_product', 'investcrm_products' ),
			'map_meta_cap'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'products',
		);

		register_post_type( 'invest_product', $args );
	}
}
