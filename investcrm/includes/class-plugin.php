<?php
namespace InvestCRM;

class Plugin {
	/**
	 * Boot the plugin hooks.
	 */
	public function run() {
		$loader      = new Loader();
		$cpts        = new PostTypes();
		$metaboxes   = new Metaboxes();
		$rest        = new RestApi();
		$automation  = new Automation();
		$assets      = new Assets();
		$dashboard   = new Dashboard();
		$portal      = new Portal();
		$compliance  = new Compliance();
		$activities  = new Activities();
		$shortcodes  = new Shortcodes( $portal );
		$settings    = new Settings();

		$loader->add_action( 'init', array( $cpts, 'register' ), 0 );
		$loader->add_action( 'init', array( $metaboxes, 'register_meta' ) );
		$loader->add_action( 'add_meta_boxes', array( $metaboxes, 'add_meta_boxes' ) );
		$loader->add_action( 'save_post', array( $metaboxes, 'save_meta' ), 10, 2 );
		$loader->add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		$loader->add_action( 'init', array( $automation, 'register_cron' ) );
		$loader->add_filter( 'cron_schedules', array( $automation, 'cron_schedules' ) );
		$loader->add_action( 'investcrm_hourly_event', array( $automation, 'process_queue' ) );
		$automation->register_hooks( $loader );
		$loader->add_action( 'admin_menu', array( $dashboard, 'register_menu' ) );
		$loader->add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin' ) );
		$loader->add_action( 'wp_enqueue_scripts', array( $assets, 'enqueue_public' ) );
		$loader->add_action( 'init', array( $portal, 'register_rewrite_rules' ) );
		$loader->add_filter( 'query_vars', array( $portal, 'add_query_vars' ) );
		$loader->add_filter( 'template_include', array( $portal, 'template_include' ) );
		$loader->add_action( 'init', array( $compliance, 'register_hooks' ) );
		$loader->add_action( 'init', array( $shortcodes, 'register' ) );
		$loader->add_action( 'plugins_loaded', array( $assets, 'load_textdomain' ) );
		$activities->register_hooks( $loader );
		$settings->register( $loader );

		$loader->run();
	}
}
