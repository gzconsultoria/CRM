<?php
namespace InvestCRM;

class Loader {
	protected $actions = array();
	protected $filters = array();

	public function add_action( $hook, $component, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = compact( 'hook', 'component', 'priority', 'accepted_args' );
	}

	public function add_filter( $hook, $component, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = compact( 'hook', 'component', 'priority', 'accepted_args' );
	}

	public function run() {
		foreach ( $this->actions as $action ) {
			add_action( $action['hook'], $action['component'], $action['priority'], $action['accepted_args'] );
		}

		foreach ( $this->filters as $filter ) {
			add_filter( $filter['hook'], $filter['component'], $filter['priority'], $filter['accepted_args'] );
		}
	}
}
