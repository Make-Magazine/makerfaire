<?php
/**
 * Template Name: Makers By Keyword Search
 */
global $wp_query;
//get faire ID (default to BA15
$faire = (isset($_GET['faire'])?sanitize_text_field($_GET['faire']):'BA15');
$results = $wpdb->get_results('SELECT * FROM wp_mf_faire where faire= "'.strtoupper($faire).'"');
$faire_name = $results[0]->faire_name;
$current_form_ids   = explode(',',$results[0]->form_ids);

//exclude these forms
$exclude_form = explode(',',$results[0]->non_public_forms);
foreach ($exclude_form as $exFormID){
  if(($key = array_search($exFormID, $current_form_ids)) !== false) {
    unset($current_form_ids[$key]);
  }
}

//$search_term = urldecode($wp_query->query_vars['s_keyword']);
$search_term=$_GET["s_term"];
$currentpage = $wp_query->query_vars['offset'];
$page_size = 30;
$offset=($currentpage-1)*$page_size;
$total_count = 0;
$f = $wp_query->query_vars['f'];
//$search_criteria = array( 'key' => '147', 'value' =>  $search_term);
$search_criteria['status'] = 'active';
$search_criteria['field_filters'][] = array( '151' => '1', 'operator' => 'contains','value' => $search_term);
$search_criteria['field_filters'][] = array( '15' => '1', 'operator' => 'contains','value' => $search_term);
$search_criteria['field_filters'][] = array( '303' => '1', 'value' => 'Accepted');

$sorting_criteria = array('key' => '151', 'direction' => 'ASC' );
$paging_criteria = array('offset' => $offset, 'page_size' => $page_size );
//$entries=search_entries_bytopic($current_form_ids,$search_criteria,$sorting_criteria,$paging_criteria,$total_count);
$entries =  GFAPI::get_entries( $current_form_ids, $search_criteria, $sorting_criteria, $paging_criteria, $total_count);

$current_url = '/'.$f.'/meet-the-makers/search/';
// Load Categories
$cats_tags = get_categories(array('hide_empty' => 0));


get_header(); ?>
<div class="clear"></div>

<div class="container">
	<div class="row">
		<div class="content col-md-8">

			<div class="row padbottom">
				<div class="col-md-8">
					<a href="/<?php echo str_replace(' ','-', strtolower($faire_name));?>/meet-the-makers/">&#65513; Look for More Makers</a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h1><?php echo $faire_name;?> Makers</h1>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h3 class="nomargins">Search: "<?php echo  $search_term; ?>", <span class="text-muted"><?php echo $total_count; echo ($total_count == 1) ? ' result' : ' results'; ?></span></h3>
				</div>
			</div>
			<div class="clear"></div>
			<div class="clear"></div>

			<?php foreach ($entries as $entry) :
			$project_name = isset($entry['151']) ? $entry['151']  : '';
			$entry_id = isset($entry['id']) ? $entry['id']  : '';
			?>
			<hr>
			<div class="row">
				<div class="col-md-8">
					<h3 class="nomargins maker-results"><a href="/maker/entry/<?php echo $entry_id; ?>"><?php echo $project_name;?></a></h3>
				</div>
			</div>
			<?php endforeach;?>
			<hr>

			<?php
			if ($total_count > 30) {
				echo '<div class="row padtop padbottom">
					<div class="col-md-8">
						' . pagination_display($current_url,$search_term,$currentpage,$page_size,$total_count) . '
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


	$results_count = $wpdb->get_row( $sqlcounting );
	$total_count=$results_count->total_count;

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
	$lead_detail_table_name = GFFormsModel::get_lead_details_table_name();
	$lead_table_name        = GFFormsModel::get_lead_table_name();

	$field_number_min = $sort_field_number - 0.0001;
	$field_number_max = $sort_field_number + 0.0001;

	$searchfield_number_min = $search_key - 0.0001;
	$searchfield_number_max = $search_key + 0.9999;
	$search_160 = "(field_number BETWEEN '159.9999' AND '160.9999' AND value like ( '%$search_value%' ))";
	$search_158 = "(field_number BETWEEN '157.9999' AND '158.9999' AND value like ( '%$search_value%' ))";
	$search_155 = "(field_number BETWEEN '154.9999' AND '155.9999' AND value like ( '%$search_value%' ))";
	$search_166 = "(field_number BETWEEN '165.9999' AND '166.9999' AND value like ( '%$search_value%' ))";
	$search_157 = "(field_number BETWEEN '156.9999' AND '157.9999' AND value like ( '%$search_value%' ))";
	$search_159 = "(field_number BETWEEN '158.9999' AND '159.9999' AND value like ( '%$search_value%' ))";
	$search_154 = "(field_number BETWEEN '153.9999' AND '154.9999' AND value like ( '%$search_value%' ))";
	$search_109 = "(field_number BETWEEN '108.9999' AND '109.9999' AND value like ( '%$search_value%' ))";
	$search_151 = "(field_number BETWEEN '150.9999' AND '151.9999' AND value like ( '%$search_value%' ))";
	$search_16 = "(field_number BETWEEN '15.9999' AND '16.9999' AND value like ( '%$search_value%' ))";
	$accepted_criteria = "(field_number BETWEEN '302.9999' AND '303.9999' AND value = 'Accepted' )";


	$sql = "

	SELECT sorted.sort,sorted.value, l.*, d.field_number, d.value
            FROM $lead_table_name l
            INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
			INNER JOIN (
            SELECT @rownum:=@rownum+1 as sort, l.lead_id as id, l.value
			FROM (Select @rownum:=0) r, wp_rg_lead_detail l
				INNER JOIN (
						SELECT
						lead_id as id
						from $lead_detail_table_name
						WHERE ( $search_160 OR $search_158 OR $search_155 OR $search_166 OR $search_157 OR $search_159 OR $search_154 OR $search_109 OR $search_151 OR $search_16)
						AND form_id in ($form_id)
					) filtered on l.lead_id=filtered.id
					INNER JOIN
				    (
				    SELECT
						lead_id as id
						from $lead_detail_table_name
						WHERE $accepted_criteria
						AND form_id in ($form_id)
						) accepted on l.lead_id=accepted.id
				WHERE field_number  between $field_number_min AND $field_number_max AND l.form_id in ($form_id)
		ORDER BY l.value ASC LIMIT $offset,$page_size ) sorted on sorted.id=l.id
        order by sorted.sort
	";

	return $sql;


}

