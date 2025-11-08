<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
?>
<div class="wrap investcrm-automation">
<h1><?php esc_html_e( 'Automação', 'investcrm' ); ?></h1>
<p><?php esc_html_e( 'Configure gatilhos automáticos para tarefas e comunicações.', 'investcrm' ); ?></p>
<form method="post" action="options.php">
<?php
settings_fields( 'investcrm_automation' );
do_settings_sections( 'investcrm_automation' );
submit_button();
?>
</form>
</div>
