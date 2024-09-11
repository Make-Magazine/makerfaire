<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$selfaire  = '';
$type   = '';
$formid = (isset($_GET['formid']) ? $_GET['formid'] : '');

//Alicia - how can we get this to work without hardcoding the jquery libraries
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>

<h2 style="text-align:center">Showcase</h2>
<?php
//if form isn't set, abort
if ($formid == '') {
    echo 'Form ID is missing';
    //Alicia - maybe do form drop down with links back to the page?
} else {
?>
    <div id="faire-showcase">
        <input type="hidden" id="formID" value="<?php echo $formid; ?>" />
        <!-- Showcase Information -->
        <?php
        $showcase_query = "select id.wp_mf_lead_rel as relation_id, parentID, 
(select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = parentID and meta_key=151) as parentTitle,
(select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = parentID and meta_key=303) as parentStatus,
childID,
(select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = childID and meta_key=151) as childTitle,
(select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = childID and meta_key=303) as childStatus
from wp_mf_lead_rel 
left outer join wp_gf_entry parent_entry on parent_entry.ID = wp_mf_lead_rel.parentID
left outer join wp_gf_entry child_entry  on child_entry.ID = wp_mf_lead_rel.childID
where form in($formid)  
and parent_entry.status='active'
and child_entry.status='active'
ORDER BY `wp_mf_lead_rel`.`parentID` ASC;";

        $showcases = $wpdb->get_results($showcase_query);
        $grouped_showcases  = array();
        foreach ($showcases as $showcase) {
            //alicia - build array of used array containing every parent and child
            if (!isset($grouped_showcases[$showcase->parentID])) {
                $grouped_showcases[$showcase->parentID]['title'] = $showcase->parentTitle;
                $grouped_showcases[$showcase->parentID]['status'] = $showcase->parentStatus;
            }
            $grouped_showcases[$showcase->parentID]['children'][$showcase->childID] = array('title' => $showcase->childTitle, 'status' => $showcase->childStatus);
        }

        foreach ($grouped_showcases as $parentID => $showcase) {
            $parent_title = $showcase['title'];
            $parent_status = $showcase['status'];
        ?>
            <div class="row" id="showcase<?php echo $parentID; ?>">
                <div class="col-md-5">
                    <h3>Showcase</h3>
                    <?php
                    echo $parentID . ' - ' . $showcase['title'] . '(' . $parent_status . ')<br/>';
                    ?>
                </div>
                <div class="col-md-5">
                    <h3>Entries Assigned to Showcase</h3>
                    <?php
                    //loop through showcase children
                    foreach ($showcase['children'] as $childID => $child) {
                        $child_title  = $child['title'];
                        $child_status = $child['status'];
                        echo $childID . ' - ' . $child_title . '(' . $child_status . ')<br/>';
                        //alicia - need to add delete functionality - pass relation_id to delete
                    }

                    //add to showcase
                    ?>
                    <hr />
                    <label for="faire">Add to Showcase</label>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="ui-widget">
                                <input style="width:100%" class="assign-entries" name="assign-entries">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button onclick="addShowcase(<?php echo $parentID; ?>)">Add to Showcase</button>
                            <span class="updMsg add_to_showcaseMsg"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p id="assign-entries-value"></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        if (empty($grouped_showcases)) {
            echo 'No Showcases set yet for this form.';
        }
        //get all active entries in this form
        $search_criteria = array('status' => 'active');
        
        //alicia - the search criteria should exclude already entered parent/child for this form
        $sorting         = array();
        $paging          = array('offset' => 0, 'page_size' => 999);
        $total_count     = 0;
        //adds roughly 1 second to the call
        $entries         = GFAPI::get_entries($formid, $search_criteria, $sorting, $paging, $total_count);

        //build input array for JS
        $entry_array =  array();
        foreach ($entries as $entry) {
            //alicia - if $entry['id'] in_array (used id's) don't add here
            $entry_array[] = array(
                'label' => addslashes($entry['151']) . '(' . $entry['303'] . ')',
                'value' => $entry['id']
            );
        }
        ?>

        <div style="clear:both"></div>

        <h2>Add New Showcase</h2>
        <div class="row" id="showcasenew">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="faire">Select Showcase</label>
                    <div class="ui-widget">
                        <input type="text"
                            class="add-showcase"
                            placeholder="Type Name or Entry ID" />
                    </div>
                    <p id="showcase-name"></p>
                </div>

            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="faire">Assign to Showcase</label>
                    <div class="ui-widget">
                        <input class="assign-entries">
                    </div>
                    <p id="assign-entries-value"></p>

                </div>

            </div>
            <div class="col-md-2">
                <button onclick="addShowcase('new')">Add New Showcase</button>
                <span class="updMsg add_to_showcaseMsg"></span>
            </div>
        </div>
    </div>
<?php
}
?>
<script>
    var items = <?php echo json_encode($entry_array); ?>
    
    //Add new parent section
    jQuery(function() {
        jQuery(".add-showcase").autocomplete({
            source: items,
            select: function(event, ui) {
                this.value = ui.item.value;                
                jQuery('#showcase-name').html('<p>' + ui.item.value + ' - ' + ui.item.label + '</p>');
                return false;
            }
        });
    });

    //assign a child to a parent
    jQuery(function() {
        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        jQuery(".assign-entries")
            // don't navigate away from the field on tab when selecting an item
            .on("keydown", function(event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    jQuery(this).autocomplete("instance").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 0,
                source: function(request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response(jQuery.ui.autocomplete.filter(
                        items, extractLast(request.term)));
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {                    
                    var terms = split(this.value);
                    // remove the current input (as it is the id only)
                    terms.pop();

                    // add the selected item text
                    terms.push(ui.item.value);
                    //remove ui.item.value from the items array

                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");

                    //Alicia - this needs to be specific to either the entry or the new section
                    jQuery('#assign-entries-value').append('<p>' + ui.item.value + ' - ' + ui.item.label + '</p>');

                    return false;
                }
            });
    });

    function check(array, key, value) {
        return array.some(object => object[key] === value);
    }
</script>