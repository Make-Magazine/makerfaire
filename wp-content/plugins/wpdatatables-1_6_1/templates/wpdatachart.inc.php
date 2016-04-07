<script type="text/javascript">if(typeof(wpDataCharts)=='undefined'){wpDataCharts = {};}; wpDataCharts[<?php echo $chart_id; ?>] = {render_data: <?php echo $json_chart_render_data; ?>, engine: "<?php echo $this->_engine;?>", type: "<?php echo $this->_type; ?>", title: "<?php echo $this->_title; ?>", container: "wpDataChart_<?php echo $chart_id?>", follow_filtering: <?php echo (int) $this->_follow_filtering; ?>, wpdatatable_id: <?php echo $this->_wpdatatable_id ?> }</script>

<div id="wpDataChart_<?php echo $chart_id?>">

</div>