function sort_by_field_count( $form_id, $searching ) {
	global $wpdb;
	$search_value       = isset( $searching['value'] ) ? $searching['value'] : '';

	$lead_detail_table_name = GFFormsModel::get_lead_details_table_name();
	$lead_table_name        = GFFormsModel::get_lead_table_name();

	$search_160 = "(field_number BETWEEN '159.9999' AND '160.9999' AND value like ( '%$search_value%' ))";
	$search_158 = "(field_number BETWEEN '157.9999' AND '158.9999' AND value like ( '%$search_value%' ))";
	$search_155 = "(field_number BETWEEN '154.9999' AND '155.9999' AND value like ( '%$search_value%' ))";
	$search_166 = "(field_number BETWEEN '165.9999' AND '166.9999' AND value like ( '%$search_value%' ))";
	$search_157 = "(field_number BETWEEN '156.9999' AND '157.9999' AND value like ( '%$search_value%' ))";
	$search_159 = "(field_number BETWEEN '158.9999' AND '159.9999' AND value like ( '%$search_value%' ))";
	$search_154 = "(field_number BETWEEN '153.9999' AND '154.9999' AND value like ( '%$search_value%' ))";
	$search_109 = "(field_number BETWEEN '108.9999' AND '109.9999' AND value like ( '%$search_value%' ))";
	$search_151 = "(field_number BETWEEN '150.9999' AND '151.9999' AND value like ( '%$search_value%' ))";
	$search_16 = "(field_number BETWEEN '15.9999' AND '16.9999' AND value like ( '%$search_value%' ))";
	$accepted_criteria = "(field_number BETWEEN '302.9999' AND '303.9999' AND value = 'Accepted' )";


	$sql = "SELECT
	count(distinct l.lead_id) as total_count
	from $lead_detail_table_name as l
	INNER JOIN
				    (
				    SELECT
						lead_id as id
						from $lead_detail_table_name
						WHERE $accepted_criteria
						AND form_id in ($form_id)
						) accepted on l.lead_id=accepted.id
	WHERE ( $search_160 OR $search_158 OR $search_155 OR $search_166 OR $search_157 OR $search_159 OR $search_154 OR $search_109 OR $search_151 OR $search_16)
	AND form_id in ($form_id)
	";

	return $sql;

}

function pagination_display ($current_url,$search_term,$current_page,$pagesize,$total_count) {
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
			<li <?php if ($current_page == 1) echo 'class = "disabled"'; ?>><a <?php if ($current_page == 1) echo 'class = "disabled"'; ?> href="<?php echo $current_url?>/<?php echo ($current_page == 1) ? $current_page.'#': $current_page-1; ?>/?s_term=<?php echo $search_term;?>">&laquo;</a></li>
			<?php endif; ?>
			<?php for($i = 1;$i <= $pages;$i++): ?>
			<li  <?php if ($current_page == $i) echo 'class = "active"'; ?> ><a href="<?php echo $current_url?>/<?php echo $i?>/?s_term=<?php echo $search_term.'&faire='.$faire;?>"><?php echo $i?></a></li>
			<?php endfor;?>
			<?php if ($current_page < $pages) : ?>
			<li <?php if ($current_page == $pages) echo 'class = "disabled"'; ?>><a <?php if ($current_page == $pages) echo 'class = "disabled"'; ?> href="<?php echo $current_url?>/<?php echo ($current_page == $pages) ? $current_page.'#': $current_page+1;?>/?s_term=<?php echo $search_term;?>">&raquo;</a></li>
			<?php endif;?>
		</ul>
	</nav>
</div>

<?php
}
?>