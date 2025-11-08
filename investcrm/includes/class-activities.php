<?php
namespace InvestCRM;

class Activities {
	public function register_hooks( Loader $loader ) {
		$loader->add_action( 'save_post_invest_task', array( $this, 'sync_activity' ), 10, 3 );
	}

	public function sync_activity( $post_id, $post, $update ) {
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return;
		}

		global $wpdb;

		$table = $wpdb->prefix . 'investcrm_activities';

		$data = array(
			'title'         => $post->post_title,
			'description'   => $post->post_content,
			'client_id'     => (int) get_post_meta( $post_id, 'client_id', true ),
			'opportunity_id'=> (int) get_post_meta( $post_id, 'opportunity_id', true ),
			'assignee'      => (int) get_post_meta( $post_id, 'assignee', true ),
			'due_at'        => get_post_meta( $post_id, 'due_at', true ),
			'recurrence'    => get_post_meta( $post_id, 'recurrence', true ),
			'updated_at'    => current_time( 'mysql' ),
		);

		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE opportunity_id = %d AND title = %s", $data['opportunity_id'], $post->post_title ) );

		if ( $existing ) {
			$wpdb->update( $table, $data, array( 'id' => $existing ) );
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$data['status']     = 'pending';
			$wpdb->insert( $table, $data );
		}
	}
}
