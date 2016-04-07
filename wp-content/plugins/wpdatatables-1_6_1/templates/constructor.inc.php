<div class="wpDataTables metabox-holder">
    <div id="wdtPreloadLayer" class="overlayed">
    </div>
    
    <div class="wrap">
	    <div id="poststuff">
		    <div id="post-body" class="metabox-holder">
				<div id="postbox-container-1" class="postbox-container">
					<img src="<?php echo dirname(plugin_dir_url(__FILE__)); ?>/assets/img/wpdatatables-logo.png" class="wpdatatables_logo" />
					<p><i><?php _e('Please refer to','wpdatatables');?> <a href="http://wpdatatables.com/documentation/creating-new-wpdatatables-with-table-constructor/"><?php _e('wpDataTables documentation on Table Consturctor','wpdatatables');?></a> <?php _e('if you have some questions or problems.','wpdatatables'); ?></i></p>
					<h2><?php _e('wpDataTable constructor','wpdatatables'); ?></h2>
					<form method="post" action="<?php echo WDT_ROOT_URL ?>" id="wpDataTablesSettings">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<div class="handlediv" title="<?php _e('Click to toggle','wpdatatables'); ?>"><br/></div>
						    <h3 class="hndle">
						    	<span><div class="dashicons dashicons-edit"></div> <?php _e('Table Creation Wizard','wpdatatables'); ?></span>
						    </h3>
						    <div class="inside">

								<div class="steps">
									
									<!-- Selection of data source type -->
									<div class="constructorStep step1" data-step="1">
										<h3><?php _e('Choose what kind of table would you like to construct','wpdatatables'); ?></h3>
										<fieldset style="margin: 10px;">
											<label for="manual_input"><input id="manual_input" type="radio" name="wpdatatables_type_input" value="manual" /> <span><?php _e('I would like to prepare structure and input all the data manually','wpdatatables');?></span></label><br/>
											<label for="file_input"><input id="file_input" type="radio" name="wpdatatables_type_input" value="file" /> <span><?php _e('I would like to read the initial table data from an input file','wpdatatables');?></span></label><br/>
											<label for="wp_posts_input"><input id="wp_posts_input" type="radio" name="wpdatatables_type_input" value="wp" /> <span><?php _e('I want to create a table based on my WordPress data (posts or pages, and post meta or taxonomy values)','wpdatatables');?></span></label><br/>
											<label for="mysql_construct_input"><input id="mysql_construct_input" type="radio" name="wpdatatables_type_input" value="mysql" /> <span><?php _e('I want to construct a table based on data from existing MySQL DB tables','wpdatatables');?></span></label><br/>
											
										</fieldset>
									</div>
									
									<!-- For manual tables  -->
									<div class="constructorStep step11" data-step="1-1" style="display: none">
										<h3><?php _e('Please provide some initial structure metadata before the table will be created','wpdatatables'); ?></h3>
                                                                                <p><?php _e('This constructor will help you to create a table from scratch. You will be able to edit the table content and metadata later manually at any time, but not the column number, so be careful.','wpdatatables'); ?></p>
										<fieldset style="margin: 10px;">
											<table>
											<tr>
												<td style="width: 250px">
                                                                                                        <label for="file_table_name"><span><strong><?php _e('Table name','wpdatatables');?></strong></span></label><br/>
                                                                                                        <span class="description"><small><?php _e('What is the header of the table that will be visible to the site visitors?','wpdatatables');?>.</small></span>
												</td>
												<td>
													<input id="table_name" type="text" value="<?php _e('New wpDataTable','wpdatatable');?>" />
												</td>
											</tr>
											<tr>
												<td>
													<label for="table_columns"><span><strong><?php _e('Columns','wpdatatables');?></strong></span></label><br/>
                                                                                                        <span class="description"><small><?php _e('How many columns will it have? (you can also modify it below with "+" and "X" buttons)','wpdatatables');?></small>.</span>
												</td>
												<td>
													<input id="table_columns" type="number" value="4" /></label>
												</td>
											</tr>
											<tr>
												<td>
													<label for="table_columns"><span><strong><?php _e('Column names and types','wpdatatables');?></strong></span></label><br/>
                                                                                                        <span class="description"><small><?php _e('Drag and drop to reorder columns','wpdatatables');?></small>.</span>

												</td>
												<td class="columnsContainer">
													
												</td>
											</tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                            </td>
                                                                                            <td>
                                                                                                    <button class="addColumnBlock button"><span class="dashicons dashicons-plus"></span></button>
                                                                                            </td>
                                                                                        </tr>
											
											</table>
										</fieldset>
										
									</div>
									
									<!-- For tables with file input  -->
									<div class="constructorStep step12" data-step="1-2" style="display: none">
										<h3><?php _e('Please upload the file that contains the initial table data (Excel or CSV)','wpdatatables'); ?></h3>
                                                                                <p><?php _e('This constructor type will import your table data to a MySQL table and generate a wpDataTable based on a SQL query to this table.','wpdatatables'); ?></p>
										<fieldset style="margin: 10px;">
											<table>
											<tr>
												<td>
                                                                                                    <label for="wpdatatables_wizzard_fileupload"><span><?php _e('Choose the file','wpdatatables');?></span></label><br/>
												</td>
												<td>
														<div class="uploader">
														  <input type="text" name="wpdatatables_wizzard_fileupload" id="wpdatatables_wizzard_fileupload" />
														  <button name="wpdatatables_wizzard_fileupload_button" id="wpdatatables_wizzard_fileupload_button" class="button"><?php _e('Upload','wpdatatables'); ?></button>
														</div>
														<label id="fileupload_file_empty_error" style="display: none; font-weight: bold; color: red"> </label>
												</td>
											</tr>
											</table>
										</fieldset>
									</div>
                                                                        
                                                                        <div class="constructorStep step22" data-step="2-2" style="display: none">
                                                                            
                                                                                <fieldset style="margin: 10px;">
                                                                                        <label><?php _e('Please check which columns would you like to import and make sure that the column types were imported correctly','wpdatatables');?></label>
                                                                                        <div class="uploader previewFileTable">

                                                                                        </div>
										</fieldset>                                                                            
                                                                        </div>
									
									<!-- For tables with WP data -->
									<div class="constructorStep step13" data-step="1-3" style="display: none">
										<h3><?php _e('Please choose the WP data which will be used to create a table','wpdatatables'); ?></h3>
                                                                                <p><?php _e('This constructor type will create a query to WordPress database and create a wpDataTable based on this query. This table content cannot be edited manually afterwards, but will always contain actual data from your WordPress database.','wpdatatables'); ?></p>
										
										<fieldset style="margin: 10px;">
											<table>
											<tr>
												<td style="width: 250px">
													<label for="wpdatatables_post_type"><span><?php _e('Choose the post types which you would like to have in the table','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">

                                                                                                            <select id="wpdatatables_post_type" multiple="true">
                                                                                                                    <option value="all"><?php _e('all','wpdatatables'); ?></option>
                                                                                                                    <?php foreach(get_post_types() as $post_type){ ?>
                                                                                                                    <option value="<?php echo $post_type ?>"><?php echo $post_type ?></option>
                                                                                                                    <?php } ?>
                                                                                                            </select>
                                                                                                            
                                                                                                        </div>
												</td>
											</tr>
											<tr>
												<td>
													<label for="wpdatatables_post_columns"><span><?php _e('Choose the posts properties that you would like to have as columns in the table','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">

                                                                                                            <select id="wpdatatables_post_columns" multiple="true">
                                                                                                                <option value=""><?php _e('Please select post types','wpdatatables');?></option>
                                                                                                            </select>
                                                                                                            
                                                                                                        </div>
												</td>
											</tr>
											<tr class="wdt_handle_post_types" style="display: none">
												<td>
													<label for="wpdatatables_posts_relations"><span><?php _e('Choose how to handle different post types','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">
                                                                                                            <label for="wpdatatables_posts_relations_join"><input type="radio" name="wpdatatables_posts_relations" id="wpdatatables_posts_relations_join" value="parent_child" /> <?php _e('Define relations (joining rules) between post types','wpdatatables');?></label><br/>
                                                                                                            <label for="wpdatatables_posts_relations_outer_join"><input type="radio" name="wpdatatables_posts_relations" id="wpdatatables_posts_relations_outer_join" value="union" /> <?php _e('Do not define relations between post types - do a full outer join','wpdatatables');?></label><br/>
                                                                                                        </div>
												</td>
											</tr>
											<tr class="wdt_define_relations" style="display: none">
												<td>
													<label for="wpdatatables_posts_relations"><span><?php _e('Define the relations between different post types','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader relationsContainer">
                                                                                                        </div>
												</td>
											</tr>
											<tr class="wdt_define_conditions">
												<td>
													<label for="wpdatatables_posts_conditions"><span><?php _e('Add conditions that you would like to have','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader conditionsContainer">
                                                                                                        </div>
                                                                                                        <button class="btn button-secondary" id="wdt_posts_add_where_condition">+</button>
												</td>
											</tr>
											<tr class="wdt_define_grouping">
												<td>
													<label for="wpdatatables_posts_grouping"><span><?php _e('Add grouping rules that you would like to have','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader groupingContainer">
                                                                                                        </div>
                                                                                                        <button class="btn button-secondary" id="wdt_posts_add_grouping_rule">+</button>
												</td>
											</tr>                                                                                        
											</table>
										</fieldset>										
										
									</div>
									
									<!-- For tables with MySQL data -->
									<div class="constructorStep step14" data-step="1-4" style="display: none">
										<h3><?php _e('Please choose the MySQL data which will be used to create a table','wpdatatables'); ?></h3>
											<table>
											<tr>
												<td style="width: 250px">
													<label for="wpdatatables_mysql_tables"><span><?php _e('Choose the tables that should provide the data to your table','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">

                                                                                                            <select id="wpdatatables_mysql_tables" multiple="true">
                                                                                                                    <?php foreach(wpDataTableConstructor::listMySQLTables() as $mysql_table){ ?>
                                                                                                                    <option value="<?php echo $mysql_table ?>"><?php echo $mysql_table ?></option>
                                                                                                                    <?php } ?>
                                                                                                            </select>
                                                                                                            
                                                                                                        </div>
												</td>
											</tr>
											<tr>
												<td>
													<label for="wpdatatables_mysql_tables_columns"><span><?php _e('Choose the columns that you would like to have in your table','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">

                                                                                                            <select id="wpdatatables_mysql_tables_columns" multiple="true">
                                                                                                                <option value=""><?php _e('Please select the tables first','wpdatatables');?></option>
                                                                                                            </select>
                                                                                                            
                                                                                                        </div>
												</td>
											</tr>
											<tr class="wdt_handle_multiple_tables" style="display: none">
												<td>
													<label for="wpdatatables_table_relations"><span><?php _e('Choose how to handle relations between multiple tables','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">
                                                                                                            <label for="wpdatatables_table_relations_join"><input type="radio" name="wpdatatables_table_relations" id="wpdatatables_table_relations_join" value="parent_child" /> <?php _e('Define relations (joining rules) between MySQL tables','wpdatatables');?></label><br/>
                                                                                                            <label for="wpdatatables_table_relations_outer_join"><input type="radio" name="wpdatatables_table_relations" id="wpdatatables_table_relations_outer_join" value="union" /> <?php _e('Do not define relations between MySQL tables - do a full outer join','wpdatatables');?></label><br/>
                                                                                                        </div>
												</td>
											</tr>
                                                                                        
											<tr class="wdt_define_mysql_relations" style="display: none">
												<td>
													<label for="wpdatatables_mysql_relations"><span><?php _e('Define the relations between tables','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader mysqlRelationsContainer">
                                                                                                        </div>
												</td>
											</tr>
											<tr class="wdt_define_mysql_conditions">
												<td>
													<label for="wpdatatables_mysql_conditions"><span><?php _e('Add conditions that you would like to have','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader mysqlConditionsContainer">
                                                                                                        </div>
                                                                                                        <button class="btn button-secondary" id="wdt_mysql_add_where_condition">+</button>
												</td>
											</tr>
											<tr class="wdt_define_mysql_grouping">
												<td>
													<label for="wpdatatables_posts_grouping"><span><?php _e('Add grouping rules that you would like to have','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader mysqlGroupingContainer">
                                                                                                        </div>
                                                                                                        <button class="btn button-secondary" id="wdt_mysql_add_grouping_rule">+</button>
												</td>
											</tr>                                                                                        
                                                                                        
                                                                                        </table>
                                                                                
                                                                                
									</div>									
                                                                        
									<!-- Query preview for tables with WP data -->
									<div class="constructorStep step23" data-step="2-3" style="display: none">
                                                                                <fieldset style="margin: 10px;">
											<table>
											<tr>
												<td style="width: 250px">
													<label for="wpdatatables_post_query_preview"><span><?php _e('Preview the query that has been generated for you','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader">
                                                                                                            <pre id="previewWPQuery" class="previewWPQuery" style="height: 300px; width: 100%; min-width: 500px"></pre>
                                                                                                        </div>
                                                                                                        <button class="refreshWpQuery button"><span class="dashicons dashicons-update"></span> <?php _e('Refresh query and preview','wpdatatables'); ?></button>
												</td>
											</tr>
											<tr>
												<td style="width: 250px">
													<label for="wpdatatables_post_result_preview"><span><?php _e('Preview the 5 first result rows','wpdatatables');?></span></label>
												</td>
												<td>
                                                                                                        <div class="uploader previewWPTable">
                                                                                                            
                                                                                                        </div>
												</td>
											</tr>
                                                                                        
											</table>
										</fieldset>
                                                                        </div>
									
								</div>
							
								<button class="button" style="display:none;" id="prevStep"><?php _e('&lt;&lt; Previous','wpdatatables'); ?></button>
								<button class="button" id="nextStep"><?php _e('Next &gt;&gt;','wpdatatables'); ?></button>
								<button class="button" style="display:none;" id="createAndEdit"><?php _e('Create the table and open in editor','wpdatatables'); ?></button>
								<button class="button" style="display:none;" id="saveAndOpen"><?php _e('Create wpDataTable','wpdatatables'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    var wdt_post_meta_by_post_types = <?php echo wpdatatables_get_post_meta_keys_for_post_types() ?>;
    var wdt_taxonomies_by_post_types = <?php echo wpdatatables_get_taxonomies_for_post_types() ?>;
</script>

<script id="columnBlockTmpl" type="text/x-jsrender">
<div class="columnBlock">
        <table>
            <tr>
                <td colspan="2" class="columnBlockHeader">
                    <button class="button removeColumnBlock" style=""><span class="dashicons dashicons-no-alt"></span></button>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Column header','wpdatatables'); ?></strong></span>:</label>
                </td>
                <td>
                    <div class="columnName">
                            <input type="text" value="{{>name}}" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Type','wpdatatables'); ?></strong></span>:</label>
                </td>    
                <td>
                    <div class="columnType">
                            <select>
                                    {{for ~columnTypes tmpl="#columnTypeTemplate"/}}
                            </select>
                    </div>
                </td>
            </tr>
            <tr class="columnPossibleValuesBlock" style="display: none">
                <td>
                    <label><span><strong><?php _e('Possible values','wpdatatables'); ?></strong></span>:</label><br/>
                    <span class="description"><small><?php _e('Separate with comma','wpdatatables');?></small>.</span>

                </td>
                <td>
                    <div class="columnPossibleValues">
                        <input type="text" value="" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Default value','wpdatatables'); ?></strong></span>:</label></br>
                    <span class="description"><small><?php _e('(Optional)','wpdatatables'); ?></small></span>
                </td>
                <td>
                    <div class="columnDefaultValue">
                        <input type="text" value="" />
                    </div>
                </td>
            </tr>
        </table>
</div>
</script>

<script id="columnTypeTemplate" type="text/x-jsrender">
	<option value="{{>name}}">{{>value}}</option>
</script>

<script id="relationBlockTemplate" type="text/x-jsrender">
    <div class="post_blocks">
     <span class="relationInitiatorType">{{>post_type}}.</span>
     <select class="relationInitiatorColumn" data-post_type="{{>post_type}}">
        <option value=""></option>
        {{for post_type_columns}}
        <option value="{{:}}">{{:}}</option>
        {{/for}}
     </select> 
      =
     <select class="relationConnectedColumn" data-post_type="{{>post_type}}">
        <option value=""></option>
        {{for other_post_type_columns}}
        <option value="{{:}}">{{:}}</option>
        {{/for}}
     </select>
     <input type="checkbox" title="<?php _e('Check to have an inner join, uncheck to have left join','wpdatatables'); ?>" class="innerjoin" />
    </div>
</script>

<script id="postColumnTemplate" type="text/x-jsrender">
    {{for availablePostColumns}}
	<option value="{{:}}">{{:}}</option>
    {{/for}}
</script>

<script id="whereConditionTemplate" type="text/x-jsrender">
    <div class="post_where_blocks">
    <select class="whereConditionColumn">
        <option value=""></option>
        {{for post_type_columns}}
        <option value="{{:}}">{{:}}</option>
        {{/for}}
     </select>
     <select class="whereOperator">
           <option value="eq">=</option>
           <option value="gt">&gt;</option>
           <option value="gtoreq">&gt;=</option>
           <option value="lt">&lt;</option>
           <option value="ltoreq">&lt;=</option>
           <option value="neq">&lt;&gt;</option>
           <option value="like">LIKE</option>
           <option value="plikep">%LIKE%</option>
           <option value="in">IN</option>
     </select>
     
    <input type="text" />
                
    <button class="button-secondary deleteConditionPosts" style="line-height: 26px; font-size: 26px"><span class="dashicons dashicons-no"></span></button>
    </div>
</script>

<script id="groupingRuleTemplate" type="text/x-jsrender">
    <div class="post_grouping_rule_blocks">
    <?php _e('Group by ', 'wpdatatables'); ?>
        
    <select class="groupingRuleColumn">
        <option value=""></option>
        {{for post_type_columns}}
        <option value="{{:}}">{{:}}</option>
        {{/for}}
     </select>
     
    <button class="button-secondary deleteGroupingRulePosts" style="line-height: 26px; font-size: 26px"><span class="dashicons dashicons-no"></span></button>
    </div>
</script>

<script id="mysqlTableColumnTemplate" type="text/x-jsrender">
    {{for availableTableColumns}}
	<option value="{{:}}">{{:}}</option>
    {{/for}}
</script>

<script id="mysqlRelationBlockTemplate" type="text/x-jsrender">
    <div class="mysql_table_blocks">
     <span class="relationInitiatorTable">{{>table}}.</span>
     <select class="relationInitiatorColumn" data-table="{{>table}}">
        <option value=""></option>
        {{for columns}}
        <option value="{{:}}">{{:}}</option>
        {{/for}}
     </select> 
      =
     <select class="relationConnectedColumn" data-table="{{>table}}">
        <option value=""></option>
        {{for other_table_columns}}
        <option value="{{:}}">{{:}}</option>
        {{/for}}
     </select>
     <input type="checkbox" title="<?php _e('Check to have an inner join, uncheck to have left join','wpdatatables'); ?>" class="innerjoin" />
    </div>
</script>