<?php
/* Rewrite Rules */
function maker_url_vars($rules) {
  $newrules = array();

  //classic entry page for flagship faires — slug-plus-ID, legacy numeric still matches
  $newrules['maker/entry/([A-Za-z0-9\-]*?)-?(\d+)(?:/(edit))?/?$'] =
    'index.php?post_type=page&pagename=entry-page-do-not-delete'
    . '&e_slug=$matches[1]&e_id=$matches[2]&edit_slug=$matches[3]';

  //classic schedule page
  $newrules['([^\/]*)/schedule/([^/]+)/?$'] = 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]';
  $newrules['([^\/]*)/schedule/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]&sched_type=$matches[3]';
  
  //create maker signs
  $newrules['maker-sign/(\d*)/?(.*)$/?'] = 'index.php?makersign=true&eid=$matches[1]&faire=$matches[2]';
  $newrules['^maker-sign/([^/]*)/([^/]*)$'] = '/wp-content/themes/makerfaire/generate_pdf/makersigns.php?eid=$matches[1]&faire=$matches[2]';        

  //create maker load in pass
  $newrules['loadin/(\d*)/?(.*)$/?'] = 'index.php?loadin=true&eid=$matches[1]&type=$matches[2]';
  $newrules['^loadin/([^/]*)/([^/]*)$'] = '/wp-content/themes/makerfaire/generate_pdf/loadInPass.php?eid=$matches[1]&type=$matches[2]';        
  
  //kendo scheduler - page-mfscheduler.php
  $newrules['^mfscheduler/([^/]*)/?'] = 'index.php?pagename=mfscheduler&faire_id=$matches[1]';
  $newrules['^mfscheduler-tasks/?'] = 'index.php?pagename=mfscheduler-tasks';

  return $newrules + $rules;
}

add_filter('rewrite_rules_array', 'maker_url_vars');

/* Query Vars */
add_filter( 'query_vars', 'makerfaire_register_query_var' );
function makerfaire_register_query_var( $vars ) {
    $vars[] = 'type';       //page-api.php, page-mfapi.php
    $vars[] = 'e_id';       //page-entry.php
    $vars[] = 'edit_slug';  //page-entry.php   
    $vars[] = 'e_slug';     //page-entry.php — canonical check only
    $vars[] = 'faire_id';   //page-mfscheduler.php
    $vars[] = 'token';      //page-maker-checkin.php, page-mfscheduler.php, page-onsite-checkin.php, page-onsite-pinning.php
    $vars[] = 'makersign';  //classes/makerfaire-helper.php
    $vars[] = 'loadin';     //classes/makerfaire-helper.php
    $vars[] = 'faire';      //generate_pdf/makersigns.php
    $vars[] = 'eid';        //generate_pdf/makersigns.php
    $vars[] = "sched_type"; //page-schedule.php
        
    return $vars;
}

function custom_rewrite_tag() {  
  add_rewrite_tag('%faire_id%', '([^&]+)');  
  add_rewrite_tag('%entryslug%', '([^&]+)');  //page-entryarchives.php
}

add_action('init', 'custom_rewrite_tag', 10, 0);

/* ---- Maker entry URLs HELPER FUNCTIONS -------------------------------------------- */

define( 'MF_TITLE_FIELD', 151 );

function mf_slug_from_title( $title ) {
    $slug = sanitize_title( $title );
    if ( strlen( $slug ) > 60 ) {
        $slug = rtrim( substr( $slug, 0, 60 ), '-' );
    }
    return $slug;
}

function mf_entry_path( $title, $entry_id, $edit = false ) {
    $slug = mf_slug_from_title( $title );
    $path = '/maker/entry/' . ( $slug ? $slug . '-' : '' ) . (int) $entry_id . '/';
    if ( $edit ) {
        $path .= 'edit/';
    }
    return $path;
}

