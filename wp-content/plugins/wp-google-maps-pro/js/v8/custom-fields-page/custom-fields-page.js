/**
 * @namespace WPGMZA
 * @module CustomFieldsPage
 * @requires WPGMZA.EventDispatcher
 */

jQuery(function($) {
    
    if(WPGMZA.currentPage != "custom-fields"){
        return;
    }

	WPGMZA.CustomFieldsPage = function(){
        var self = this;

        this.element = $(".custom-field-list-container");
        this.form = $("form#wpgmza-custom-fields");

        this.parseRowTemplate();

        this.element.sortable({
            items: ".wpgmza-custom-field-item-row",
            handle: ".wpgmza-drag-handle"      
        });

        this.form.on("submit", function(event) {
            self.onSubmit(event);
        });

        this.element.find(".icon-picker").each(function(event){
            var autocomplete = new WPGMZA.FontAwesomeIconPickerField(this);
        });

        this.element.on('click', '.field-action-btn', function(event){
            self.onFieldAction($(this));
        });

        this.element.on('keydown', '.html-attributes input', function(event){
            self.onAttributeKeydown(event);
        });

        this.element.parent().find('.custom-field-new-row-control').on('click', function(event){
            self.addRow();
        });
    }

    WPGMZA.extend(WPGMZA.CustomFieldsPage, WPGMZA.EventDispatcher);
    
    WPGMZA.CustomFieldsPage.createInstance = function(){
        return new WPGMZA.CustomFieldsPage();
    }

    WPGMZA.CustomFieldsPage.prototype.onFieldAction = function(element){
        var type = element.data('action');
        var row = element.parent().parent().parent();
        switch(type){
            case 'edit':
                this.onEdit(row);
                break;
            case 'delete':
                this.onDelete(row);
                break;
        }
    }

    WPGMZA.CustomFieldsPage.prototype.onEdit = function(row){
        row.find('.field-meta-container').toggleClass('wpgmza-hidden');
    }

    WPGMZA.CustomFieldsPage.prototype.onDelete = function(row){
        row.remove();
    }

    WPGMZA.CustomFieldsPage.prototype.parseRowTemplate = function(){
        this.rowTemplate = this.element.find('.wpgmza-custom-field-item-row.row-template').clone();
        this.rowTemplate.removeClass('row-template');
        this.element.find('.wpgmza-custom-field-item-row.row-template').remove();
    }

    WPGMZA.CustomFieldsPage.prototype.addRow = function(){
        this.element.append(this.rowTemplate.clone()).find(".icon-picker").each(function(event){
            var autocomplete = new WPGMZA.FontAwesomeIconPickerField(this);
        });
    }

    WPGMZA.CustomFieldsPage.prototype.onAttributeKeydown = function(event){
        var row = $(event.target).closest(".wpgmza-row");

        var attrName = row.find("input.attribute-name");
        var attrValue = row.find("input.attribute-value");

        switch(event.keyCode){
            case 13:    
                this.addAttributeRow(event);
                event.preventDefault();
                return false;
            case 8:
                if(attrName.val().length == 0 && attrValue.val().length == 0){
                    this.removeAttributeRow(event);
                    event.preventDefault();
                    return false;
                }
                
                if(attrValue.val().length == 0 && event.target == attrValue[0]){
                    attrName.focus();
                }
                break;
        }
    }

    WPGMZA.CustomFieldsPage.prototype.addAttributeRow = function(event){
        var row = $(event.target).closest(".wpgmza-row");
        var attrName = row.find("input.attribute-name");
        var attrValue = row.find("input.attribute-value");
        
        if(!$(attrName).val().length){
            $(attrName).focus();
            return;
        }
        
        var newAttribute = row.clone();
        newAttribute.find("input").val("");
        
        row.parent().append(newAttribute);
        
        newAttribute.find("input.attribute-name").focus();
    }

    WPGMZA.CustomFieldsPage.prototype.removeAttributeRow = function(event){
        var numRows = $(event.target).closest(".attributes").children(".wpgmza-row").length;
        
        if(numRows == 1){
            return; 
        }
        
        var row = $(event.target).closest(".wpgmza-row");
        var prevValueInput = row.prev(".wpgmza-row").find("input.attribute-value");
        
        row.remove();
        prevValueInput.focus();
    } 

    WPGMZA.CustomFieldsPage.prototype.onSubmit = function(event) {
        var names = [];
        
        this.element.children().find("input[name='names[]']").each(function(){
            names.push($(this).val());
        });
    
        for(var i = 0; i < names.length; i++){
            for(var j = i + 1; j < names.length; j++){
                if(names[i] == names[j]){
                    alert(WPGMZA.localized_strings.duplicate_custom_field_name);
                    event.preventDefault();
                    return false;         
                }
            }
        }

        this.element.find(".attributes").each(function(index, el) {
            var json = {};
            
            $(el).find("input.attribute-name").each(function(j, input) {
                var name = $(input).val();
                var val = $(input).closest(".wpgmza-row").find("input.attribute-value").val();
                json[name] = val;
            });
            
            $(el).find("input[name='attributes[]']").val(JSON.stringify(json));
        });
    }  

    $(document).ready(function(event) {
        WPGMZA.customFieldsPage = WPGMZA.CustomFieldsPage.createInstance();
    });
});