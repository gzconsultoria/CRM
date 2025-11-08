<?php
namespace InvestCRM;

class Automation {
	const CRON_HOOK = 'investcrm_hourly_event';

	public function register_hooks( Loader $loader ) {
		$loader->add_action( 'set_object_terms', array( $this, 'handle_stage_change' ), 10, 6 );
	}

	public function register_cron() {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'investcrm_hourly', self::CRON_HOOK );
		}
	}

	public function cron_schedules( $schedules ) {
		$schedules['investcrm_hourly'] = array(
			'interval' => HOUR_IN_SECONDS,
			'display'  => __( 'InvestCRM de hora em hora', 'investcrm' ),
		);

		return $schedules;
	}

	public function process_queue() {
		$overdue = $this->get_overdue_tasks();

		foreach ( $overdue as $task ) {
			$this->notify_user( $task );
		}

		$this->schedule_recurring_tasks();
	}

	private function get_overdue_tasks() {
		global $wpdb;

		$table = $wpdb->prefix . 'investcrm_activities';
		$sql   = $wpdb->prepare( "SELECT * FROM {$table} WHERE status = %s AND due_at < %s", 'pending', current_time( 'mysql' ) );

		return $wpdb->get_results( $sql );
	}

	private function notify_user( $task ) {
		if ( empty( $task->assignee ) ) {
			return;
		}

		$user = get_user_by( 'id', $task->assignee );
		if ( ! $user ) {
			return;
		}

		$subject = __( 'Tarefa InvestCRM atrasada', 'investcrm' );
		$message = sprintf(
			/* translators: %1$s: task title, %2$s: due date */
			__( 'A tarefa "%1$s" está atrasada desde %2$s.', 'investcrm' ),
			$task->title,
			mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $task->due_at )
		);

		wp_mail( $user->user_email, $subject, $message );
	}

	private function schedule_recurring_tasks() {
		global $wpdb;

		$table = $wpdb->prefix . 'investcrm_activities';
		$now   = current_time( 'mysql' );

		$tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE recurrence != %s AND due_at < %s", '', $now ) );

		foreach ( $tasks as $task ) {
			$next_due = $this->calculate_next_due( $task->due_at, $task->recurrence );
			if ( ! $next_due ) {
				continue;
			}

			$wpdb->insert(
				$table,
				array(
					'title'         => $task->title,
					'description'   => $task->description,
					'client_id'     => $task->client_id,
					'opportunity_id'=> $task->opportunity_id,
					'assignee'      => $task->assignee,
					'due_at'        => $next_due,
					'recurrence'    => $task->recurrence,
					'status'        => 'pending',
					'created_at'    => current_time( 'mysql' ),
					'updated_at'    => current_time( 'mysql' ),
				)
			);
		}
	}

	private function calculate_next_due( $due_at, $recurrence ) {
		$timestamp = strtotime( $due_at );
		if ( ! $timestamp ) {
			return false;
		}

		switch ( $recurrence ) {
			case 'weekly':
				$timestamp = strtotime( '+1 week', $timestamp );
				break;
			case 'monthly':
				$timestamp = strtotime( '+1 month', $timestamp );
				break;
			case 'quarterly':
				$timestamp = strtotime( '+3 months', $timestamp );
				break;
			case 'yearly':
				$timestamp = strtotime( '+1 year', $timestamp );
				break;
			default:
				return false;
		}

		return gmdate( 'Y-m-d H:i:s', $timestamp + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
	}

	public function handle_stage_change( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ( 'invest_opportunity_stage' !== $taxonomy ) {
			return;
		}

		sort( $tt_ids );
		sort( $old_tt_ids );
		if ( $tt_ids === $old_tt_ids ) {
			return;
		}

		$followup_days = absint( get_option( 'investcrm_auto_followup_days', 3 ) );
		if ( ! $followup_days ) {
			return;
		}

		$post = get_post( $object_id );
		if ( ! $post ) {
			return;
		}

		$due = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) + ( $followup_days * DAY_IN_SECONDS ) );

		global $wpdb;
		$table = $wpdb->prefix . 'investcrm_activities';

		$stage_names = array();
		foreach ( (array) $terms as $term ) {
			$term_obj      = is_numeric( $term ) ? get_term( $term, 'invest_opportunity_stage' ) : get_term_by( 'name', $term, 'invest_opportunity_stage' );
			$stage_names[] = $term_obj && ! is_wp_error( $term_obj ) ? $term_obj->name : $term;
		}

		$wpdb->insert(
			$table,
			array(
				'title'         => sprintf( __( 'Follow-up após etapa: %s', 'investcrm' ), implode( ', ', $stage_names ) ),
				'description'   => __( 'Criado automaticamente após mudança de estágio.', 'investcrm' ),
				'client_id'     => (int) get_post_meta( $object_id, 'client_id', true ),
				'opportunity_id'=> $object_id,
				'assignee'      => (int) get_post_meta( $object_id, 'consultant', true ),
				'due_at'        => $due,
				'recurrence'    => '',
				'status'        => 'pending',
				'created_at'    => current_time( 'mysql' ),
				'updated_at'    => current_time( 'mysql' ),
			)
		);
	}
}