function mf_entry_exhibit_types( $entry, $form = null ) {
    if ( ! is_array( $entry ) || empty( $entry ) ) {
        return array();
    }
    if ( null === $form ) {
        $form = GFAPI::get_form( $entry['form_id'] );
    }
    if ( ! is_array( $form ) ) {
        return array();
    }
    $formType = isset( $form['form_type'] ) ? $form['form_type'] : '';
    $types    = array();

    if ( 'Master' === $formType ) {
        foreach ( $entry as $key => $value ) {
            if ( strpos( (string) $key, '339.' ) === 0 && $value !== '' && $value !== null ) {
                $types[] = ( stripos( $value, 'sponsor' ) !== false ) ? 'Exhibit' : $value;
            }
        }
    } else {
        $types[] = ( stripos( $formType, 'sponsor' ) !== false ) ? 'Exhibit' : $formType;
    }
    return array_unique( $types );
}

/**
 * The single public-visibility rule. Deliberately ignores $adminView and
 * $makerEdit — sitemap, REST, and the 301 must all agree for every visitor.
 */
function mf_entry_is_public( $entry, $form = null ) {
    if ( ! is_array( $entry ) || empty( $entry ) ) {
        return false;
    }
    if ( ! isset( $entry[151] ) || '' === trim( (string) $entry[151] ) ) {
        return false;
    }
    if ( ! isset( $entry['status'] ) || 'active' !== $entry['status'] ) {
        return false;
    }
    if ( ! isset( $entry[303] ) || 'Accepted' != $entry[303] ) {
        return false;
    }
    foreach ( $entry as $key => $value ) {
        if ( strpos( (string) $key, '304.' ) === 0 && 'no-public-view' === $value ) {
            return false;
        }
    }
    $types = mf_entry_exhibit_types( $entry, $form );
    foreach ( array( 'Show Management', 'Not Sure Yet', 'Other' ) as $blocked ) {
        if ( in_array( $blocked, $types, true ) ) {
            return false;
        }
    }
    return true;
}

/**
 * Memoized fetch — template_redirect and page-entry.php both need the entry.
 * Returns array on success, null on failure/WP_Error.
 */
function mf_get_entry( $entry_id ) {
    static $cache = array();

    $entry_id = (int) $entry_id;
    if ( ! $entry_id ) {
        return null;
    }
    if ( ! array_key_exists( $entry_id, $cache ) ) {
        $entry = GFAPI::get_entry( $entry_id );
        $cache[ $entry_id ] = is_wp_error( $entry ) ? null : $entry;
    }
    return $cache[ $entry_id ];
}

/* Canonical redirect — must live on template_redirect rather than in
 * page-entry.php: WP 6.8+ exits before the template include on HEAD requests,
 * and running here also skips the remote @getimagesize() call on URLs that
 * are about to 301 away. */
add_action( 'template_redirect', function() {
    $entry_id = (int) get_query_var( 'e_id' );
    if ( ! $entry_id || ! is_page( 'entry-page-do-not-delete' ) ) {
        return;
    }

    // HEAD is safe/idempotent so it redirects too. POST does not — a 301 would
    // convert it to GET and silently drop a GravityView edit submission.
    $reqMethod = strtoupper( $_SERVER['REQUEST_METHOD'] ?? 'GET' );
    if ( ! in_array( $reqMethod, array( 'GET', 'HEAD' ), true ) ) {
        return;
    }

    $entry = mf_get_entry( $entry_id );
    if ( ! mf_entry_is_public( $entry ) ) {
        return; // let the template render "Invalid Entry"
    }

    $title = rgar( $entry, (string) MF_TITLE_FIELD );
    if ( (string) get_query_var( 'e_slug' ) !== mf_slug_from_title( $title ) ) {
        $isEdit = ( 'edit' === (string) get_query_var( 'edit_slug' ) );
        $target = home_url( mf_entry_path( $title, $entry_id, $isEdit ) );
        if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
            $target .= '?' . $_SERVER['QUERY_STRING'];
        }
        wp_safe_redirect( $target, 301 );
        exit;
    }
}, 1 );