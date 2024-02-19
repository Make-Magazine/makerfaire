<?php
$error = '';
$rows = array();
if (isset($_FILES['mpfy_upload'])) {
	if ($_FILES['mpfy_upload']['error'] == 0) {
		ini_set('auto_detect_line_endings', true);
		$map_id = intval($_POST['mpfy_map']);
		$handle = fopen($_FILES['mpfy_upload']['tmp_name'], 'r');
		$rows   = array();
		@fgetcsv($handle); // skip headers
		while ($r = fgetcsv($handle)) {
			$rows[] = $r;
		}

		if (!$rows) {
			$error = 'The uploaded file does not contain any rows.';
		}
	} else {
		if ($_FILES['mpfy_upload']['error'] == 4) {
			$error = 'You have not selected a file for upload.';
		} else {
			$error = 'Unexpected upload error #' . $_FILES['mpfy_upload']['error'] . '. Please contact support.';
		}
	}
}

$json_rows = json_encode($rows);
switch ( json_last_error() ) {
	case JSON_ERROR_NONE:
		// do nothing
		break;
	case JSON_ERROR_UTF8:
		$error = 'Malformed UTF-8 characters found in import file.';
		break;
	default:
		$error = 'An unexpected error occurred while reading your file. Please ensure your file does not have invalid characters and is in prover CSV format.';
		break;
}
?>
<div class="wrap mpfy-admin-shell">
	<div class="icon32" id="icon-options-general"><br></div>
	
	<h2>Locations Batch Upload</h2><br>

	<?php if ($error) : ?>
		<p style="color: red;"><?php echo $error; ?></p>
	<?php endif; ?>
	<?php if (!$error && $rows) : ?>
		<p>Importing <?php echo count($rows); ?> entr<?php echo (count($rows) != 1) ? 'ies' : 'y'; ?> ... <span class="mpfy-admin-loading">&nbsp;</span></p>
		<div class="mpfy-progressbar" style="display: none;">
			<span class="mpfy-progressbar-progress"></span>
			<span class="mpfy-progressbar-label">0%</span>
		</div>
		<ul class="import-log"></ul>
		<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				function pad2(str) {
					str = str.toString();
					str = str.length >= 2 ? str : '0' + str;
					return str;
				}

				function progress(percent) {
					var progressbar = $('.mpfy-progressbar:first');
					var bar = progressbar.find('.mpfy-progressbar-progress:first');
					var label = progressbar.find('.mpfy-progressbar-label:first');

					if (percent == 1) {
						progressbar.hide();
					} else {
						progressbar.show();
						bar.css('width', (percent * 100).toString() + '%');
						label.text((percent * 100).toFixed(0).toString() + '%');
					}
				}

				var rows = <?php echo $json_rows; ?>;
				var totalRows = rows.length;
				function import_queue() {
					var d = new Date();
					if (rows.length > 0) {
						var r = rows.pop();
						var url = '<?php echo add_query_arg('action', 'mpfy_batch_upload', admin_url('admin-ajax.php')); ?>';
						var postdata = {'row': r, 'map_id': <?php echo intval($map_id); ?>};

						progress((totalRows - rows.length - 1) / totalRows);
						$.post(url, postdata, function(response) {
							$('.import-log').prepend('<li><strong>' + pad2(d.getHours()) + ':' + pad2(d.getMinutes()) + ':' + pad2(d.getSeconds()) + ':</strong> ' + response + '</li>');
							import_queue();
						}, 'html').fail(function(){
							var li = $('<li><strong>' + pad2(d.getHours()) + ':' + pad2(d.getMinutes()) + ':' + pad2(d.getSeconds()) + ':</strong>&nbsp;</li>');
							var span = $('<span style="color: red;"></span>').text('An unknown error ocurred while attempting to import the following location: ' + postdata.row.join('; '));
							li.append(span)
							$('.import-log').prepend(li);
							import_queue();
						});
					} else {
						$('.import-log').prepend('<li><strong>' + pad2(d.getHours()) + ':' + pad2(d.getMinutes()) + ':' + pad2(d.getSeconds()) + ':</strong> <span style="color: green;">Import complete.<span></li>');
						$('.mpfy-admin-loading').hide();
						progress(1);
					}
				}
				import_queue();
			});
		})(jQuery);
		</script>
	<?php else : ?>
		<form method="post" action="" enctype="multipart/form-data">
			<?php $maps = Mpfy_Map::get_all_maps(); ?>
			<select name="mpfy_map">
				<?php foreach ($maps as $id => $name) : ?>
					<option value="<?php echo $id; ?>"><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="file" name="mpfy_upload" value="" /><br><br>
			<input type="submit" name="" class="button button-primary" value="Import Locations" />
		</form>
		<div class="cl">&nbsp;</div>
		<a href="<?php echo plugins_url('batch-upload-sample.csv', __FILE__); ?>">Download sample csv file</a>
		<br/><br/>
		Please note that if the Title field matches the title of an existing location, then the following will apply:<br/>
		<ul>
			<li>The existing entry will be updated (instead of creating a new entry).</li>
			<li>The description will not be updated by default.</li>
			<li>If the item exists that matches the Title but it is in your Trash, then a new entry will not be created. The item in the trash will be updated. (We are working on a fix!)</li>
		</ul>

	<?php endif; ?>
</div>
