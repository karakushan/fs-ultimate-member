<?php
/**
 * @package FS Ultimate Member
 * @version 1.0
 */
/*
Plugin Name: FS Ultimate Member
Plugin URI: https://f-shop.top/
Description: интеграция с плагином Ultimate Member
Author: Vitaliy Karakushan
Version: 1.0
Author URI: https://f-shop.top/
*/


/* add new tab called "mytab" */

add_filter( 'um_account_page_default_tabs_hook', 'my_custom_tab_in_um', 100 );
function my_custom_tab_in_um( $tabs ) {
	$tabs[800]['orders']['icon']   = 'um-faicon-shopping-cart';
	$tabs[800]['orders']['title']  = 'Мои заказы';
	$tabs[800]['orders']['custom'] = true;

	return $tabs;
}

/* make our new tab hookable */

add_action( 'um_account_tab__orders', 'um_account_tab__mytab' );
function um_account_tab__mytab( $info ) {
	global $ultimatemember;
	extract( $info );

	$output = $ultimatemember->account->get_tab_output( 'orders' );
	if ( $output ) {
		echo $output;
	}
}

/* Finally we add some content in the tab */

add_filter( 'um_account_content_hook_orders', 'um_account_content_hook_mytab' );
function um_account_content_hook_mytab( $output ) {
	ob_start();

	$user_orders = \FS\FS_Orders_Class::get_user_orders();
	?>
	<div class="um-account-heading uimob340-hide uimob500-hide"><i class="um-faicon-shopping-cart"></i>Мои заказы</div>
	<div class="um-field">
		<table class="table table-striped">
			<thead>
			<tr>
				<td>№</td>
				<td>Дата</td>
				<td>Сумма</td>
				<td>Статус</td>
				<td>Детали</td>
			</tr>
			</thead>
			<tbody>

			<?php if ( $user_orders->have_posts() ) : while ( $user_orders->have_posts() ) : $user_orders->the_post();
				global $post;
				$order = FS\FS_Orders_Class::get_order( $post->ID )
				?>
				<tr>
					<td><?php the_ID(); ?></td>
					<td><?php the_time( 'd.m.Y' ) ?></td>
					<td><?php echo $order->sum ?><?php echo fs_currency() ?></td>
					<td><?php echo $order->status ?></td>
					<td>Детали</td>
				</tr>
			<?php endwhile;
				wp_reset_query(); ?>
			<?php else: ?>
			<?php endif; ?>

			</tbody>

		</table>

	</div>

	<?php

	$output .= ob_get_contents();
	ob_end_clean();

	return $output;
}
