<?php
/**
 * EasySocialShareButtons DisplayMethod: PostBar
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.5
 *
 */

class ESSBDisplayMethodPostBar {
	
	public static function generate_postbar_code($options, $share_buttons, $total_shares_code) {
		global $post;
		
		$output = '';
		
		$postbar_deactivate_prevnext = essb_object_bool_value($options, 'postbar_deactivate_prevnext');
		$postbar_deactivate_progress = essb_object_bool_value($options, 'postbar_deactivate_progress');
		$postbar_deactivate_title = essb_object_bool_value($options, 'postbar_deactivate_title');
		
		$postbar_deactivate_share = essb_object_bool_value($options, 'postbar_deactivate_share');
			
		$postbar_activate_category = essb_object_bool_value($options, 'postbar_activate_category');
		$postbar_activate_author = essb_object_bool_value($options, 'postbar_activate_author');
		$postbar_activate_total = essb_object_bool_value($options, 'postbar_activate_total');
		$postbar_activate_comments = essb_object_bool_value($options, 'postbar_activate_comments');
		$postbar_activate_time = essb_object_bool_value($options, 'postbar_activate_time');
		$postbar_activate_total = essb_object_bool_value($options, 'postbar_activate_total');
		$postbar_activate_time_words = essb_object_value($options, 'postbar_activate_time_words');
			
		$output .= '<div id="essb-postbar" class="essb-postbar">';
			
		// progress bar
		if (!$postbar_deactivate_progress) {
			$output .= '<div class="essb-postbar-progress-container"><span class="essb-postbar-progress-bar"></span></div>';
		}
			
		// main post bar content
		$output .= '<div class="essb-postbar-container">';
			
		// prev post icon
		if (!$postbar_deactivate_prevnext) {
		
			$prev_post = get_adjacent_post( true, '', true, 'category');
		
			if ( is_a( $prev_post, 'WP_Post' ) ) {
				$output .= '<div class="essb-postbar-prev-post">';
				$output .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'"><i class="essb_icon_prev"></i></a>';
					
				$output .= '<div class="essb_prev_post">';
				$output .= '<div class="essb_prev_post_info">';
				$output .= '<div class="essb-postbar-close-postpopup essb-postbar-close-prev">X</div>';
				$output .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'">';
				$output .= '<span class="essb_title">';
				$output .= '<span class="essb_tcategory">';
				$post_title = mb_substr(get_the_title( $prev_post->ID ),0,100);
				$output .= $post_title;
				if (strlen($post_title) >48){
					$output .= '&hellip;';
				}
				$output .= '</span>';
				
				$output .= '<span class="essb_category">';
				if(is_singular( 'post' )) {
					$category = get_the_category($prev_post->ID);
					$output .= $category[0]->cat_name;
				}
				$output .= '</span>';
				
				$working_post_content = $prev_post->post_content;
		
				$post_shortdesc = $prev_post->post_excerpt;
				if ($post_shortdesc != '') {
					$working_post_content = $post_shortdesc;
				}
		
		
				$working_post_content = strip_tags ( $working_post_content );
				$working_post_content = preg_replace( '/\s+/', ' ', $working_post_content );
				$working_post_content = strip_shortcodes($working_post_content);
				$working_post_content = trim ( $working_post_content );
				$working_post_content = mb_substr ( $working_post_content, 0, 150 );
				$working_post_content .= '&hellip;';
				$output .= '<span class="essb_tdesc">';
				$output .= $working_post_content;
				$output .= '</span>';
				$output .= '</span>';
				$output .= '</a>';				
				$output .= '</div>';
				$output .= '</div>'; // post info
		
				$output .= '</div>';
			}
		
		
		}
			
		// category bar
		if ($postbar_activate_category) {
			$category = get_the_category($post->ID);
		
			$output .= '<div class="essb-postbar-category">';
			$output .= '<a href="'.esc_url(get_category_link($category[0]->cat_ID)).'">'.$category[0]->cat_name.'</a>';
			$output .= '</div>';
		}
			
		$output .= '<div class="essb-postbar-titleholder">';
		
		if (!$postbar_deactivate_title || $postbar_activate_author) {
			$output .= '<div class="inner-content">';
		}
			
		if (!$postbar_deactivate_title) {
			$output .= '<h2>'.esc_attr($post->post_title).'</h2>';
		}
			
		if ($postbar_activate_author) {
			$author_id = get_post_field( 'post_author', $post->ID );
			$author_name = get_the_author_meta( 'display_name', $author_id );
		
			$output .= '<span class="essb-postbar-author">'.esc_html__('by', 'easy-social-share-buttons').' '.$author_name.'</span>';
		
		}
		
		if (!$postbar_deactivate_title || $postbar_activate_author) {
			$output .= '</div>';
		}
			
		$output .= '</div>'; // titleholder
			
		$output .= '<div class="essb-postbar-right">';
			
		$one_icon = false;
		$output .= '<div class="essb-posbar-icons-container">';
			
		if ($postbar_activate_total) {
			$output .= '<span class="essb-postbar-totalshares"><i class="essb_icon_share"></i><span class="essb-postbar-number">'.$total_shares_code.'</span></span>';
			$one_icon = true;
		}
		if ($postbar_activate_comments) {
			$comment_count = get_post_field( 'comment_count', $post->ID );
		
			$output .= '<span class="essb-postbar-comments"><i class="essb_icon_comments"></i><span class="essb-postbar-number">'.$comment_count.'</span></span>';
			$one_icon = true;
		}
		if ($postbar_activate_time) {
			$content = get_post_field( 'post_content', $post->ID );
			$word_count = str_word_count( strip_tags( $content ) );
			if ($postbar_activate_time_words != '') {
				$ttr = round($word_count / $postbar_activate_time_words);
			}
			else {
				$ttr = round($word_count / 250);
			}
		
			if ($ttr == 0 ){
				$ttr = '<1';
			}
		
			$output .= '<span class="essb-postbar-time"><i class="essb_icon_clock"></i><span class="essb-postbar-number">'.$ttr.' '.esc_html__('min', 'easy-social-share-buttons').'</span></span>';
			$one_icon = true;
		}
		if (!$one_icon) {
			$output .= '<span class="essb-postbar-totalshares"><span class="essb-postbar-number">&nbsp;</span></span>';
		}
		
		$output .= '</div>'; // icons
		
		
		if (!$postbar_deactivate_share) {
    		$output .= '<div class="essb-postbar-buttons">';
    			
    		$output .= $share_buttons;
    		
    		$output .= '</div>'; // buttons
		}
			
		if (!$postbar_deactivate_prevnext) {
			$next_post = get_adjacent_post( true, '', false, 'category');
				
			if ( is_a( $next_post, 'WP_Post' ) ) {
				$output .= '<div class="essb-postbar-next-post">';
				$output .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'"><i class="essb_icon_next"></i></a>';
					
				$output .= '<div class="essb_next_post">';
					
				$output .= '<div class="essb_next_post_info">';
				$output .= '<div class="essb-postbar-close-postpopup essb-postbar-close-next">X</div>';			
				$output .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'">';	
				$output .= '<span class="essb_title">';
				$output .= '<span class="essb_tcategory">';
				$post_title = mb_substr(get_the_title( $next_post->ID ),0,80);
				$output .= $post_title;
				if (strlen($post_title) >48){
					$output .= '&hellip;';
				}
				$output .= '</span>';

				$output .= '<span class="essb_category">';
				if(is_singular( 'post' )) {
					$category = get_the_category($next_post->ID);
					$output .= $category[0]->cat_name;
				}
				$output .= '</span>';
				$working_post_content = $next_post->post_content;
					
				$post_shortdesc = $next_post->post_excerpt;
				if ($post_shortdesc != '') {
					$working_post_content = $post_shortdesc;
				}
					
					
				$working_post_content = strip_tags ( $working_post_content );
				$working_post_content = preg_replace( '/\s+/', ' ', $working_post_content );
				$working_post_content = strip_shortcodes($working_post_content);
				$working_post_content = trim ( $working_post_content );
				$working_post_content = mb_substr ( $working_post_content, 0, 150 );
				$working_post_content .= '&hellip;';
				$output .= '<span class="essb_tdesc">';
				$output .= $working_post_content;
				$output .= '</span>';
				$output .= '</span>';
				$output .= '</a>';
				$output .= '</div>';
				$output .= '</div>'; // post info
		
				$output .= '</div>';
			}
				
		}
			
		$output .= '</div>'; // right
			
		$output .= '</div>'; // container
			
			
		$output .= '</div>';
		
		return $output;
	}
	
}