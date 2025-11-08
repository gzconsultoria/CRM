<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

if ( ! is_user_logged_in() ) {
wp_safe_redirect( wp_login_url( home_url( '/portal-investidor/' ) ) );
exit;
}

$portal = new \InvestCRM\Portal();
$data   = $portal->get_client_portal_data( get_current_user_id() );

get_header();
?>
<div class="investcrm-portal">
<h1><?php esc_html_e( 'Portal do Investidor', 'investcrm' ); ?></h1>
<?php if ( empty( $data ) ) : ?>
<p><?php esc_html_e( 'Nenhuma informação disponível.', 'investcrm' ); ?></p>
<?php else : ?>
<section class="investcrm-portal__summary">
<h2><?php echo esc_html( $data['client']->post_title ); ?></h2>
<ul>
<li><strong><?php esc_html_e( 'AUM', 'investcrm' ); ?>:</strong> R$ <?php echo esc_html( number_format_i18n( (float) $data['aum'], 2 ) ); ?></li>
<li><strong><?php esc_html_e( 'Perfil', 'investcrm' ); ?>:</strong> <?php echo esc_html( ucfirst( $data['profile'] ) ); ?></li>
<li><strong><?php esc_html_e( 'Próxima reunião', 'investcrm' ); ?>:</strong> <?php echo esc_html( $data['next_meeting'] ); ?></li>
<li><strong><?php esc_html_e( 'Rentabilidade', 'investcrm' ); ?>:</strong> <?php echo esc_html( $data['rentability'] ); ?></li>
<?php if ( ! empty( $data['contact_link'] ) ) : ?>
<li><strong><?php esc_html_e( 'Fale com o consultor', 'investcrm' ); ?>:</strong> <a href="<?php echo esc_url( $data['contact_link'] ); ?>" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Abrir canal', 'investcrm' ); ?></a></li>
<?php endif; ?>
</ul>
</section>
<section class="investcrm-portal__tasks">
<h3><?php esc_html_e( 'Atividades recentes', 'investcrm' ); ?></h3>
<?php if ( empty( $data['tasks'] ) ) : ?>
<p><?php esc_html_e( 'Nenhuma tarefa registrada.', 'investcrm' ); ?></p>
<?php else : ?>
<ul>
<?php foreach ( $data['tasks'] as $task ) : ?>
<li>
<strong><?php echo esc_html( $task->title ); ?></strong>
<span><?php echo esc_html( mysql2date( get_option( 'date_format' ), $task->due_at ) ); ?></span>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</section>
<section class="investcrm-portal__documents">
<h3><?php esc_html_e( 'Documentos', 'investcrm' ); ?></h3>
<?php if ( empty( $data['documents'] ) ) : ?>
<p><?php esc_html_e( 'Nenhum documento disponível.', 'investcrm' ); ?></p>
<?php else : ?>
<ul>
<?php foreach ( $data['documents'] as $document ) : ?>
<li><a href="<?php echo esc_url( wp_get_attachment_url( $document->ID ) ); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html( $document->post_title ); ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</section>
<?php endif; ?>
</div>
<?php
get_footer();
