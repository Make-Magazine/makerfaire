/**
 * Inline cell edit class
 */
var inlineEditClass = function (tableDescription, dataTableOptions, $) {
    var obj = {
        params: {
            tableId: tableDescription.tableId,
            currentCell: '',
            editInputId: 'dt_cell_edit',
            editSelector: '#dt_cell_edit',
            validationPopover: '<div class="wpDataTablesPopover editError"></div>',
            value: '',
            table: '',
            cellData: '',
            cellInfo: '',
            rowId: '',
            columnId: '',
            columnType: '',
            inputType: '',
            columnHeader: '',
            notNull: '',
            code: '',
            expandButton: '',
            valid: true,
            additionalValid: true,
            dateFormat: tableDescription.datepickFormat.replace(/y/g, 'yy').replace(/Y/g, 'yyyy').replace(/M/g, 'mmm')
        },
        // Remove a text selection on double click
        removeSelection: function () {
            if (document.selection && document.selection.empty) {
                document.selection.empty();
            } else if (window.getSelection) {
                var sel = window.getSelection();
                sel.removeAllRanges();
            }
            ;
        },
        // Measure an error popover position
        setErrorPopoverPosition: function (element, popover) {
            var position = '';
            var offset = element.offset();
            var width = element.outerWidth(true);
            var height = element.outerHeight(true);

            var centerX = offset.left + (width / 2) - (popover.width / 2);
            var centerY = offset.top - (popover.height);
            position = {"top": centerY, "left": centerX};

            return position;
        },
        // Parse a link for edit inputs
        parseLink: function () {
            if (obj.params.value.indexOf('<a ') != -1) {
                if ($.inArray(obj.params.inputType, ['text', 'link', 'textarea', 'email']) !== -1) {
                    var $link = $(obj.params.value);
                    if ($link.attr('href').indexOf($link.html()) === -1) {
                        obj.params.value = $link.attr('href').replace('mailto:', '') + '||' + $link.html();
                    } else {
                        obj.params.value = $link.html();
                    }
                } else {
                    obj.params.value = $(obj.params.value).html();
                }
            }
        },
        // Save a cell value function
        saveData: function (val, rowId, columnId) {
            $(tableDescription.selector).addClass('overlayed');
            wpDataTables[obj.params.tableId].fnUpdate(val, rowId, columnId, 0, 0);
            var data = wpDataTables[obj.params.tableId].fnGetData(rowId);
            wpDataTablesFunctions[obj.params.tableId].applyData(data);
            wpDataTablesFunctions[obj.params.tableId].saveTableData(1);
            $(tableDescription.selector).find('td').removeClass('editing');
        },
        // Validate email and url fields
        fieldValidation: function (type, element) {
            if (obj.params.value != '') {
                if (type == 'email') {
                    var field_valid = wdtValidateEmail(obj.params.value);
                    var message = wpdatatables_frontend_strings.invalid_email;
                } else if (type == 'link') {
                    var field_valid = wdtValidateURL(obj.params.value);
                    var message = wpdatatables_frontend_strings.invalid_link;
                }
                if (!field_valid) {
                    if (!element.closest('td').hasClass('error')) {
                        element.closest('td').addClass('error');
                        $('body').prepend(obj.params.validationPopover);
                        $('.wpDataTablesPopover.editError').html(message);
                        var popoverSize = {"width": $('.wpDataTablesPopover.editError').outerWidth(), "height": $('.wpDataTablesPopover.editError').outerHeight() + 7};
                        $('.wpDataTablesPopover.editError').css(obj.setErrorPopoverPosition(element, popoverSize));
                        setTimeout(function () {
                            $('.wpDataTablesPopover.editError').fadeOut('300', function () {
                                $(this).remove();
                                element.closest('td').removeClass('error');
                            });
                        }, 2000);
                    }
                    obj.params.valid = false;
                    obj.params.additionalValid = false;
                } else {
                    $(this).closest('td').removeClass('error');
                    obj.params.valid = true;
                    obj.params.additionalValid = true;
                }
            } else {
                obj.params.valid = true;
                obj.params.additionalValid = true;
            }
        },
        // Validate mandatory fields
        mandatoryFieldValidation: function (element) {
            if (element.val() == '' || !obj.params.additionalValid) {
                if (!element.closest('td').hasClass('error')) {
                    element.closest('td').addClass('error');
                    $('body').prepend(obj.params.validationPopover);
                    $('.wpDataTablesPopover.editError').html(wpdatatables_frontend_strings.cannot_be_empty);
                    var popoverSize = {"width": $('.wpDataTablesPopover.editError').outerWidth(), "height": $('.wpDataTablesPopover.editError').outerHeight() + 7};
                    $('.wpDataTablesPopover.editError').css(obj.setErrorPopoverPosition(element, popoverSize));
                    setTimeout(function () {
                        $('.wpDataTablesPopover.editError').fadeOut('300', function () {
                            $(this).remove();
                            element.closest('td').removeClass('error');
                        });
                    }, 2000);
                    obj.params.valid = false;
                }
            } else {
                element.closest('td').removeClass('error');
                obj.params.valid = true;
            }
        },
        // Merged save and validation function
        validateAndSave: function ($this) {
            // Validation
            var type_array = new Array('email', 'link');
            if ($.inArray(obj.params.inputType, type_array) !== -1) {
                obj.fieldValidation(obj.params.inputType, $this);
                if (obj.params.notNull) {
                    obj.mandatoryFieldValidation($this);
                }
            } else {
                if (obj.params.notNull) {
                    obj.mandatoryFieldValidation($this);
                }
            }

            // Saving
            if (obj.params.valid) {
                if (obj.params.inputType == 'date') {
                    datePickerInit(tableDescription.selector + ' .row_edit_datepicker', additional_params, 'opened');
                } else {
                    $.when(obj.saveData(obj.params.value, obj.params.rowId, obj.params.columnId)).then(obj.params.currentCell.prepend(obj.params.expandButton));
                }
            }
        },
        FieldTypeMethods: {
            noneditableCell: function () {
                obj.params.currentCell.removeClass('editing');
                var $this = obj.params.currentCell;
                var $value = obj.params.currentCell.html();
                obj.params.currentCell.empty().html('You can\'t edit this field');

                $(document).click(function () {
                    $this.html($value);
                });
            },
            textCell: function () {
                // Parse a link if there exists
                obj.parseLink();
                // Create an input for a selected cell editing
                obj.params.code = '<input type="text" data-input_type="' + obj.params.inputType + '" id="' + obj.params.editInputId + '" value=\'' + obj.params.value + '\' />';
                // Append a created input to a current cell
                obj.params.currentCell.empty().append(obj.params.code);

                // Establish a focus to a inserted input
                $(obj.params.editSelector).focus();

                // Saving event
                $(obj.params.editSelector).blur(function () {
                    obj.params.value = $(this).val();

                    obj.validateAndSave($(this));
                })
            },
            textareaCell: function () {
                // Parse a link if there exists
                obj.parseLink();
                // Replace 'br' tag to "\n"
                if (obj.params.value.indexOf('<br/>') != -1) {
                    obj.params.value = (obj.params.value).replace(/<br\/>/g, "\n");
                }
                // Create an input for a selected cell editing
                obj.params.code = '<textarea data-input_type="' + obj.params.inputType + '" id="' + obj.params.editInputId + '" rows="3" columns="50">' + obj.params.value + '</textarea>';

                // Append a created input to a current cell
                obj.params.currentCell.empty().append(obj.params.code);

                // Establish a focus to a inserted input
                $(obj.params.editSelector).focus();

                // Saving event
                $(obj.params.editSelector).blur(function () {
                    obj.params.value = $(this).val();

                    obj.validateAndSave($(this));
                })
            },
            linkCell: function () {
                // Parse a link if there exists
                obj.parseLink();
                // Create an input for a selected cell editing
                obj.params.code = '<input type="text" data-input_type="' + obj.params.inputType + '" id="' + obj.params.editInputId + '" value="' + obj.params.value + '" />';

                // Append a created input to a current cell
                obj.params.currentCell.empty().append(obj.params.code);

                // Establish a focus to a inserted input
                $(obj.params.editSelector).focus();

                // Saving event
                $(obj.params.editSelector).blur(function () {
                    obj.params.value = $(this).val();

                    obj.validateAndSave($(this));
                })
            },
            dateCell: function () {
                // Create an input for a selected cell editing
                obj.params.code = '<input type="text" class="row_edit_datepicker" data-input_type="' + obj.params.inputType + '" id="' + obj.params.editInputId + '" value="' + obj.params.value + '" />';

                // Append a created input to a current cell
                obj.params.currentCell.empty().append(obj.params.code);

                // Save a cell data as parameter for the datepicker's on set date event
                var additional_params = function () {
                    obj.params.value = $(obj.params.editSelector).val();
                    $.when(obj.saveData(obj.params.value, obj.params.rowId, obj.params.columnId)).then($(obj.params.editSelector).prepend(obj.params.expandButton));
                };

                // Saving
                if (obj.params.valid) {
                    obj.datePickerInit(tableDescription.selector + ' .row_edit_datepicker', additional_params, 'opened');
                }
                ;
            },
            selectboxCell: function () {
                // Clone a selectbox from appropriate edit modal's field
                obj.params.code = $('#' + tableDescription.tableId + '_' + obj.params.columnHeader).clone();

                // Append a cloned selectbox to a current cell
                obj.params.currentCell.empty().append(obj.params.code.attr('id', obj.params.editInputId).removeClass('selecter-element'));

                // Set a selected options for a cloned selectbox
                if (obj.params.value !== null) {
                    $.each(obj.params.value.split(", "), function (i, e) {
                        $(obj.params.editSelector + ' option[value="' + e + '"]').prop("selected", true);
                    });
                }

                // Establish a focus to a inserted selectbox
                $(obj.params.editSelector).selecter().focus();

                // Saving event
                if (obj.params.inputType == 'selectbox') {
                    obj.params.currentCell.css({'overflow': 'initial'});
                    $(obj.params.editSelector).change(function () {
                        obj.params.value = $(this).val();
                        if ($.type(obj.params.value) === 'array') {
                            obj.params.value = obj.params.value.join(', ');
                        }

                        obj.validateAndSave($(this));
                    })
                } else {
                    $(obj.params.editSelector).closest('.selecter').blur(function () {
                        obj.params.value = $('#' + obj.params.editInputId).val();
                        if ($.type(obj.params.value) === 'array') {
                            obj.params.value = obj.params.value.join(', ');
                        }

                        obj.validateAndSave($(this));
                    })
                }
            },
            attachmentCell: function () {
                // Clear and create necessary variables
                obj.params.value = '';
                var src = '';
                var fileNameContainer = '';

                if (obj.params.currentCell.find('img').length > 0) {
                    if (obj.params.currentCell.find('img').attr('src').length > 0) {
                        src = obj.params.currentCell.find('img').attr('src');
                    }
                } else if (obj.params.currentCell.find('a').length > 0) {
                    if (obj.params.currentCell.find('a').attr('href').length > 0) {
                        src = obj.params.currentCell.find('a').attr('href');
                    }
                } else {
                    src = obj.params.currentCell.html();
                }

                var fileName = src.substring(src.lastIndexOf('/') + 1);
                fileNameContainer = '<p>' + fileName + ' [<a href="#" data-key="row_edit_' + tableDescription.tableId + '_sets" class="wdtdeleteFile">' + wpdatatables_frontend_strings.detach_file + '</a>]</p>';

                // Create a control buttons and a file information block
                obj.params.code = '<span class="fileinput-button">\n\
				   <button id="row_edit_' + tableDescription.tableId + '_sets_button" data-column_type="icon" data-rel_input="row_edit_' + tableDescription.tableId + '_sets" class="btn fileupload_row_edit_' + tableDescription.tableId + '">' + wpdatatables_frontend_strings.browse_file + '</button>\n\
				   <input type="hidden" id="row_edit_' + tableDescription.tableId + '_sets" data-key="sets" data-input_type="attachment" class="editDialogInput" value="' + src + '" />\n\
				   <button class="btn row_edit_fileupload_submit_' + tableDescription.tableId + '">' + wpdatatables_frontend_strings.ok + '</button>\n\
			       </span>\n\
			       <div id="files_row_edit_' + tableDescription.tableId + '_sets" class="files">' + fileNameContainer + '</div>';

                // Append a created container to a current cell
                obj.params.currentCell.empty().append(obj.params.code);
                obj.fileUploadInit('row_edit_' + tableDescription.tableId);

                // Saving event
                $('.row_edit_fileupload_submit_' + tableDescription.tableId).click(function () {
                    obj.params.value = $('#row_edit_' + tableDescription.tableId + '_sets').val();
                    obj.params.value = '<a href="' + obj.params.value + '"></a>';
                    $('#row_edit_' + tableDescription.tableId + '_sets').closest('td').empty().html(obj.params.value);

                    obj.validateAndSave($(this));
                });
            }

        },
        // Apply fileuploaders
        fileUploadInit: function (selector) {
            if ($('.fileupload_' + selector).length) {

                // Extend the wp.media object
                wdtCustomUploader = wp.media({
                    title: wpdatatables_frontend_strings.select_upload_file,
                    button: {
                        text: wpdatatables_frontend_strings.choose_file
                    },
                    multiple: false
                });

                $('button.fileupload_' + selector).click(function (e) {
                    e.preventDefault();
                    var $button = $(this);
                    var $relInput = $('#' + $button.data('rel_input'));
                    if( obj.params.columnType == 'icon' ){
                        wdtCustomUploader = wp.media({
                            title: wpdatatables_frontend_strings.select_upload_file,
                            button: {
                                text: wpdatatables_frontend_strings.choose_file
                            },
                            multiple: false,
                            library: {
                                type: 'image'
                            }
                        });
                        wdtCustomUploader.off('select').on('select', function () {
                            attachment = wdtCustomUploader.state().get('selection').first().toJSON();
                            $relInput.val( attachment.sizes.thumbnail.url + '||' +  attachment.url );
                            $('#files_' + $button.data('rel_input')).html('<p>' + attachment.filename + ' [<a href="#" data-key="' + $button.data('rel_input') + '" class="wdtdeleteFile">' + wpdatatables_frontend_strings.detach_file + '</a>]</p>');
                        });
                    }else{
                            // When a file is selected, grab the URL and set it as the text field's value
                            wdtCustomUploader.off('select').on('select', function () {
                                attachment = wdtCustomUploader.state().get('selection').first().toJSON();
                                $relInput.val(attachment.url);
                                $('#files_' + $button.data('rel_input')).html('<p>' + attachment.filename + ' [<a href="#" data-key="' + $button.data('rel_input') + '" class="wdtdeleteFile">' + wpdatatables_frontend_strings.detach_file + '</a>]</p>');
                            });
                    }
                    // Open the uploader dialog
                    wdtCustomUploader.open();

                });

            }
        },
        // Apply pickadate
        datePickerInit: function (selector, additional_params, state) {
            var input = $(selector).pickadate({
                format: obj.params.dateFormat,
                formatSubmit: obj.params.dateFormat,
                selectYears: 20,
                selectMonths: true,
                container: '.wpDataTablesWrapper',
                onClose: additional_params
            });

            var picker = input.pickadate('picker');

            if (state == 'opened') {
                picker.open();
            }
        },
        // Cell editing on double click event
        bindClickEvent: function () {
            $(tableDescription.selector).on('dblclick', 'td', function (e) {

                // Prevent click event if current element is input, has invalid value or already is edited
                var target = e.target || e.srcElement;
                var elementName = target.tagName.toLowerCase();
                if (elementName == 'input' || !obj.params.valid || $(this).hasClass('editing')) {
                    return false;
                }

                // Remove a text selection inside a cell
                obj.removeSelection();

                // Add editing class
                $(this).addClass('editing');

                // Set variables
                obj.params.table = $(tableDescription.selector).DataTable();
                obj.params.cellData = obj.params.table.cell(this).data();
                obj.params.cellInfo = obj.params.table.cell(this).index();
                obj.params.columnId = obj.params.cellInfo.column;
                obj.params.columnType = wpDataTables[obj.params.tableId].fnSettings().aoColumns[obj.params.columnId].wdtType;
                obj.params.rowId = obj.params.cellInfo.row;
                obj.params.inputType = dataTableOptions.aoColumnDefs[obj.params.columnId]['InputType'];
                obj.params.columnHeader = dataTableOptions.aoColumnDefs[obj.params.columnId]['origHeader'];
                obj.params.notNull = dataTableOptions.aoColumnDefs[obj.params.columnId]['notNull'];
                obj.params.value = obj.params.cellData;
                obj.params.currentCell = $(this);

                // If a coulumn is resposive than record an expand button to variable
                if ($(this).children('.responsiveExpander') != '') {
                    obj.params.expandButton = $(this).children('.responsiveExpander');
                }

                // Cell editing depending on the type
                switch (obj.params.inputType) {

                    // Cell prohibited for editing
                    case 'none':
                        obj.FieldTypeMethods.noneditableCell();
                        break;

                    // Plain text cell
                    case 'text':
                        obj.FieldTypeMethods.textCell();
                        break;

                    // Multiple lines cell
                    case 'textarea':
                        obj.FieldTypeMethods.textareaCell();
                        break;

                    // Email and url cell
                    case 'link':
                    case 'email':
                        obj.FieldTypeMethods.linkCell();
                        break;

                    // Datepicker cell
                    case 'date':
                        obj.FieldTypeMethods.dateCell();
                        break;

                    // Single and multi selectbox cells
                    case 'multi-selectbox':
                    case 'selectbox':
                        obj.FieldTypeMethods.selectboxCell();
                        break;

                    // Attachment cell
                    case 'attachment':
                        obj.FieldTypeMethods.attachmentCell();
                        break;
                }
                ;

            });
        }
    }
    obj.bindClickEvent();
}