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
	<?php if ( ! empty( $_GET['order_detail'] ) ): ?>
		<?php
		$order_id = intval( $_GET['order_detail'] );
		$order    = FS\FS_Orders_Class::get_order( $order_id )
		?>
    <style>
      .order-detail {
        margin: 21px 0;
        border: 1px solid #eee;
        padding: 10px;
      }
      .order-detail-title {
        margin: 10px 0;
        font-weight: bold;
      }
      .order-detail .thumb img {
        height: auto;
        width: 138px;
      }
    </style>
    <div class="order-detail">
      <div class="order-detail-title">Детали заказа #<?php echo $order_id ?></div>
      <table class="table table-striped">
        <thead>
        <tr>
          <td>#</td>
          <td>Фото</td>
          <td>Название</td>
          <td>Цена</td>
          <td>К-во</td>
          <td>Стоимость</td>
        </tr>
        </thead>
        <tbody>
		<?php if ( ! empty( $order->items ) ): ?>
			<?php foreach ( $order->items as $id => $item ): ?>
            <tr>
              <td><?php echo $id ?></td>
              <td class="thumb"><?php if ( has_post_thumbnail( $id ) )
					  echo get_the_post_thumbnail( $id ) ?></td>
              <td><?php echo get_the_title( $id ) ?></td>
              <td><?php do_action( 'fs_the_price', $id ) ?></td>
              <td><?php echo $item['count'] ?></td>
              <td><?php echo $order->sum ?> <?php echo fs_currency() ?></td>
            </tr>
			<?php endforeach; ?>
		<?php endif; ?>

        </tbody>
      </table>
    </div>

	<?php endif; ?>
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
          <td><a href="<?php echo esc_url( add_query_arg( array( 'order_detail' => $post->ID ) ) ) ?>"
                 class="fs-order-detail">Детали</a></td>
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
