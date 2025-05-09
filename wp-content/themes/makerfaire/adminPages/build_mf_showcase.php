<?php
/* Admin page where showcases and their entries can be assigned
 */
global $wpdb;
$selfaire  = '';
$type   = '';
$formid = (isset($_GET['formid']) ? $_GET['formid'] : '');

wp_enqueue_script( "jqueryui-menu", "/wp-includes/js/jquery/ui/menu.min.js", array("jquery-ui-core"), "");
wp_enqueue_script( "jqueryui-autocomplete", "/wp-includes/js/jquery/ui/autocomplete.min.js", array("jquery-ui-core"), "");
?>

<div class="showcase-admin-header">
    <h2>Showcase Management</h2>
<?php

$rawForms = GFAPI::get_forms();
$forms = array();
foreach($rawForms as $form) {
    $forms[$form['id']] = $form['title'];
}
?>
<label>Change Form
    <select class="form-select" name="form-select">
    <?php foreach($forms as $key => $value) { ?>
        <option value="<?php echo $key; ?>" <?php if($key == $_GET['formid']){ echo 'selected="selected"'; } ?>><?php echo $value; ?></option>
    <?php } ?>
    </select>
</label>
<?php
//if form isn't set, abort
if ($formid == '') {
    echo 'Form ID is missing';
} else {
?>
</div>

    <div id="faire-showcase">
        <input type="hidden" id="formID" value="<?php echo $formid; ?>" />
        <!-- Showcase Information -->
        <?php
        $showcase_query = "select wp_mf_lead_rel.id as relation_id, parentID, 
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
        $grouped_showcases = array();
        foreach ($showcases as $showcase) {
            if (!isset($grouped_showcases[$showcase->parentID])) {
                $grouped_showcases[$showcase->parentID]['title'] = $showcase->parentTitle;
                $grouped_showcases[$showcase->parentID]['status'] = $showcase->parentStatus;
            }
            $grouped_showcases[$showcase->parentID]['children'][$showcase->childID] = array('title' => $showcase->childTitle, 'status' => $showcase->childStatus, 'relation_id' => $showcase->relation_id);
        }
        $used_showcases = array_keys($grouped_showcases); // this will be used to exclude used entries from dropdown

        foreach ($grouped_showcases as $parentID => $showcase) {
            $parent_title  = $showcase['title'];
            $parent_status = $showcase['status'];
            
        ?>
            <div class="row" id="showcase<?php echo $parentID; ?>">
                <div class="col-md-5">
                    <h3>Showcase</h3>
                    <?php
                    echo $parentID . ' - ' . $showcase['title'] . '(' . $parent_status . ')<br/>';
                    ?>
                </div>
                <div class="col-md-7">
                    <h3>Entries Assigned to Showcase</h3>
                    <?php
                    //loop through showcase children
                    foreach ($showcase['children'] as $childID => $child) {
                        $child_title  = $child['title'];
                        $child_status = $child['status'];
                        $relation_id  = $child['relation_id'];
                        $used_showcases[] = $childID;
                        echo '<p id="child'.$childID.'">' . $childID . ' - ' . $child_title . '(' . $child_status . ')<span class="fa fa-times" onclick="removeShowcase(' . $parentID . ", " . $childID . ", " . $relation_id . ')"></span></p>';
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
                            <p id="assign-entries-value"></p>
                        </div>
                        <div class="col-md-4">
                            <button onclick="addShowcase(<?php echo $parentID; ?>)">Add to Showcase</button>
                            <span class="updMsg add_to_showcaseMsg"></span>
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
        
        $sorting         = array();
        $paging          = array('offset' => 0, 'page_size' => 999);
        $total_count     = 0;
        //adds roughly 1 second to the call
        $entries         = GFAPI::get_entries($formid, $search_criteria, $sorting, $paging, $total_count);

        //build input array for JS
        $entry_array =  array();
        // dedupe used_showcases 
        array_unique($used_showcases);
        foreach ($entries as $entry) {
            // don't add entries that are already assigned to a showcase to input array
            if(!in_array($entry['id'], $used_showcases)) {
                $entry_array[] = array(
                    'label' => addslashes($entry['151']) . '(' . $entry['303'] . ')',
                    'value' => $entry['id']
                );
            }
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
                <div class="form-group new-assign">
                    <label for="faire">Assign to Showcase</label>
                    <div class="ui-widget">
                        <input class="assign-entries">
                    </div>
                    <p id="assign-entries-value"></p>

                </div>

            </div>
            <div class="col-md-2 new-button">
                <button onclick="addShowcase('new')">Add New Showcase</button>
                <span class="updMsg add_to_showcaseMsg"></span>
            </div>
        </div>
    </div>
<?php
}
?>
<script>
    var items = <?php echo json_encode($entry_array); ?>;
    var storedItems = []; // array to store items that are being selected
    //console.log(items);
    jQuery(function() {

        jQuery('.form-select').on('change', function() {
            window.location.href = '/wp-admin/admin.php?page=mf_showcase&formid=' + this.options[ this.selectedIndex ].value;
        });
        //Add new parent section
        jQuery(".add-showcase").autocomplete({
            source: function(request, response) {
                // delegate back to autocomplete, but extract the last term
                response(jQuery.ui.autocomplete.filter(
                    items, extractLast(request.term)));
            },
            select: function(event, ui) {
                this.value = ui.item.value;                
                jQuery('#showcase-name').html('<p>' + ui.item.value + ' - ' + ui.item.label + '</p>');
                // remove item from items array after selection and put in storedItems
                items = items.filter(function( obj ) {
                    if(obj.value == ui.item.value) {
                        storedItems.push(obj);
                    }
                    return obj.value !== ui.item.value;
                });
                return false;
            }
        });
        //assign a child to a parent
        jQuery(".assign-entries")
            // don't navigate away from the field on tab when selecting an item
            .on("keydown", function(event) {
                if (event.keyCode === jQuery.ui.keyCode.TAB &&
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
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");

                    jQuery(this).closest(".ui-widget").next("#assign-entries-value").append('<p class="adding' + ui.item.value + '">' + ui.item.value + ' - ' + ui.item.label + '<span class="fa fa-times" onclick="returnShowcase(' + ui.item.value +', this)"></p>');
                    
                    //remove ui.item.value from the items array and put it in storedItems
                    items = items.filter(function( obj ) {
                        if(obj.value == ui.item.value) {
                            storedItems.push(obj);
                        }
                        return obj.value !== ui.item.value;
                    });

                    return false;
                }
            });
    });

    // remove showcase that was already selected, remove from storedItems array, put back in items array
    function returnShowcase(entryID, el) {
        storedItems.forEach(function(item) {
            if(item.value == entryID) {
                var itemIndex = storedItems.indexOf(item);
                storedItems.splice(itemIndex, 1);
                items.push(item);
                var input = jQuery(el).parent().parent().prev().children(".assign-entries")[0];
                input.value = input.value.replace(entryID + ', ','');
                jQuery(".adding" + entryID).remove();
            }
        });
    }

    function split(val) {
        return val.split(/,\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }

    function check(array, key, value) {
        return array.some(object => object[key] === value);
    }

</script>