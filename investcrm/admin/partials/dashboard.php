<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
?>
<div class="wrap investcrm-dashboard">
<h1><?php esc_html_e( 'Central da Consultoria', 'investcrm' ); ?></h1>
<p><?php esc_html_e( 'Resumo das principais métricas da operação.', 'investcrm' ); ?></p>
<div class="investcrm-cards">
<div class="investcrm-card">
<h2><?php esc_html_e( 'Clientes', 'investcrm' ); ?></h2>
<p><?php echo esc_html( $clients->publish ); ?></p>
</div>
<div class="investcrm-card">
<h2><?php esc_html_e( 'Negócios', 'investcrm' ); ?></h2>
<p><?php echo esc_html( $opportunities->publish ); ?></p>
</div>
<div class="investcrm-card">
<h2><?php esc_html_e( 'Tarefas', 'investcrm' ); ?></h2>
<p><?php echo esc_html( $tasks->publish ); ?></p>
</div>
</div>
<div class="investcrm-kanban" id="investcrm-kanban"></div>
</div>
