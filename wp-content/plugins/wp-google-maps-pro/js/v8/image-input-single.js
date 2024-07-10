/**
 * @namespace WPGMZA
 * @module ImageInputSingle
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
    WPGMZA.ImageInputSingle = function(element, options){
        if(!(element instanceof HTMLInputElement))
            throw new Error("Element is not an instance of HTMLInputElement");

        this.element = $(element);
        this.type = element.type;
        this.value = element.value;

        this.wrap();
        this.bindEvents();
        this.parseImage(this.value);
    }

    WPGMZA.extend(WPGMZA.ImageInputSingle, WPGMZA.EventDispatcher);

    WPGMZA.ImageInputSingle.createInstance = function(element) {
        return new WPGMZA.ImageInputSingle(element);
    }

    WPGMZA.ImageInputSingle.prototype.wrap = function(){
        var self = this;
        if(this.element && this.type === "text"){
            this.element.hide();
            this.container = $("<div class='wpgmza-image-single-input-wrapper' />");
            this.imageFrame = $("<div class='wpgmza-image-single-input-preview' />");

            this.image = $("<img />");
            this.placeholder = $("<div />");
            this.placeholder.append('<i class="fa fa-camera"></i>');

            const resetEnabled = this.element.data('image-reset');
            if(resetEnabled){
                this.resetButton = $('<span />');
                this.resetButton.append('<i class="fa fa-times"></i>');
                this.resetButton.addClass('wpgmza-image-single-input-reset');

                this.imageFrame.append(this.resetButton);
            }

            this.container.insertAfter(this.element);
            this.container.append(this.element);

            this.container.append(this.imageFrame);
        } else {
            throw new Error("WPGMZA.ImageInputSingle requires a text field as a base");
        }
    }

    WPGMZA.ImageInputSingle.prototype.bindEvents = function(){
        this.imageFrame.on('click', (event) => {
            this.onOpenMediaPicker();
        });

        this.element.on('change', (event) => {
            this.value = this.element.val();
            this.parseImage(this.value);
        });

        if(this.resetButton){
            this.resetButton.on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.onReset();
            });
        }
    }

    WPGMZA.ImageInputSingle.prototype.onOpenMediaPicker = function(){
        if(this.hasDependencies()){
            WPGMZA.openMediaDialog((mediaId, mediaUrl) => {
                if(mediaUrl){
                    this.value = mediaUrl;
                    this.commit();
                    this.parseImage(this.value);
                }
            });
        }
    }

    WPGMZA.ImageInputSingle.prototype.onReset = function(){
        this.value = '';
        this.commit();
    }

    WPGMZA.ImageInputSingle.prototype.parseImage = function(url){
        this.placeholder.remove();
        this.image.remove();

        if(this.resetButton){
            this.resetButton.hide();
        }

        if(url.trim().length > 0){
            this.image.on('load', () => {
                $(this.element).trigger('imagechange');
            });

            this.image.attr('src', url.trim());
            this.imageFrame.append(this.image);

            if(this.resetButton){
                this.resetButton.show();
            }
        } else{
            this.imageFrame.append(this.placeholder);
        }
    }
   
    WPGMZA.ImageInputSingle.prototype.commit = function(){
        this.element.val(this.value);
        this.element.trigger('change');
    }

    WPGMZA.ImageInputSingle.prototype.hasDependencies = function(){
        if(typeof wp !== 'undefined' && typeof wp.media !== 'undefined' && typeof WPGMZA.openMediaDialog !== 'undefined'){
            return true;
        }
        return false;
    }

    $(document.body).ready(function(){
        $("input.wpgmza-image-single-input").each(function(index, el) {
            el.wpgmzaImageInputSingle = WPGMZA.ImageInputSingle.createInstance(el);
        });
    });

});