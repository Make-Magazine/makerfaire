<div class="sumome-plugin-container">
  <div class="sumome-plugin-main">
    <div class="statistics-container">
      <div class="statistics"></div>
    </div>
    <div class="loading"><img src="<?php echo plugins_url('images/sumome-loading.gif', dirname(__FILE__)) ?>"></div>
  </div>
</div>
<script>
jQuery(document).ready(function() {
  getSumomeStats();
});

function getSumomeStats() {
  var siteID='<?php print get_option('sumome_site_id'); ?>';
  statisticsDate=jQuery('.sumome-dashboard-date-select').val();
  if (statisticsDate==null) {
    statisticsDate='<?php print date('Y-m-d')?>'; //default=last week
  }
  jQuery.ajax({
    url: 'https://sumome.com/apps/dashboard/stats',
    type: 'POST',
    dataType: 'json',
    beforeSend: function(req) {
      req.setRequestHeader('X-Sumo-Auth', '<?php print $_COOKIE['__smToken']?>');
    },
    xhrFields: {
      withCredentials: false
    },
    crossDomain: true,
    data: {'site_id':siteID,'date': statisticsDate},
    success: function(data) {
      jQuery('.loading').hide();

      var returnText;
      if (data.htmlBody!=null) {
        returnText=data.htmlBody;
      } else {
        returnText='<h3 class="headline">'+data.headline+'</h3>';
      }

      jQuery('.statistics').html(returnText);
      statisticsDateDropdown();
      jQuery(".sumome-dashboard-date-select option[value='"+statisticsDate +"']").attr('selected', 'selected');
      jQuery('.statistics-container').show();
    },
  });
}


function padDateString(n) {
    return (n < 10) ? ("0" + n) : n;
}

function getDropDownDateFormat(givenDate,plusDays) {
  if (plusDays==null) plusDays=0;
  year=givenDate.getFullYear()
  month=padDateString(givenDate.getMonth() + 1);
  day=padDateString(givenDate.getDate()+plusDays);
  return year+"-"+month+"-"+day;
}

function statisticsDateDropdown() {
  var thisSundayFull=new Date(new Date().setDate(new Date().getDate() - new Date().getDay()));
  var todayFull=new Date();
  var thisWeek=getDropDownDateFormat(todayFull,6);
  var lastWeek=getDropDownDateFormat(thisSundayFull);

  var dropdownContent= '<select class="sumome-dashboard-date-select">\
                          <option value="'+thisWeek+'">This Week</option>\
                          <option value="'+lastWeek+'" selected="">Last Week</option>\
                        </select><br>';

  jQuery('.statistics .headline').prepend(dropdownContent);
}

jQuery(document).on('change','.sumome-dashboard-date-select',function() {
  getSumomeStats();
});
</script>
