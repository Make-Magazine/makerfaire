<?php

/*
 * Please paste PHP code below this that is generated using Custom Fields-> Tools -> Export Field Groups
 * Toggle all field groups and click 'Generate Export Code'
 */
if (function_exists('acf_add_local_field_group')) {
   
   acf_add_local_field_group(
      array(
         'key' => 'group_573b526e78706',
         'title' => 'Panels: Use these to manage what content displays on this page.',
         'fields' => array(
            array(
               'layouts' => array(
                  // Panel: Buy Tickets floating banner
                  array(
                     'key' => '57196b4abc501',
                     'name' => 'buy_tickets_float',
                     'label' => 'Get Tickets Floating Banner',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_57196b4abc502',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'This adds a floating banner to buy tickets.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'key' => 'field_57196b4abc503',
                           'label' => 'Buy Ticket URL',
                           'name' => 'buy_ticket_url',
                           'type' => 'url',
                           'instructions' => 'Required. Enter the URL to the ticket purchasing page for this faire.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 20,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_57196b4abc504',
                           'label' => 'Buy Ticket Text',
                           'name' => 'buy_ticket_text',
                           'type' => 'text',
                           'instructions' => 'Please enter the text displayed in the \'Buy Ticket\' Flag.<br/>20 character limit.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        )
                     ),
                     'min' => '',
                     'max' => 1
                  ),
                  // Panel: flag separator banner
                  array(
                     'key' => '572d8358ky7e1',
                     'name' => 'flag_banner_panel',
                     'label' => 'Flag Banner Separator Panel',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'show',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_572d8358ky7e2',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'This adds a flag banner that can be used to separate panels.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 100,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_572d8358fe8e3',
                           'label' => 'Newsletter Text',
                           'name' => 'newsletter_panel_text',
                           'type' => 'text',
                           'instructions' => 'Please enter the text displayed on the left side of the panel.<br/>100 character limit.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  // Panel: Newsletter signup
                  array(
                     'key' => '572d8358fe8e1',
                     'name' => 'newsletter_panel',
                     'label' => 'Newsletter Sign Up',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'show',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_572d8358fe8e2',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'This adds an email sign up form for newsletter subscriptions.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 100,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_572d8358fe8e3',
                           'label' => 'Newsletter Text',
                           'name' => 'newsletter_panel_text',
                           'type' => 'text',
                           'instructions' => 'Please enter the text displayed on the left side of the panel.<br/>100 character limit.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  
                  // one column wysisyg panel
                  array(
                     'key' => '572bac2b2d757',
                     'name' => '1_column_wysiwyg',
                     'label' => '1 Column WYSIWYG',
                     'display' => 'block',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_572bac2b2d757a',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => '',
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_572bac2b2d757b',
                           'label' => 'Title',
                           'name' => 'title',
                           'type' => 'text',
                           'instructions' => '',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'tabs' => 'all',
                           'toolbar' => 'full',
                           'media_upload' => 1,
                           'default_value' => '',
                           'delay' => 0,
                           'key' => 'field_572bac2b2d757c',
                           'label' => 'Content',
                           'name' => 'column_1',
                           'type' => 'wysiwyg',
                           'instructions' => 'Use the editor to style this content block however you like.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => 100,
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => '',
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_572bac2b2d757d',
                           'label' => 'CTA Button Text',
                           'name' => 'cta_button',
                           'type' => 'text',
                           'instructions' => 'Optional Call To Action button to add underneath the content. i.e. "Learn More" or "Buy Now". Leave blank to hide.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'key' => 'field_572bac2b2d757e',
                           'label' => 'CTA Button URL',
                           'name' => 'cta_button_url',
                           'type' => 'url',
                           'instructions' => '',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  // Panel: Featured Makers
                  array(
                     'key' => '56fc6f9fdc4a2',
                     'name' => 'featured_makers_panel',
                     'label' => 'Featured Items',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_5727a9191b209',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 28,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_56fcb5958152f',
                           'label' => 'Panel Title',
                           'name' => 'title',
                           'type' => 'text',
                           'instructions' => 'i.e. "Featured Items". 28 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'layout' => 'vertical',
                           'choices' => array(
                              3 => 3,
                              6 => 6,
                              9 => 9
                           ),
                           'default_value' => 3,
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_5719832407ae9',
                           'label' => 'Amount of Items to show',
                           'name' => 'makers_to_show',
                           'type' => 'radio',
                           'instructions' => 'Show 3, 6, or 9 featured items',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'sub_fields' => array(
                              array(
                                 'return_format' => 'array',
                                 'preview_size' => 'thumbnail',
                                 'library' => 'all',
                                 'min_width' => '',
                                 'min_height' => '',
                                 'min_size' => '',
                                 'max_width' => '',
                                 'max_height' => '',
                                 'max_size' => '',
                                 'mime_types' => '',
                                 'key' => 'field_56fc70e8dc4a4',
                                 'label' => 'Image',
                                 'name' => 'maker_image',
                                 'type' => 'image',
                                 'instructions' => 'Images are best when square sizes around 500px x 500px.',
                                 'required' => 1,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 )
                              ),
                              array(
                                 'default_value' => '',
                                 'maxlength' => 19,
                                 'placeholder' => '',
                                 'prepend' => '',
                                 'append' => '',
                                 'key' => 'field_56fc7172dc4a5',
                                 'label' => 'Name',
                                 'name' => 'maker_name',
                                 'type' => 'text',
                                 'instructions' => 'Optional field. 24 character limit.',
                                 'required' => 0,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 ),
                                 'readonly' => 0,
                                 'disabled' => 0
                              ),
                              array(
                                 'default_value' => '',
                                 'maxlength' => 300,
                                 'placeholder' => '',
                                 'prepend' => '',
                                 'append' => '',
                                 'key' => 'field_56fc71a0dc4a6',
                                 'label' => 'Short Description',
                                 'name' => 'maker_short_description',
                                 'type' => 'textarea',
                                 'instructions' => 'Optional field. 300 character limit.',
                                 'required' => 0,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 ),
                                 'readonly' => 0,
                                 'disabled' => 0
                              ),
                              array(
                                 'default_value' => '',
                                 'placeholder' => '',
                                 'key' => 'field_56fc71a0dc4a7',
                                 'label' => 'More Info URL',
                                 'name' => 'more_info_url',
                                 'type' => 'url',
                                 'instructions' => 'Optional link to more information. Leave URL field blank to hide.',
                                 'required' => 0,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 )
                              )
                           ),
                           'min' => 1,
                           'max' => 9,
                           'layout' => 'table',
                           'button_label' => 'Add New Item',
                           'collapsed' => '',
                           'key' => 'field_56fc6fc3dc4a3',
                           'label' => 'Featured Items',
                           'name' => 'featured_makers',
                           'type' => 'repeater',
                           'instructions' => 'Adds a panel for 1-3 rows of featured items. Each item features an image, name, and short description. Start by clicking the "Add New Item" button for each featured item to show in this panel.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 30,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_571ab19c2e7ec',
                           'label' => 'Text',
                           'name' => 'cta_text',
                           'type' => 'text',
                           'instructions' => 'Type the CTA link text here. 30 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'key' => 'field_571ab19c2e7ed',
                           'label' => 'CTA Link',
                           'name' => 'cta_url',
                           'type' => 'url',
                           'instructions' => 'Optional button to link to a page with more items. Leave URL field blank to hide.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Blue' => 'Blue',
                              'White' => 'White'
                           ),
                           'default_value' => 'Blue',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_5727eedc044ee',
                           'label' => 'Background Color',
                           'name' => 'background_color',
                           'type' => 'radio',
                           'instructions' => 'Background color of this panel. Choose blue or white.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  // Panel: Featured Makers/Presenters Dynamic
                  array(
                     'key' => '579924c93b1b9',
                     'name' => 'featured_makers_panel_dynamic',
                     'label' => 'Featured Makers - Dynamic',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_579924c93b1ba',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 28,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_579924c93b1bb',
                           'label' => 'Panel Title',
                           'name' => 'title',
                           'type' => 'text',
                           'instructions' => 'i.e. "Featured Makers". 28 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'min' => '',
                           'max' => '',
                           'step' => '',
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_579925463b1d9',
                           'label' => 'Enter formid here',
                           'name' => 'enter_formid_here',
                           'type' => 'number',
                           'instructions' => 'Enter the form to pull featured individuals from. They must have the \'Featured Maker\' flag set to be pulled in.',
                           'required' => 1,
                           'conditional_logic' => '',
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'layout' => 'vertical',
                           'choices' => array(
                              3 => 3,
                              6 => 6,
                              9 => 9
                           ),
                           'default_value' => 34,
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_579924c93b1bc',
                           'label' => 'Number of Makers to show',
                           'name' => 'makers_to_show',
                           'type' => 'radio',
                           'instructions' => 'Show 3, 6, or 9 Makers',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 50,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_579924c93b1bd',
                           'label' => 'Text',
                           'name' => 'cta_text',
                           'type' => 'text',
                           'instructions' => 'Type the CTA link text here. 50 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'key' => 'field_579924c93b1c2',
                           'label' => 'CTA Link',
                           'name' => 'cta_url',
                           'type' => 'url',
                           'instructions' => 'Optional button to link to a page with more makers. Leave URL field blank to hide.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Blue' => 'Blue',
                              'White' => 'White'
                           ),
                           'default_value' => 'Blue',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_579924c93b1c3',
                           'label' => 'Background Color',
                           'name' => 'background_color',
                           'type' => 'radio',
                           'instructions' => 'Background color of this panel. Choose blue or white.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  
                  // Panel: Featured Faires
                  array(
                     'key' => '571518b722bb0',
                     'name' => 'featured_faires_panel',
                     'label' => 'Featured Faires - Dynamic',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_571518b722bb0b',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        
                        array(
                           'key' => 'field_571518b722bb0c',
                           'label' => 'Featured Faires Page URL',
                           'name' => 'featured_faires_page_url',
                           'type' => 'url',
                           'instructions' => 'This is used for pulling in the featured faires data.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'default_value' => '',
                           'placeholder' => ''
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_571518b722bb0d',
                           'label' => 'Title',
                           'name' => 'featured_faires_title',
                           'type' => 'text',
                           'instructions' => 'Displayed above the faires.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'layout' => 'vertical',
                           'choices' => array(
                              3 => 3,
                              6 => 6,
                              9 => 9
                           ),
                           'default_value' => 34,
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_571518b722bb0e',
                           'label' => 'Num of Faires to show',
                           'name' => 'faires_to_show',
                           'type' => 'radio',
                           'instructions' => 'Show 3, 6, or 9',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 50,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_571518b722bb0f',
                           'label' => 'More Faires text',
                           'name' => 'more_faires_text',
                           'type' => 'text',
                           'instructions' => 'Enter the button text to link to more faires. 50 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'key' => 'field_571518b722bb0g',
                           'label' => 'More Faires Link',
                           'name' => 'more_faires_url',
                           'type' => 'url',
                           'instructions' => 'Optional button to link to a page with more faires. Leave URL field blank to hide.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        )
                     
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  
                  // Panel: Sponsors
                  array(
                     'key' => '571518b722ba0',
                     'name' => 'sponsors_panel',
                     'label' => 'Sponsors',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_5727a9d21b20b',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        
                        array(
                           'key' => 'field_5727a9d21b20c',
                           'label' => 'Sponsors Page URL',
                           'name' => 'sponsors_page_url',
                           'type' => 'url',
                           'instructions' => 'This is used for pulling in the sponsor data for this faire.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'default_value' => '',
                           'placeholder' => ''
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 4,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_5727a9d21b20d',
                           'label' => 'Text',
                           'name' => 'sponsors_page_year',
                           'type' => 'text',
                           'instructions' => 'Type the 4 digit year of the faire here. 4 character limit.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  // Panel: Star CTA
                  array(
                     'key' => '571e869b082c2',
                     'name' => 'call_to_action_panel',
                     'label' => 'Star Ribbon Panel',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_5727aa011b20c',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'default_value' => '',
                           'maxlength' => 50,
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'key' => 'field_571e86b7082c3',
                           'label' => 'Text',
                           'name' => 'text',
                           'type' => 'text',
                           'instructions' => 'Type the CTA message here. 50 character limit.',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        ),
                        array(
                           'default_value' => '',
                           'placeholder' => '',
                           'key' => 'field_571e86fa082c4',
                           'label' => 'URL',
                           'name' => 'url',
                           'type' => 'url',
                           'instructions' => '',
                           'required' => 1,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        ),
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Blue' => 'Blue',
                              'Light Blue' => 'Light Blue',
                              'Red' => 'Red',
                              'Orange' => 'Orange'
                           ),
                           'default_value' => 'Blue',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_5775a4459a43g',
                           'label' => 'Background Color',
                           'name' => 'background_color',
                           'type' => 'radio',
                           'instructions' => 'Background color of this panel. Choose blue, light blue, red or orange.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  // Panel: Ribbon Separator
                  array(
                     'key' => '571e8hfiHIu8f',
                     'name' => 'ribbon_separator_panel',
                     'label' => 'Ribbon Separator Panel',
                     'display' => 'row',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_57f8dfh89hsndjn',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        // array(
                        //    'default_value' => '',
                        //    'maxlength' => 50,
                        //    'placeholder' => '',
                        //    'prepend' => '',
                        //    'append' => '',
                        //    'key' => 'field_571e8fsdfhUHIU8d',
                        //    'label' => 'Text',
                        //    'name' => 'text',
                        //    'type' => 'text',
                        //    'instructions' => 'Type the CTA message here. 50 character limit.',
                        //    'required' => 1,
                        //    'conditional_logic' => 0,
                        //    'wrapper' => array(
                        //       'width' => '',
                        //       'class' => '',
                        //       'id' => ''
                        //    ),
                        //    'readonly' => 0,
                        //    'disabled' => 0
                        // ),
                        // array(
                        //    'default_value' => '',
                        //    'placeholder' => '',
                        //    'key' => 'field_571e899dshIUHG988h',
                        //    'label' => 'URL',
                        //    'name' => 'url',
                        //    'type' => 'url',
                        //    'instructions' => '',
                        //    'required' => 1,
                        //    'conditional_logic' => 0,
                        //    'wrapper' => array(
                        //       'width' => '',
                        //       'class' => '',
                        //       'id' => ''
                        //    )
                        // ),
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Blue' => 'Blue',
                              'Light Blue' => 'Light Blue',
                              'Red' => 'Red',
                              'Orange' => 'Orange'
                           ),
                           'default_value' => 'Blue',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_577sda89s8hiUHI87d',
                           'label' => 'Background Color',
                           'name' => 'background_color',
                           'type' => 'radio',
                           'instructions' => 'Background color of this panel. Choose blue, light blue, red or orange.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  // Panel: Image w/text
                  array(
                     'key' => '572bad2b2d757',
                     'name' => '1_column',
                     'label' => 'Hero Panel',
                     'display' => 'block',
                     'sub_fields' => array(
                        array(
                           'layout' => 'horizontal',
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'default_value' => 'Active',
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'allow_null' => 0,
                           'return_format' => 'value',
                           'key' => 'field_572bad2b2d758',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => 'Activate or Inactivate this panel',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => 'activeinactive',
                              'id' => ''
                           )
                        ),
                        array(
                           'key' => 'field_572bad2b2d751',
                           'label' => 'Hero image Repeater',
                           'name' => 'hero_image_repeater',
                           'type' => 'repeater',
                           'instructions' => 'Upload 1-10 images for use as the hero image on the page. The displayed image will be randomly selected from these.<br/>Optimal size is 1920 x 490',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'collapsed' => '',
                           'min' => 1,
                           'max' => 10,
                           'layout' => 'table',
                           'button_label' => '',
                           'sub_fields' => array(
                              // Array of Images
                              array(
                                 'key' => 'field_572bad2b2d752',
                                 'label' => 'Hero image',
                                 'name' => 'hero_image_random',
                                 'type' => 'image',
                                 'instructions' => '',
                                 'required' => 0,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 ),                           
                                 'return_format' => 'array',
                                 'preview_size' => 'thumbnail',
                                 'library' => 'all',
                                 'min_width' => '',
                                 'min_height' => '',
                                 'min_size' => '',
                                 'max_width' => '',
                                 'max_height' => '',
                                 'max_size' => '',
                                 'mime_types' => ''
                              ),
                              // Image Link/URL
                              array( 
                                 'key' => 'field_5b4e6672c7h98',
                                 'label' => 'Image Link',
                                 'name' => 'image_cta',
                                 'type' => 'url',
                                 'instructions' => 'Optional - If supplied, this will make the image a clickable link.',
                                 'required' => 0,
                                 'conditional_logic' => array(
                                    array(
                                       array(
                                          'field' => 'field_572bad2b2e752',
                                          'operator' => '==',
                                          'value' => 'image'
                                       )
                                    )
                                 ),
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 ),
                                 'default_value' => '',
                                 'placeholder' => ''
                              ), // End of Image Link
                           ), // End Array of Images
                        ), // End of Hero Image Repeater
                        array(
                           'tabs' => 'all',
                           'toolbar' => 'full',
                           'media_upload' => 1,
                           'default_value' => '',
                           'delay' => 0,
                           'key' => 'field_572bad2b2d757',
                           'label' => 'Hero Title',
                           'name' => 'column_title',
                           'type' => 'wysiwyg',
                           'instructions' => 'This title is displayed over the content with a semi transparent white background',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'readonly' => 0,
                           'disabled' => 0
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                  
                  // Panel: 3 column - photo and text
                  array(
                     'key' => '5b4e51639ab7e',
                     'name' => '3_column',
                     'label' => '3 column',
                     'display' => 'block',
                     'sub_fields' => array(
                        array(
                           'key' => 'field_5b4e70db5d7d7',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => '',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'allow_null' => 0,
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'default_value' => 'active',
                           'layout' => 'horizontal',
                           'return_format' => 'value'
                        ),
                        array(
                           'key' => 'field_5b4e70905d7d6',
                           'label' => 'Panel Title',
                           'name' => 'panel_title',
                           'type' => 'text',
                           'instructions' => 'Optional: 50 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'default_value' => '',
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'maxlength' => 50
                        ),
                        array(
                           'key' => 'field_5b4e5bec567f5',
                           'label' => 'Columns',
                           'name' => 'column',
                           'type' => 'repeater',
                           'instructions' => '',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'collapsed' => '',
                           'min' => 3,
                           'max' => 3,
                           'layout' => 'table',
                           'button_label' => '',
                           'sub_fields' => array(
                              array(
                                 'key' => 'field_5b4e5177fec84',
                                 'label' => 'Type',
                                 'name' => 'column_type',
                                 'type' => 'radio',
                                 'instructions' => '',
                                 'required' => 1,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '30',
                                    'class' => '',
                                    'id' => ''
                                 ),
                                 'choices' => array(
                                    'image' => 'Image with optional link',
                                    'paragraph' => 'Paragraph text',
                                    'list' => 'List of items with optional links'
                                 ),
                                 'allow_null' => 0,
                                 'other_choice' => 0,
                                 'save_other_choice' => 0,
                                 'default_value' => 'image',
                                 'layout' => 'vertical',
                                 'return_format' => 'value'
                              ),
                              array(
                                 'key' => 'field_5b4e645f30c5e',
                                 'label' => 'Data',
                                 'name' => 'data',
                                 'type' => 'group',
                                 'instructions' => '',
                                 'required' => 0,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 ),
                                 'layout' => 'block',
                                 // Array of Images
                                 'sub_fields' => array(
                                    //Image
                                    array(
                                       'key' => 'field_5b4e54c9fec85',
                                       'label' => 'Image',
                                       'name' => 'column_image_field',
                                       'type' => 'image',
                                       'instructions' => 'Upload an image',
                                       'required' => 1,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'image'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'return_format' => 'array',
                                       'preview_size' => 'thumbnail',
                                       'library' => 'all',
                                       'min_width' => '',
                                       'min_height' => '',
                                       'min_size' => '',
                                       'max_width' => '',
                                       'max_height' => '',
                                       'max_size' => '',
                                       'mime_types' => ''
                                    ),
                                    // Image Link/URL
                                    array(
                                       'key' => 'field_5b4e6672c7f98',
                                       'label' => 'Image Link',
                                       'name' => 'image_cta',
                                       'type' => 'url',
                                       'instructions' => 'Optional - If supplied, this will make the image a clickable link.',
                                       'required' => 0,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'image'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'default_value' => '',
                                       'placeholder' => ''
                                    ),
                                    array(
                                       'key' => 'field_5b4e66a4c7f99',
                                       'label' => 'Link Text',
                                       'name' => 'image_cta_text',
                                       'type' => 'text',
                                       'instructions' => 'Optional - If supplied, an additional link is displayed below the image using this text.',
                                       'required' => 0,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'image'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'default_value' => '',
                                       'placeholder' => '',
                                       'prepend' => '',
                                       'append' => '',
                                       'maxlength' => ''
                                    ),
                                    array(
                                       'key' => 'field_5b4e66a4c7f90',
                                       'label' => 'Alignment',
                                       'name' => 'column_list_alignment',
                                       'type' => 'radio',
                                       'instructions' => '',
                                       'required' => 0,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'image'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '100',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'choices' => array(
                                          'left' => 'Left',
                                          'center' => 'Center',
                                          'right' => 'Right'
                                       ),
                                       'allow_null' => 0,
                                       'other_choice' => 0,
                                       'save_other_choice' => 0,
                                       'default_value' => 'left',
                                       'layout' => 'vertical',
                                       'return_format' => 'value'
                                    ),
                                    array(
                                       'key' => 'field_5b4e54fdfec86',
                                       'label' => 'Paragraph',
                                       'name' => 'column_paragraph',
                                       'type' => 'textarea',
                                       'instructions' => 'Character limit is 350',
                                       'required' => 1,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'paragraph'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'default_value' => '',
                                       'placeholder' => '',
                                       'maxlength' => 350,
                                       'rows' => '',
                                       'new_lines' => ''
                                    ),
                                    array(
                                       'key' => 'field_5b4e61ffa92ef',
                                       'label' => 'List Title',
                                       'name' => 'list_title',
                                       'type' => 'text',
                                       'instructions' => '',
                                       'required' => 0,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'list'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '105',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'default_value' => '',
                                       'placeholder' => 'ie: Helpful Links',
                                       'prepend' => '',
                                       'append' => '',
                                       'maxlength' => 30
                                    ),
                                    array(
                                       'key' => 'field_5b4e55f4fec87',
                                       'label' => 'List fields',
                                       'name' => 'column_list_fields',
                                       'type' => 'repeater',
                                       'instructions' => 'Enter in your list items and (if appropriate) their urls (maximum of 5)',
                                       'required' => 0,
                                       'conditional_logic' => array(
                                          array(
                                             array(
                                                'field' => 'field_5b4e5177fec84',
                                                'operator' => '==',
                                                'value' => 'list'
                                             )
                                          )
                                       ),
                                       'wrapper' => array(
                                          'width' => '100',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'collapsed' => 'field_5b4e561bfec88',
                                       'min' => 1,
                                       'max' => 5,
                                       'layout' => 'table',
                                       'button_label' => '',
                                       'sub_fields' => array(
                                          array(
                                             'key' => 'field_5b4e561bfec88',
                                             'label' => 'Label',
                                             'name' => 'list_text',
                                             'type' => 'text',
                                             'instructions' => '',
                                             'required' => 1,
                                             'conditional_logic' => 0,
                                             'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => ''
                                             ),
                                             'default_value' => '',
                                             'placeholder' => '',
                                             'prepend' => '',
                                             'append' => '',
                                             'maxlength' => ''
                                          ),
                                          array(
                                             'key' => 'field_5b4e562bfec89',
                                             'label' => 'Link',
                                             'name' => 'list_link',
                                             'type' => 'url',
                                             'instructions' => '',
                                             'required' => 0,
                                             'conditional_logic' => 0,
                                             'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => ''
                                             ),
                                             'default_value' => '',
                                             'placeholder' => ''
                                          )
                                       )
                                    )
                                 )
                              )
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  ),
                   
                  // Panel: 6 column - 6 column navigation panel
                  array(
                     'key' => '5b4e51639cd8f',
                     'name' => '6_column',
                     'label' => '6 column navigation panel',
                     'display' => 'block',
                     'sub_fields' => array(
                        array(             
                           'key' => 'field_5b4e70cd5d7d8',
                           'label' => 'Active/Inactive',
                           'name' => 'activeinactive',
                           'type' => 'radio',
                           'instructions' => '',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'choices' => array(
                              'Active' => 'Active',
                              'Inactive' => 'Inactive'
                           ),
                           'allow_null' => 0,
                           'other_choice' => 0,
                           'save_other_choice' => 0,
                           'default_value' => 'active',
                           'layout' => 'horizontal',
                           'return_format' => 'value'
                        ),
                        array(       
                           'key' => 'field_5b4e70cd5d7d9',
                           'label' => 'Panel Title',
                           'name' => 'panel_title',
                           'type' => 'text',
                           'instructions' => 'Optional: 50 character limit.',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'default_value' => '',
                           'placeholder' => '',
                           'prepend' => '',
                           'append' => '',
                           'maxlength' => 50
                        ),
                        array(
                           'key' => 'field_5b4e70cd5d7d0',
                           'label' => 'Columns',
                           'name' => 'column',
                           'type' => 'repeater',
                           'instructions' => '',
                           'required' => 0,
                           'conditional_logic' => 0,
                           'wrapper' => array(
                              'width' => '',
                              'class' => '',
                              'id' => ''
                           ),
                           'collapsed' => '',
                           'min' => 2,
                           'max' => 6,
                           'layout' => 'table',
                           'button_label' => '',
                           'sub_fields' => array(                              
                              array(
                                 'key' => 'field_5b4e70cd5d7d2',
                                 'label' => 'Data',
                                 'name' => 'data',
                                 'type' => 'group',
                                 'instructions' => '',
                                 'required' => 0,
                                 'conditional_logic' => 0,
                                 'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => ''
                                 ),
                                 'layout' => 'block',
                                 // Array of Images
                                 'sub_fields' => array(
                                    //Image
                                    array(
                                       'key' => 'field_5b4e70cd5d7d3',
                                       'label' => 'Image',
                                       'name' => 'column_image_field',
                                       'type' => 'image',
                                       'instructions' => 'Upload an image',
                                       'required' => 1,                                  
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'return_format' => 'array',
                                       'preview_size' => 'thumbnail',
                                       'library' => 'all',
                                       'min_width' => '',
                                       'min_height' => '',
                                       'min_size' => '',
                                       'max_width' => '',
                                       'max_height' => '',
                                       'max_size' => '',
                                       'mime_types' => ''
                                    ),
                                    // Image Link/URL
                                    array(
                                       'key' => 'field_5b4e70cd5d7d4',
                                       'label' => 'Image Link',
                                       'name' => 'image_cta',
                                       'type' => 'url',
                                       'instructions' => 'Optional - If supplied, this will make the image a clickable link.',
                                       'required' => 0,                          
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'default_value' => '',
                                       'placeholder' => ''
                                    ),
                                    array(
                                       'key' => 'field_5b4e70cd5d7d5',
                                       'label' => 'Link Text',
                                       'name' => 'image_cta_text',
                                       'type' => 'text',
                                       'instructions' => 'Optional - If supplied, an additional link is displayed below the image using this text.',
                                       'required' => 0,                                      
                                       'wrapper' => array(
                                          'width' => '',
                                          'class' => '',
                                          'id' => ''
                                       ),
                                       'default_value' => '',
                                       'placeholder' => '',
                                       'prepend' => '',
                                       'append' => '',
                                       'maxlength' => ''
                                    )                                                                                                          
                                 )
                              )
                           )
                        )
                     ),
                     'min' => '',
                     'max' => ''
                  )
                  /*
                * // Panel: Image Carousel
                * array(
                * 'key' => '572d9f7f52da4',
                * 'name' => 'static_or_carousel',
                * 'label' => 'Image Carousel (Rectangle)',
                * 'display' => 'block',
                * 'sub_fields' => array(
                * array(
                * 'layout' => 'horizontal',
                * 'choices' => array(
                * 'Active' => 'Active',
                * 'Inactive' => 'Inactive',
                * ),
                * 'default_value' => 'Active',
                * 'other_choice' => 0,
                * 'save_other_choice' => 0,
                * 'allow_null' => 0,
                * 'return_format' => 'value',
                * 'key' => 'field_572daf4770904',
                * 'label' => 'Active/Inactive',
                * 'name' => 'activeinactive',
                * 'type' => 'radio',
                * 'instructions' => 'Activate or Inactivate this panel',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => 50,
                * 'class' => 'activeinactive',
                * 'id' => '',
                * ),
                * ),
                * array(
                * 'layout' => 'vertical',
                * 'choices' => array(
                * 'Content Width' => 'Content Width',
                * 'Browser Width' => 'Browser Width',
                * ),
                * 'default_value' => 'Content Width',
                * 'other_choice' => 0,
                * 'save_other_choice' => 0,
                * 'allow_null' => 0,
                * 'return_format' => 'value',
                * 'key' => 'field_573d09f7c7a5a',
                * 'label' => 'Width',
                * 'name' => 'width',
                * 'type' => 'radio',
                * 'instructions' => 'Content width or browser width.',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => 50,
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array(
                * 'sub_fields' => array(
                * array(
                * 'return_format' => 'array',
                * 'preview_size' => 'thumbnail',
                * 'library' => 'all',
                * 'min_width' => '',
                * 'min_height' => '',
                * 'min_size' => '',
                * 'max_width' => '',
                * 'max_height' => '',
                * 'max_size' => '',
                * 'mime_types' => '',
                * 'key' => 'field_572da05c52da6',
                * 'label' => 'Image',
                * 'name' => 'image',
                * 'type' => 'image',
                * 'instructions' => '',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array(
                * 'default_value' => '',
                * 'maxlength' => 40,
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_572da08d52da7',
                * 'label' => 'Text',
                * 'name' => 'text',
                * 'type' => 'text',
                * 'instructions' => '40 Character Limit',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * 'readonly' => 0,
                * 'disabled' => 0,
                * ),
                * array(
                * 'default_value' => '',
                * 'placeholder' => '',
                * 'key' => 'field_573b9ce46699c',
                * 'label' => 'URL',
                * 'name' => 'url',
                * 'type' => 'url',
                * 'instructions' => 'Add a URL here if you want the image to link to another page.',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * ),
                * 'min' => 1,
                * 'max' => 10,
                * 'layout' => 'table',
                * 'button_label' => 'Add More Image',
                * 'collapsed' => '',
                * 'key' => 'field_572d9faa52da5',
                * 'label' => 'Images',
                * 'name' => 'images',
                * 'type' => 'repeater',
                * 'instructions' => 'Minimum of 1 image. Max 10 images.',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * ),
                * 'min' => '',
                * 'max' => '',
                * ),
                * // Panel: Image Carousel (square) panel
                * array(
                * 'key' => '573d16220b295',
                * 'name' => 'square_image_carousel',
                * 'label' => 'Image Carousel (Square)',
                * 'display' => 'block',
                * 'sub_fields' => array(
                * array(
                * 'layout' => 'horizontal',
                * 'choices' => array(
                * 'Active' => 'Active',
                * 'Inactive' => 'Inactive',
                * ),
                * 'default_value' => 'Active',
                * 'other_choice' => 0,
                * 'save_other_choice' => 0,
                * 'allow_null' => 0,
                * 'return_format' => 'value',
                * 'key' => 'field_573d16220b296',
                * 'label' => 'Active/Inactive',
                * 'name' => 'activeinactive',
                * 'type' => 'radio',
                * 'instructions' => 'Activate or Inactivate this panel',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => 50,
                * 'class' => 'activeinactive',
                * 'id' => '',
                * ),
                * ),
                * array(
                * 'layout' => 'horizontal',
                * 'choices' => array(
                * 'Content Width' => 'Content Width',
                * 'Browser Width' => 'Browser Width',
                * ),
                * 'default_value' => 'Content Width',
                * 'other_choice' => 0,
                * 'save_other_choice' => 0,
                * 'allow_null' => 0,
                * 'return_format' => 'value',
                * 'key' => 'field_573d16220b297',
                * 'label' => 'Width',
                * 'name' => 'width',
                * 'type' => 'radio',
                * 'instructions' => 'Content width or browser width.',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => 50,
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array(
                * 'sub_fields' => array(
                * array(
                * 'return_format' => 'array',
                * 'preview_size' => 'thumbnail',
                * 'library' => 'all',
                * 'min_width' => '',
                * 'min_height' => '',
                * 'min_size' => '',
                * 'max_width' => '',
                * 'max_height' => '',
                * 'max_size' => '',
                * 'mime_types' => '',
                * 'key' => 'field_573d16220b299',
                * 'label' => 'Image',
                * 'name' => 'image',
                * 'type' => 'image',
                * 'instructions' => '',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array(
                * 'default_value' => '',
                * 'maxlength' => 40,
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_573d16220b29a',
                * 'label' => 'Text',
                * 'name' => 'text',
                * 'type' => 'text',
                * 'instructions' => '40 Character Limit',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * 'readonly' => 0,
                * 'disabled' => 0,
                * ),
                * array(
                * 'default_value' => '',
                * 'placeholder' => '',
                * 'key' => 'field_573d16220b29b',
                * 'label' => 'URL',
                * 'name' => 'url',
                * 'type' => 'url',
                * 'instructions' => 'Add a URL here if you want the image to link to another page.',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * ),
                * 'min' => 3,
                * 'max' => 10,
                * 'layout' => 'table',
                * 'button_label' => 'Add More Image',
                * 'collapsed' => '',
                * 'key' => 'field_573d16220b298',
                * 'label' => 'Images',
                * 'name' => 'images',
                * 'type' => 'repeater',
                * 'instructions' => 'Minimum of 3 images. Max 10 images.',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array(
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * ),
                * 'min' => '',
                * 'max' => 1,
                * ),
                * // Panel: Social media
                * array (
                * 'key' => '57b20ae569288',
                * 'name' => 'social_media',
                * 'label' => 'Social Media',
                * 'display' => 'block',
                * 'sub_fields' => array (
                * array (
                * 'layout' => 'vertical',
                * 'choices' => array (
                * 'Active' => 'Active',
                * 'Inactive' => 'Inactive',
                * ),
                * 'default_value' => 'Active',
                * 'other_choice' => 0,
                * 'save_other_choice' => 0,
                * 'allow_null' => 0,
                * 'return_format' => 'value',
                * 'key' => 'field_57b215b4644e8',
                * 'label' => 'Active/Inactive',
                * 'name' => 'activeinactive',
                * 'type' => 'radio',
                * 'instructions' => 'Activate or Inactivate this panel',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array (
                * 'default_value' => '',
                * 'maxlength' => '',
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_57b22cbb9598e',
                * 'label' => 'Title',
                * 'name' => 'panel_title',
                * 'type' => 'text',
                * 'instructions' => 'Optional: Add a title to this panel. e.g. "Follow us on Social Media"',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * 'readonly' => 0,
                * 'disabled' => 0,
                * ),
                * array (
                * 'layouts' => array (
                * array (
                * 'key' => '57b20b62ee5c3',
                * 'name' => 'facebook',
                * 'label' => 'Facebook',
                * 'display' => 'block',
                * 'sub_fields' => array (
                * array (
                * 'default_value' => '',
                * 'maxlength' => '',
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_57b37bd956349',
                * 'label' => 'Text above Facebook feed',
                * 'name' => 'fb_title',
                * 'type' => 'text',
                * 'instructions' => '',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array (
                * 'default_value' => '',
                * 'placeholder' => '',
                * 'key' => 'field_57b20c5f6928a',
                * 'label' => 'Facebook URL',
                * 'name' => 'facebook_url',
                * 'type' => 'url',
                * 'instructions' => 'Enter a Facebook page URL to generate the feed. e.g. "https://www.facebook.com/makerfaire/"',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * ),
                * 'min' => '',
                * 'max' => '',
                * ),
                * array (
                * 'key' => '57b218d7b1f40',
                * 'name' => 'twitter',
                * 'label' => 'Twitter',
                * 'display' => 'block',
                * 'sub_fields' => array (
                * array (
                * 'default_value' => '',
                * 'maxlength' => '',
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_57b37bf35634b',
                * 'label' => 'Text above Twitter feed',
                * 'name' => 'tw_title',
                * 'type' => 'text',
                * 'instructions' => '',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array (
                * 'default_value' => '',
                * 'maxlength' => '',
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_57b218e0b1f41',
                * 'label' => 'Twitter Handle',
                * 'name' => 'twitter_id',
                * 'type' => 'text',
                * 'instructions' => 'Enter Twitter name(handle). e.g. "makerfaire" or "MakerCamp"',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * 'readonly' => 0,
                * 'disabled' => 0,
                * ),
                * ),
                * 'min' => '',
                * 'max' => '',
                * ),
                * array (
                * 'key' => '57b26f1be766d',
                * 'name' => 'instagram',
                * 'label' => 'Instagram',
                * 'display' => 'block',
                * 'sub_fields' => array (
                * array (
                * 'default_value' => '',
                * 'maxlength' => '',
                * 'placeholder' => '',
                * 'prepend' => '',
                * 'append' => '',
                * 'key' => 'field_57b37c0b5634c',
                * 'label' => 'Text above Instagram feed',
                * 'name' => 'ig_title',
                * 'type' => 'text',
                * 'instructions' => '',
                * 'required' => 0,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * array (
                * 'default_value' => '',
                * 'new_lines' => '',
                * 'maxlength' => '',
                * 'placeholder' => '',
                * 'rows' => '',
                * 'key' => 'field_57b26f23e766e',
                * 'label' => 'Paste code here',
                * 'name' => 'instagram_iframe',
                * 'type' => 'textarea',
                * 'instructions' => 'Go to this URL to get the iframe code to paste here. https://snapwidget.com/widgets/create?plan=free&service=instagram&type=grid
                * <br/><br/>
                * Snapwidget will have you login to Instagram to generate the code. Default settings will work fine, and then click the "Get Widget" button.',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * 'readonly' => 0,
                * 'disabled' => 0,
                * ),
                * ),
                * 'min' => '',
                * 'max' => '',
                * ),
                * ),
                * 'min' => '',
                * 'max' => 3,
                * 'button_label' => 'Add Social Feed',
                * 'key' => 'field_57b20b0b69289',
                * 'label' => 'Active Feeds',
                * 'name' => 'active_feeds',
                * 'type' => 'flexible_content',
                * 'instructions' => 'Click the "Add Social Feed" button for each Social media feeds. Up to 3 feeds can be added.',
                * 'required' => 1,
                * 'conditional_logic' => 0,
                * 'wrapper' => array (
                * 'width' => '',
                * 'class' => '',
                * 'id' => '',
                * ),
                * ),
                * ),
                * 'min' => '',
                * 'max' => '',
                * ),
                */
               ),
               'min' => '',
               'max' => '',
               'button_label' => 'Add New Panel',
               'key' => 'field_573b53999849d',
               'label' => 'Content Panels',
               'name' => 'content_panels',
               'type' => 'flexible_content',
               'instructions' => 'Add panels here by clicking the "Add New Panel" button at the bottom right side. Panel order can be changed by dragging each up or down.',
               'required' => 0,
               'conditional_logic' => 0,
               'wrapper' => array(
                  'width' => '',
                  'class' => '',
                  'id' => ''
               )
            )
         ),
         'location' => array(
            array(
               array(
                  'param' => 'post_type',
                  'operator' => '==',
                  'value' => 'post'
               )
            ),
            array(
               array(
                  'param' => 'post_type',
                  'operator' => '==',
                  'value' => 'page'
               ),
               array(
                  'param' => 'page_template',
                  'operator' => '==',
                  'value' => 'page-flagship-faire-panels.php'
               )
            ),
            array(
               array(
                  'param' => 'post_type',
                  'operator' => '==',
                  'value' => 'page'
               ),
               array(
                  'param' => 'page_template',
                  'operator' => '==',
                  'value' => 'front-page.php'
               )
            )
         ),
         'menu_order' => 0,
         'position' => 'normal',
         'style' => 'default',
         'label_placement' => 'top',
         'instruction_placement' => 'label',
         'hide_on_screen' => '',
         'active' => 1,
         'description' => '',
         'modified' => 1463506572,
         'local' => 'php'
      ));
}
