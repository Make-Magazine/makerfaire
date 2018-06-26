<?php
/**
 * Template Name: Makers By Topic
 */
global $wp_query;

//get faire ID (default to BA15
$faire = (isset($_GET['faire'])?sanitize_text_field($_GET['faire']):'BA15');
$results            = $wpdb->get_results('SELECT * FROM wp_mf_faire where faire= "'.strtoupper($faire).'"');
$url_sub_path       = $results[0]->url_path;
$faire_name         = $results[0]->faire_name;
$current_form_ids   = explode(',',$results[0]->form_ids);
//exclude these forms
$exclude_form = explode(',',$results[0]->non_public_forms);
foreach ($exclude_form as $exFormID){
  if(($key = array_search($exFormID, $current_form_ids)) !== false) {
    unset($current_form_ids[$key]);
  }
}
$topic_slug = $wp_query->query_vars['t_slug'];

$category = get_term_by('slug',$topic_slug,'makerfaire_category');

//change to search by taxonomy code
$search_category = $category->term_id;
$currentpage = $wp_query->query_vars['offset'];
$page_size = 30;
$offset=($currentpage-1)*$page_size;
$total_count = 0;
$f = $wp_query->query_vars['f'];

$sorting_criteria = array('key' => '151', 'direction' => 'ASC' );
$paging_criteria  = array('offset' => $offset, 'page_size' => $page_size );

//search by primary category
$search_criteria['status'] = 'active';
$search_criteria['field_filters'][] = array( '321' => '1', 'value' => $search_category);
$search_criteria['field_filters'][] = array( '303' => '1', 'value' => 'Accepted');

$entries =  GFAPI::get_entries( $current_form_ids, $search_criteria, $sorting_criteria, $paging_criteria, $total_count);

$current_url = '/'.$f.'/meet-the-makers/topics/'.$topic_slug;

// Load Categories
$cats_tags = get_categories(array('hide_empty' => 0));

get_header(); ?>
<div class="clear"></div>

<div class="container">
	<div class="row">
		<div class="content col-md-8">

			<div class="row padbottom">
				<div class="col-md-8">
          <a href="/<?php echo $url_sub_path;?>/meet-the-makers/">&#65513; Look for More Makers</a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h1><?php echo $faire_name;?> Makers</h1>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h3 class="nomargins">Topic: <?php echo $category->name;?></h3>
				</div>
			</div>
			<div class="clear"></div>
			<div class="clear"></div>

			<?php foreach ($entries as $entry) {
        //check if entry marked for no public view
        $validEntry = true;
        foreach($entry as $key=>$field ) {
          $pos = strpos($key, '304.');
          if ($pos !== false) {
            if($field=='no-public-view')  $validEntry = false;
          }
        }
        if($validEntry) {
          $project_name = isset($entry['151']) ? $entry['151']  : '';
          $entry_id = isset($entry['id']) ? $entry['id']  : '';
          ?>
          <hr>
          <div class="row">
            <div class="col-md-8">
              <h3 class="nomargins maker-results"><a href="/maker/entry/<?php echo $entry_id; ?>"><?php echo $project_name;?></a></h3>
            </div>
          </div>
      <?php
        }
      }?>
			<hr>

			<?php
			if ($total_count > 30) {
				echo '<div class="row padtop padbottom">
					<div class="col-md-8">
						' . pagination_display($current_url,$currentpage,$page_size,$total_count) . '
					</div>
				</div>';
			}
			?>

		</div>
		<!--Content-->

		<?php get_sidebar(); ?>

	</div>
	<div class="clear"></div>
	<div class="clear"></div>

</div><!--Container-->

<?php get_footer();

/* Support Functions */
function search_entries_bytopic( $form_id, $search_criteria = array(), $sorting = null, $paging = null, &$total_count ) {
  global $wpdb;
  $sort_field = isset( $sorting['key'] ) ? $sorting['key'] : 'date_created'; // column, field or entry meta

	//initializing rownum
	$sql = sort_by_field_query( $form_id, $search_criteria, $sorting, $paging);
	$sqlcounting = sort_by_field_count( $form_id, $search_criteria);

	GFCommon::log_debug( $sql );
	GFCommon::log_debug( $sqlcounting );
	//getting results

	$results = $wpdb->get_results( $sql );
  $leads = GFFormsModel::build_lead_array( $results );

	$results_count  = $wpdb->get_row( $sqlcounting );
	$total_count    = $results_count->total_count;

	return $leads;
}


