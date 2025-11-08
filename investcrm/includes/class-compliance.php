<?php
namespace InvestCRM;

class Compliance {
	public function register_hooks() {
		add_action( 'transition_post_status', array( $this, 'log_status_change' ), 10, 3 );
		add_action( 'added_post_meta', array( $this, 'log_meta_change' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'log_meta_change' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'log_meta_deletion' ), 10, 4 );
		add_action( 'user_register', array( $this, 'assign_default_role' ) );
	}

	public function log_status_change( $new_status, $old_status, $post ) {
		if ( $new_status === $old_status ) {
			return;
		}

		$this->log_action(
			'change_status',
			$post->post_type,
			$post->ID,
			array(
				'old_status' => $old_status,
				'new_status' => $new_status,
			)
		);
	}

	public function log_meta_change( $meta_id, $object_id, $meta_key, $_meta_value ) {
		$post = get_post( $object_id );
		if ( ! $post ) {
			return;
		}

		$this->log_action(
			'update_meta',
			$post->post_type,
			$object_id,
			array(
				'key'   => $meta_key,
				'value' => maybe_serialize( $_meta_value ),
			)
		);
	}

	public function log_meta_deletion( $meta_ids, $object_id, $meta_key, $_meta_value ) {
		$post = get_post( $object_id );
		if ( ! $post ) {
			return;
		}

		$this->log_action(
			'delete_meta',
			$post->post_type,
			$object_id,
			array(
				'key' => $meta_key,
			)
		);
	}

	public function assign_default_role( $user_id ) {
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return;
		}

		if ( ! array_intersect( array( 'investcrm_consultant', 'investcrm_manager' ), $user->roles ) ) {
			$user->add_role( 'investcrm_consultant' );
		}
	}

	private function log_action( $action, $object_type, $object_id, $payload ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'investcrm_audit_log',
			array(
				'actor'       => get_current_user_id(),
				'action'      => $action,
				'object_type' => $object_type,
				'object_id'   => $object_id,
				'payload'     => maybe_serialize( $payload ),
				'created_at'  => current_time( 'mysql' ),
			)
		);
	}
}
