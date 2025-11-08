<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

?>
<div class="investcrm-portal-widget">
<h2><?php echo esc_html( $data['client']->post_title ); ?></h2>
<ul>
<li><strong><?php esc_html_e( 'Perfil', 'investcrm' ); ?>:</strong> <?php echo esc_html( ucfirst( $data['profile'] ) ); ?></li>
<li><strong><?php esc_html_e( 'AUM', 'investcrm' ); ?>:</strong> R$ <?php echo esc_html( number_format_i18n( (float) $data['aum'], 2 ) ); ?></li>
<li><strong><?php esc_html_e( 'Próxima reunião', 'investcrm' ); ?>:</strong> <?php echo esc_html( $data['next_meeting'] ); ?></li>
<li><strong><?php esc_html_e( 'Rentabilidade', 'investcrm' ); ?>:</strong> <?php echo esc_html( $data['rentability'] ); ?></li>
<?php if ( ! empty( $data['contact_link'] ) ) : ?>
<li><strong><?php esc_html_e( 'Contato', 'investcrm' ); ?>:</strong> <a href="<?php echo esc_url( $data['contact_link'] ); ?>" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Enviar mensagem', 'investcrm' ); ?></a></li>
<?php endif; ?>
</ul>
<a class="button" href="<?php echo esc_url( home_url( '/portal-investidor/' ) ); ?>"><?php esc_html_e( 'Abrir portal completo', 'investcrm' ); ?></a>
</div>
