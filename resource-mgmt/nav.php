      <div class="well">
        <div>
          <ul class="nav nav-list">
            <li><label class="tree-toggle nav-header">Reports</label>
              <ul class="nav nav-list tree">
                <li><a href="#canned">Canned Rpts</a></li>                
                <li><a href="#reports/change">Entry Change Rpt</a></li>                
                <li><a href="#reports/schedule">Schedule Rpt</a></li>                                
                <li><a href="#reports/lookup">LookUp Report</a></li>
                <li><a href="#reports/notes">Notes Report</a></li>
                <li><a href="#reports/tickets">Tickets Report</a></li>
                
                <!--<li><a href="#reports/drill">Resource Drill Down</a></li>-->                
                <!--<li><a href="#ent2resources/all">Entry 2 Resource</a></li>-->
                <!--<li><a href="#ent2resources/sponsor">Sponsor Entry2Resource</a></li>-->
                <!--<li><a href="#reports/tasksComp">Tasks Completed</a></li>-->
                <li><a href="/wp-admin/admin.php?page=mf_fsp_gsp">Download FSP/GSP</a></li>  
                <li><a href="/wp-admin/admin.php?page=mf_fairesign">Download Signs and Table tags</a></li>                
                <li><a href="#reports/build">Build your own Rpt</a></li>
              </ul>
            </li>
            <?php if (!current_user_can( 'reports_only' ) ) { ?>
              <li><label class="tree-toggle nav-header">Entry Specific Data</label>
              <ul class="nav nav-list tree">
                <li><a href="#entry/resources">Assigned Resources</a></li>
                <li><a href="#entry/attributes">Assigned Attributes</a></li>
                <li><a href="#entry/attention">Assigned Attention</a></li>                
                <li><a href="#entry/ribbons">Faire Ribbons</a></li>                
              </ul>
            </li>
            <li><label class="tree-toggle nav-header">Manage RMT Data</label>
              <ul class="nav nav-list tree">
              <li><a href="#resources/items">Resource Categories</a></li>
                <li><a href="#resources/list">Resources</a></li>                
                <li><a href="#entry/atttibuteCategories">Attributes</a></li>
                <li><a href="#entry/workflow">Workflow/Attention</a></li>
                <li><a href="#vendors/list">Vendor List</a></li>
                <li><a href="#vendors/resources">Vendor Resources</a></li>
              </ul>
            </li>
            <li><label class="tree-toggle nav-header">Faire Data</label>
              <ul class="nav nav-list tree">
                <li><a href="#faire/global-faire">Global Faire Data</a></li>
                <li><a href="#faire/data">Faire Data</a></li>
                <li><a href="#faire/areas">Faire Areas</a></li>
                <li><a href="#faire/subareas">Faire Sub-Areas</a></li>
                <li><a href="#faire/schedule">Assigned Location/Schedule</a></li>
              </ul>
            </li>
            <?php } ?>

          </ul>
        </div>
      </div>
