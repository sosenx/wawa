<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.2
 */
global $product;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! comments_open() )
	return;
?>
<div id="reviews">
	<div id="comments">
		<h2><?php
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_rating_count() ) )
				printf( _n( '%s review for %s', '%s reviews for %s', $count, ETHEME_DOMAIN ), $count, get_the_title() );
			else
				_e( 'Reviews', 'woocommerce' );
		?></h2>

		<?php if ( have_comments() ) : ?>

			<ul class="comments-list">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'etheme_comments' ) ) ); ?>
			</ul>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="pagination-cubic">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '<i class="fa fa-angle-double-left"></i>',
					'next_text' => '<i class="fa fa-angle-double-right"></i>',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php _e( 'There are no reviews yet.', ETHEME_DOMAIN ); ?></p>

		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>

		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
					$commenter = wp_get_current_commenter();
					
			        $req = get_option('require_name_email');
			        $reqT = '<span class="required">*</span>';
		        	$aria_req = ($req ? " aria-required='true'" : ' ');

					$comment_form = array(
						'title_reply'          => '<span>' . __('Leave a reply', ETHEME_DOMAIN) . '</span>',
						'title_reply_to'       => '<span>' . __( 'Leave a Reply to %s', ETHEME_DOMAIN ). '</span>',
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
				            'author' => '<div class="form-group comment-form-author">'.
				                            '<label for="author" class="control-label">'.__('Name', ETHEME_DOMAIN).' '.($req ? $reqT : '').'</label>'.
				                            '<input id="author" name="author" type="text" class="form-control ' . ($req ? ' required-field' : '') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30" ' . $aria_req . '>'.
				                        '</div>',
				            'email' => '<div class="form-group comment-form-email">'.
				                            '<label for="email" class="control-label">'.__('Email', ETHEME_DOMAIN).' '.($req ? $reqT : '').'</label>'.
				                            '<input id="email" name="email" type="text" class="form-control ' . ($req ? ' required-field' : '') . '" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" ' . $aria_req . '>'.
				                        '</div>',
						),
						'label_submit'  => __( 'Submit', ETHEME_DOMAIN ),
						'logged_in_as'  => '',
						'comment_field' => ''
					);

					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Your Rating', ETHEME_DOMAIN ) .'</label><select name="rating" id="rating">
							<option value="">' . __( 'Rate&hellip;', ETHEME_DOMAIN ) . '</option>
							<option value="5">' . __( 'Perfect', ETHEME_DOMAIN ) . '</option>
							<option value="4">' . __( 'Good', ETHEME_DOMAIN ) . '</option>
							<option value="3">' . __( 'Average', ETHEME_DOMAIN ) . '</option>
							<option value="2">' . __( 'Not that bad', ETHEME_DOMAIN ) . '</option>
							<option value="1">' . __( 'Very Poor', ETHEME_DOMAIN ) . '</option>
						</select></p>';
					}

					$comment_form['comment_field'] .= '<div class="form-group"><label for="comment" class="control-label">'.__('Your Review', ETHEME_DOMAIN).'</label><textarea class="form-control required-field"  id="comment" name="comment" cols="45" rows="12" aria-required="true"></textarea></div>';

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', ETHEME_DOMAIN ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>