function sort_by_field_query( $form_id, $searching, $sorting, $paging ) {
	global $wpdb;
	$sort_field_number = rgar( $sorting, 'key' );
	$sort_direction    = isset( $sorting['direction'] ) ? $sorting['direction'] : 'DESC';
	$offset          = isset( $paging['offset'] ) ? $paging['offset'] : 0;
	$page_size       = isset( $paging['page_size'] ) ? $paging['page_size'] : 20;
	$search_key          = isset( $searching['key'] ) ? $searching['key'] : '';
	$search_value       = isset( $searching['value'] ) ? $searching['value'] : '';

	if ( ! is_numeric( $sort_field_number ) || ! is_numeric( $offset ) || ! is_numeric( $page_size ) ) {
		return '';
	}
	$lead_detail_table_name = 'wp_gf_entry_meta';
	$lead_table_name        = 'wp_gf_entry';

	$field_number_min = $sort_field_number - 0.0001;
	$field_number_max = $sort_field_number + 0.0001;

	$searchfield_number_min = $search_key - 0.0001;
	$searchfield_number_max = $search_key + 0.9999;
	$accepted_criteria = "(field_number BETWEEN '302.9999' AND '303.9999' AND value = 'Accepted' )";


	$sql = "

	SELECT sorted.sort,sorted.value, l.*, d.meta_key, d.meta_value
            FROM $lead_table_name l
            INNER JOIN $lead_detail_table_name d ON d.entry_id = l.id
			INNER JOIN (
            SELECT @rownum:=@rownum+1 as sort, l.entry_id as id, l.meta_value
			FROM (Select @rownum:=0) r, wp_gf_entry_meta l
				INNER JOIN (
						SELECT
						entry_id as id
						from $lead_detail_table_name
						WHERE (meta_key BETWEEN '$searchfield_number_min' AND '$searchfield_number_max' AND value IN ( '$search_value' ))
						AND form_id in ($form_id)
					) filtered on l.entry_id=filtered.id
				INNER JOIN
				    (
				    SELECT
						entry_id as id
						from $lead_detail_table_name
						WHERE $accepted_criteria
						AND form_id in ($form_id)
						) accepted on l.entry_id=accepted.id
				WHERE meta_key  between $field_number_min AND $field_number_max AND l.form_id in ($form_id)
		ORDER BY l.meta_value ASC LIMIT $offset,$page_size ) sorted on sorted.id=l.id
        order by sorted.sort
	";

	return $sql;
}

function sort_by_field_count( $form_id, $searching ) {
	global $wpdb;
	$search_value       = isset( $searching['value'] ) ? $searching['value'] : '';
	$search_key          = isset( $searching['key'] ) ? $searching['key'] : '';
	$searchfield_number_min = $search_key - 0.0001;
	$searchfield_number_max = $search_key + 0.9999;

	$lead_detail_table_name = 'wp_gf_entry_meta';
	$lead_table_name        = 'wp_gf_entry';

	$accepted_criteria = "(field_number BETWEEN '302.9999' AND '303.9999' AND value = 'Accepted' )";


	$sql = "SELECT count( distinct accepted.id ) as total_count
						from $lead_detail_table_name
						INNER JOIN
				    (
				    SELECT
						lead_id as id
						from $lead_detail_table_name
						WHERE $accepted_criteria
						AND form_id in ($form_id)
						) accepted on $lead_detail_table_name.lead_id=accepted.id
						WHERE (field_number BETWEEN '$searchfield_number_min' AND '$searchfield_number_max' AND value IN ( '$search_value' ))
						AND form_id in ($form_id)
	";

	return $sql;

}

function pagination_display ($current_url,$current_page,$pagesize,$total_count) {
global $faire;
$pages = ceil($total_count / $pagesize);

?>
<style>
.pagination {
	background: none;
	border-radius: 0;
	bottom: 0;
	line-height: 22px;
	margin: 0;
	padding: 0;
	text-align: center;
	width: 100%;
}
.pagination li  {
	display: block;
	margin:0px;
	float:left;
}
.pagination li a {
	background: none;
	border-radius: 0;
	display: block;
	height: auto;
	overflow: none;
	text-indent: 0;
	transition: all 0.3s ease 0s;
	width: auto;
}
</style>

<div class="btn-group pull-left">
	<nav>
		<ul class="pagination pull-left">
			<?php if ($current_page > 1) : ?>
			<li <?php if ($current_page == 1) echo 'class = "disabled"'; ?>><a <?php if ($current_page == 1) echo 'class = "disabled"'; ?> href="<?php echo $current_url?>/<?php echo ($current_page == 1) ? $current_page.'#': $current_page-1; ?><?php echo '?faire='.$faire;?>">&laquo;</a></li>
			<?php endif; ?>
			<?php for($i = 1;$i <= $pages;$i++): ?>
			<li  <?php if ($current_page == $i) echo 'class = "active"'; ?> ><a href="<?php echo $current_url?>/<?php echo $i.'?faire='.$faire;?>"><?php echo $i?></a></li>
			<?php endfor;?>
			<?php if ($current_page < $pages) : ?>
			<li <?php if ($current_page == $pages) echo 'class = "disabled"'; ?>><a <?php if ($current_page == $pages) echo 'class = "disabled"'; ?> href="<?php echo $current_url?>/<?php echo ($current_page == $pages) ? $current_page.'#': $current_page+1;?><?php echo '?faire='.$faire;?>">&raquo;</a></li>
			<?php endif; ?>

		</ul>
	</nav>
</div>

<?php
}
?>