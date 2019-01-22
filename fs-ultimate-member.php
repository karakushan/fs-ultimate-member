<?php
/*
Plugin Name: FS Ultimate Member
Plugin URI: https://f-shop.top/
Description: Этот плагин добавляет личный кабинет Ultimate Member в ваш интернет магазин на F-SHOP
Author: Vitaliy Karakushan
Version: 1.1
Author URI: https://f-shop.top/
*/

/*
Copyright 2016 Vitaliy Karakushan  (email : karakushan@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

use FS\FS_Orders_Class;
use FS\FS_Payment_Class;


add_filter( 'um_account_page_default_tabs_hook', 'my_custom_tab_in_um', 100 );
function my_custom_tab_in_um( $tabs ) {
	$tabs[800]['orders']['icon']   = 'um-faicon-shopping-cart';
	$tabs[800]['orders']['title']  = 'Мои заказы';
	$tabs[800]['orders']['custom'] = true;

	return $tabs;
}


add_action( 'um_account_tab__orders', 'um_account_tab__mytab' );
function um_account_tab__mytab( $info ) {
	global $ultimatemember;
	extract( $info );

	$output = $ultimatemember->account->get_tab_output( 'orders' );
	if ( $output ) {
		echo $output;
	}
}


add_filter( 'um_account_content_hook_orders', 'um_account_content_hook_mytab' );
function um_account_content_hook_mytab( $output ) {
	ob_start();
	$orders_class = new FS_Orders_Class();
	$user_orders  = $orders_class->get_user_orders();
	$payment      = new FS_Payment_Class();
	$current_user = wp_get_current_user();

	?>
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

			<?php if ( count( $user_orders ) ) : foreach ( $user_orders as $order ) :
				setup_postdata( $order );
				global $post;
				$order = $orders_class->get_order( $order->ID );
//				fs_debug_data($order,'$order','print_r');
				?>
                <tr>
                    <td><?php the_ID(); ?></td>
                    <td><?php the_time( 'd.m.Y' ) ?></td>
                    <td><?php printf('%s <span>%s</span>',$order->data->_amount,fs_currency()) ?></td>
                    <td><?php echo esc_html(get_post_status($order->ID)) ?></td>
                    <td>
                        <a href="<?php echo esc_url( add_query_arg( array( 'order_detail' => $post->ID ), get_permalink( fs_option( 'page_order_detail' ) ) ) ) ?>"
                           class="fs-order-detail">Детали</a></td>
                </tr>
			<?php
			endforeach; endif; ?>

            </tbody>

        </table>

    </div>

	<?php

	$output .= ob_get_contents();
	ob_end_clean();

	return $output;
}
