/**
 * @namespace WPGMZA
 * @module ProGenericModal
 * @requires WPGMZA.GenericModal
 */
jQuery(function($) {
    WPGMZA.ProGenericModal = function(element, complete, cancel){
        WPGMZA.GenericModal.call(this, element, complete, cancel);

        this.initCategoryPickers();
    }

    WPGMZA.extend(WPGMZA.ProGenericModal, WPGMZA.GenericModal);

    WPGMZA.ProGenericModal.prototype.initCategoryPickers = function(){
        const self = this;

        this.categoryPickers = [];
        $(this.element).find(".wpgmza-category-picker").each(function(){
            const picker = new WPGMZA.CategoryPicker($(this));
            self.categoryPickers.push(picker);
        });
    }

});
