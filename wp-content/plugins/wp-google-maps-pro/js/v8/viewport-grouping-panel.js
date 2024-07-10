/**
 * @namespace WPGMZA
 * @module ViewportGroupingPanel
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.ViewportGroupingPanel = function(element){
        var self = this;

        WPGMZA.EventDispatcher.apply(this);

        this.element = $(element);
        this.parent = this.element.parent();
        this.toggleControl = this.parent.find('.grouping-handle');

        this.viewChain = [];

        this.findComponents();

        this.toggleControl.on('click', function(){
            self.toggle();
        });

        this.element.on('click', '.wpgmza-close', function(){
            self.showPreviousComponent();
        });

        this.showDefaultComponent();

        this.parent.addClass('viewport-grouping');
    }

    WPGMZA.extend(WPGMZA.ViewportGroupingPanel, WPGMZA.EventDispatcher);
    
    WPGMZA.ViewportGroupingPanel.createInstance = function(map) {
        return new WPGMZA.ViewportGroupingPanel(map);
    }

    WPGMZA.ViewportGroupingPanel.prototype.show = function(){
        if(this.toggleControl.hasClass('wpgmza-hidden')){
            this.toggleControl.removeClass('wpgmza-hidden');
        }

        this.element.addClass('visible');
        this.parent.addClass('expanded');

    }

    WPGMZA.ViewportGroupingPanel.prototype.hide = function(){
        this.element.removeClass('visible');
        this.parent.removeClass('expanded');
    }

    WPGMZA.ViewportGroupingPanel.prototype.toggle = function(){
        this.element.toggleClass('visible');
        this.parent.toggleClass('expanded');
    }

    WPGMZA.ViewportGroupingPanel.prototype.findComponents = function(){
        var self = this;

        this.componentCount = 0;

        this.components = {};
        this.defaultView = false;
        this.element.find('.grouping-item').each(function(){
            var tag = $(this).data('component');

            self.components[tag] = $(this);

            
            if(!self.defaultView){
                self.defaultView = tag;

                var requiresFeature = $(this).data('requires-feature');
                if(requiresFeature){
                    self.toggleControl.addClass('wpgmza-hidden');
                    $(this).find('.wpgmza-close').hide();
                }
            }
            
            self.componentCount++;
        });

        if(this.componentCount === 1){
            /* Holds only one component, we should hide/disable the back button as this will not do anything */
            this.element.find('.wpgmza-close').hide();
        } else if(this.components[this.defaultView].find('.wpgmza-close').length > 0){
            /* First view has a back button (pointless) */
            this.components[this.defaultView].find('.wpgmza-close').hide();
        }

    }

    WPGMZA.ViewportGroupingPanel.prototype.hasComponent = function(tag){
        if(this.components[tag]){
            return true;
        }
        return false;
    }


    WPGMZA.ViewportGroupingPanel.prototype.showComponent = function(tag, disableFocus){
        if(this.hasComponent(tag)){
            this.hideComponents();
            this.components[tag].show();

            if(this.defaultView === tag){
                this.viewChain = [];
            }

            if(this.viewChain.indexOf(tag) === -1){
                this.viewChain.push(tag);
            }
            
            if(!disableFocus){
                this.show();
            }
        }
    }

    WPGMZA.ViewportGroupingPanel.prototype.showPreviousComponent = function(){
        this.viewChain.pop();

        if(this.viewChain.length > 0){
            var previous = this.viewChain.length - 1;
            this.showComponent(this.viewChain[previous]);
        } else {
            this.showDefaultComponent();
        }
    }
    
    WPGMZA.ViewportGroupingPanel.prototype.hideComponents = function(){
        this.element.find('.grouping-item').hide();
    }

    WPGMZA.ViewportGroupingPanel.prototype.showDefaultComponent = function(){
        if(this.defaultView){
            this.showComponent(this.defaultView, true);
        }
    }
});