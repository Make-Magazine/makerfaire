<?php
if ( ! function_exists('is_exists_term') ) {
	function is_exists_term( $term, $taxonomy = '', $parent = null ){
        $is_term_exist = term_exists( $term, $taxonomy, $parent );
        if ( ! $is_term_exist && ! empty($term) && is_numeric($term) ) {
            $is_term_exist = term_exists( (int) $term, $taxonomy, $parent );
        }
		return apply_filters( 'wp_all_import_term_exists', $is_term_exist, $taxonomy, $term, $parent );
	}
}
