<?php
namespace InvestCRM;

class Deactivator {
	public static function deactivate() {
		self::clear_cron();
		self::flush_rewrite();
	}

	private static function clear_cron() {
		$timestamp = wp_next_scheduled( Automation::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, Automation::CRON_HOOK );
		}
	}

	private static function flush_rewrite() {
		flush_rewrite_rules();
	}
}
