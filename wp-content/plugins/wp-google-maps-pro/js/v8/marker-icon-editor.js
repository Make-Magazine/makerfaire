/**
 * @namespace WPGMZA
 * @module MarkerIconEditor
 * @requires WPGMZA
 */
 jQuery(function($) {
    /**
     * Marker Icon Editor Class
     * 
     * @param Element element The container element
     */
	WPGMZA.MarkerIconEditor = function(element) {
        this.renderMode = false;

        this.source = false;
        this.layerModeState = false;

        this.container = $(element);

        this.setRenderMode(WPGMZA.MarkerIconEditor.RENDER_MODE_SHAPE);

        this.findElements();
        this.bindEvents();
    }

    /* Constants */
    WPGMZA.MarkerIconEditor.RENDER_MODE_SHAPE = 'shape';
    WPGMZA.MarkerIconEditor.RENDER_MODE_IMAGE = 'image';

    /**
     * Find the local elements within the container which control editor behaviour
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.findElements = function(){
        this.preview = this.container.find('.wpgmza-marker-icon-editor-preview canvas');
        this.previewBackdrop = this.container.find('.wpgmza-marker-icon-editor-preview');

        this.previewTileSelectors = this.container.find('.wpgmza-marker-icon-editor-preview-style');
        
        this.templates = this.container.find('.wpgmza-marker-icon-editor-templates .shape-template');
        this.icons = this.container.find('.wpgmza-marker-icon-editor-list .base-icon');

        this.templatesContainer = this.container.find('.wpgmza-marker-icon-editor-templates');
        this.iconsContainer = this.container.find('.wpgmza-marker-icon-editor-list');

        this.shapeControls = this.container.find('.wpgmza-icon-shape-control-wrapper input, .wpgmza-icon-shape-control-wrapper select');

        this.resolutionControl = this.container.find('.wpgmza-icon-output-resolution select');

        this.layerMode = this.container.find('.wpgmza-icon-layer-mode-wrapper select');
        this.layerControls = this.container.find('.wpgmza-icon-layer-control[data-mode]');
        this.layerInputs = this.container.find('.wpgmza-icon-layer-control input');

        // this.effectMode = this.container.find('.wpgmza-icon-effect-mode-wrapper select');
        
        this.effectControls = this.container.find('.wpgmza-marker-icon-editor-controls .wpgmza-icon-effect-mode-sliders input[type="range"]');

        this.actions = this.container.find('.wpgmza-marker-icon-editor-actions .wpgmza-button');

        //this.historyToggle = this.container.find('.wpgmza-marker-icon-editor-history-toggle');
        this.historyController = this.container.find('[data-history-control]');

        this.switchModeToggles = this.container.find('[data-switch-mode]');

        this.contentToggles = this.container.find('*[data-content-toggle-trigger]');

        this.sliderStacks = this.container.find('.wpgmza-icon-shape-slider-stack');
        this.sliderLockToggles = this.container.find('span[data-lock-trigger]');
        this.sliderLocks = this.container.find('input[type="range"][data-lock]');

        this.setRestoreState();
        this.setBinding(false);

        /* Init Font Awesome Picker */
        this.container.find(".icon-picker").each((index, element) => {
            element.wpgmzaFaPicker = new WPGMZA.FontAwesomeIconPickerField(element);
        });
    }

    /**
     * Bind events from local elements (and some globals) to trigger class specific methods 
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.bindEvents = function(){
        /* Local methods */
        this.templates.on('click', (event) => {
            event.preventDefault();
            this.onClickTemplate(event.currentTarget);
        });

        this.shapeControls.on('change input', (event) => {
            event.preventDefault();
            this.updatePreview();
        });

        this.icons.on('click', (event) => {
            event.preventDefault();
            this.onClickIcon(event.currentTarget);
        });

        this.actions.on('click', (event) => {
            event.preventDefault();
            this.onClickAction(event.currentTarget);
        });

        this.layerMode.on('change', (event) => {
            event.preventDefault();
            this.onChangeLayerMode(event.currentTarget);
            this.updatePreview();
        });

        this.layerInputs.on('change input', (event) => {
            event.preventDefault();
            this.updatePreview();
        });

        this.effectControls.on('change input', (event) => {
            event.preventDefault();
            this.updatePreview();
        });

        this.historyController.each((index, element) => {
            const controller = $(element);

            const target = controller.data('history-control');
            if(!this.container.find(`.${target}[data-history-control-listener]`).hasClass('has-history')){
                controller.hide();
            }

            controller.on('change', (event) => {
                event.preventDefault();
                if(controller.val() === 'storage'){
                    this.container.find(`.${target}[data-history-control-listener]`).attr('data-history', 'true');
                } else {
                    this.container.find(`.${target}[data-history-control-listener]`).removeAttr('data-history');
                }
            });
        });

        this.switchModeToggles.on('click', (event) => {
            event.preventDefault();
            const mode = $(event.currentTarget).attr('data-switch-mode');
            this.setRenderMode(mode);

            this.redraw();

            if(this.renderMode === WPGMZA.MarkerIconEditor.RENDER_MODE_SHAPE){
                this.templates.first().trigger('click');
            } else {
                this.icons.first().trigger('click');
            }

        });


        this.contentToggles.on('change', (event) => {
            event.preventDefault();
            this.onChangeContentToggle(event.currentTarget);
        });

        /* Stacked slider component relay, this allows change events to come from inputs as well
         * This might be slightly less efficient, we could integrate this on the main controls instead, but it's a low impact change
         * 
         * We might rewire this later, just to clean up this code base. This is a results drive, so we aren't concerned by this for now 
        */
        this.sliderStacks.each((index, element) => {
            let slider = $(element).find('input[type="range"]');
            if(slider.length){
                const linkedInput = $(element).find('input[data-linked]');
                const suffixContainer = $(element).find('span[data-suffix]');
                
                if(slider.data('suffix')){
                    suffixContainer.text(slider.data('suffix'));
                }

                linkedInput.on('input', (event) => {
                    this.onSliderStackInputChange(slider, linkedInput);
                });

                linkedInput.on('keydown', (event) => {
                    if(event.originalEvent instanceof KeyboardEvent){
                        const captureEvents = ["ArrowUp", "ArrowDown"];
                        if(captureEvents.indexOf(event.originalEvent.code) !== -1){
                            event.preventDefault();
                            let value = parseInt(slider.val());
                            value = event.originalEvent.code.indexOf('Up') !== -1 ? (value + 1) : (value - 1);

                            linkedInput.val(value).trigger('input');
                        } 
                    }
                })

                linkedInput.on('click', (event) => {
                    linkedInput.select();
                });

                linkedInput.on('focusout', (event) => {
                    if(!linkedInput.val().trim().length){
                        linkedInput.val(slider.val()).trigger('input');
                    }
                });
                
                slider.on('change input', (event) => {
                    /* Relay in real time */
                    linkedInput.val(slider.val());
                });

                linkedInput.val(slider.val());
            }
        });

        this.sliderLockToggles.on('click', (event) => {
            event.preventDefault();
            const locked = $(event.target).attr('data-locked');
            if(locked){
                $(event.target).removeAttr('data-locked');
            } else {
                $(event.target).attr('data-locked', 'true');
            }
        });

        this.sliderLocks.each((index, element) => {
            const slider = $(element);
            const condition = slider.data('lock');

            slider.on('input', () => {
                const shouldLock = this.container.find(`span[data-lock-trigger="${condition}"]`);
                if(shouldLock.attr('data-locked')){
                    /* Locked at the moment, bubble to linked sliders */
                    const control = slider.data('control');
                    const value = slider.val();

                    this.container.find(`input[type="range"][data-lock="${condition}"][data-control!="${control}"]`).val(value).trigger('change');
                }
            });
        });

        this.previewTileSelectors.on('click', (event) => {
            const style = $(event.target).attr('data-style');
            this.previewBackdrop.attr('data-style', style);
        });

        /* Global methods */
        $(document.body).on('click', '.wpgmza-marker-library', (event) => {
            event.preventDefault();
            this.onBindInput(event.currentTarget);
        });

        $(document.body).find('.wpgmza-editor .sidebar .grouping').on('grouping-opened', (event) => {
            if(this.isVisible()){
                this.hide();
            }
        });

        $(window).on('resize', (event) => {
            if(this.isVisible()){
                this.autoPlace();
            }
        });

    }

    /**
     * On click template
     * 
     * @param object context The context that triggered the event, usually current target from a click event
     * 
     * @return void 
     */
    WPGMZA.MarkerIconEditor.prototype.onClickTemplate = function(context){
        if(context instanceof HTMLElement){
            const template = $(context);
            const config = template.data('template');
            const type = template.data('type');

            if(config instanceof Object){
                this.importTemplate(config, type);
            }
        }
    }

    /**
     * On Click Icon 
     * 
     * @param object context The context that triggered the event, usually current target from a click event
     * 
     * @return void 
    */
    WPGMZA.MarkerIconEditor.prototype.onClickIcon = function(context){
        if(context instanceof HTMLElement){
            const icon = $(context);
            const source = icon.data('src');
            if(source){
                this.container.find('.wpgmza-marker-icon-editor-list .base-icon.selected').removeClass('selected');
                icon.addClass('selected');
                this.setIcon(source);
            }
        }
    }

    /**
     * On Click Action (button)
     * 
     * @param object context The context that triggered the event, usually current target from a click event
     * 
     * @return void 
     */
    WPGMZA.MarkerIconEditor.prototype.onClickAction = function(context){
        if(context instanceof HTMLElement){
            const button = $(context);
            const action = button.data('action');
            if(action){
                switch(action){
                    case 'use':
                        this.saveIcon();
                        break;
                    default:
                        this.hide();
                        break;
                }
            }
        }
    }

    /**
     * On Click Tab (button)
     * 
     * @param object context The context that triggered the event, usually the current target from the click event 
     * 
     * @return void 
     */
    WPGMZA.MarkerIconEditor.prototype.onClickTab = function(context){
        if(context instanceof HTMLElement){
            const element = $(context);
            const tab = element.data('tab');
            if(tab){
                element.addClass('active');
                
                this.container.find('.wpgmza-marker-icon-editor-tab').removeClass('active');
                this.container.find('.wpgmza-marker-icon-editor-tab[data-tab="' + tab + '"]').addClass('active');
            }
        }
    }

    /**
     * On Bind input
     * 
     * Binds the editor to a specific marker icon picker, this is done by hooking into clicks on the 'library' button present in all pickers 
     * 
     * Made more robust here so that we can trigger it in other use cases as well if needed
     * 
     * @param object context The context that triggered the event, usually current target from a click event
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.onBindInput = function(context){
        if(context instanceof HTMLElement){
            this.setBinding(context);
            this.show();
        }
    }

    /**
     * On change layer mode
     * 
     * @param object context The context that triggered this event, usually current target from change event 
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.onChangeLayerMode = function(context){
        if(context instanceof HTMLElement){
            const mode = $(context).val();
            if(mode !== this.layerModeState){
                this.layerModeState = mode;

                this.layerControls.removeClass('active');
                this.container.find('.wpgmza-icon-layer-control[data-mode="' + this.layerModeState + '"]').addClass('active');
            }
        }
    }

    /**
     * On Change Effect mode
     * 
     * @param object context The context that triggered the event, usually the current target from the change event 
     */
    WPGMZA.MarkerIconEditor.prototype.onChangeEffectMode = function(context){
        if(context instanceof HTMLElement){
            const mode = $(context).val();
            this.effectControls.removeClass('active');
            this.container.find('.wpgmza-marker-icon-editor-controls input[type="range"][data-control="' + mode + '"]').addClass('active');
        }
    }

    /**
     * On Change content toggle
     * 
     * Acts as a one-size-fits-all mechanism for conditionally showing content for a specific condition, driven by user
     * 
     * @param Element context 
     */
    WPGMZA.MarkerIconEditor.prototype.onChangeContentToggle = function(context){
        if(context instanceof HTMLElement){
            const trigger = $(context).data('content-toggle-trigger');
            let condition = $(context).val();

            this.container.find(`*[data-content-toggle-listener="${trigger}"]`).hide();
            this.container.find(`*[data-content-toggle-listener="${trigger}"][data-content-toggle-condition*="${condition}"]`).show();
        }
    }

    /**
     * Bubbles slider stack input field changes, directly into the slider, triggering a series
     * of events which control rendering. 
     * 
     * Again, there's some redundance here, with multiple change listeners, but that is for a later refactor 
     * 
     * @param Element slider 
     * @param Element value 
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.onSliderStackInputChange = function(slider, input){
        if(slider && input){
            let value = input.val();

            if(value){
                const limits = {
                    min : parseInt(slider.attr('min')),
                    max : parseInt(slider.attr('max'))
                };

                value = parseInt(value);

                if(value < limits.min){
                    value = limits.min;
                } else if (value > limits.max){
                    value = limits.max;
                }

                input.val(value);

                slider.val(value).trigger('change');
                
            }
        }
    }

    /**
     * Set the binding element where marker icons should be applied
     * 
     * @param Element element The element root for the binding, set to false to undbind
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.setBinding = function(element){
        if(element !== this.binding){
            /* Indicates a change in binding - Mark editor dirty, this will reinit some modules later */
            this.dirty = true;
        }

        this.binding = element;
    }

    /**
     * Update the current drawing mode, this alsso updates the editor to show/hide controls
     * 
     * @param string mode Render mode to set the system to
     * 
     * @return void 
     */
    WPGMZA.MarkerIconEditor.prototype.setRenderMode = function(mode){
        this.renderMode = mode;

        this.container.data('render-mode', this.renderMode);

        this.container.find('*[data-render-mode]').each((index, element) => {
            if($(element).data('render-mode') === this.renderMode){
                $(element).show();
            } else {
                $(element).hide();
            }
        });
    }

    /**
     * Caches some of the controls default values to be restored when the editor is marked as dirty
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.setRestoreState = function(){
        this.layerInputs.each((index, element) => {
            $(element).attr('data-restore', $(element).val());
        });

        this.effectControls.each((index, element) => {
            $(element).attr('data-restore', $(element).val());
        });
    }

    /**
     * Set the current editor base icon
     * 
     * This will trigger canvas redraws and initialization
     * 
     * @param string source The URL to the marker icon base 
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.setIcon = function(source){
        source = source ? source : this.getDefaultIcon();
        if(source !== this.source){
            this.source = source;
            this.setRenderMode(WPGMZA.MarkerIconEditor.RENDER_MODE_IMAGE);
            this.redraw();
        }
    }

    /**
     * Get the default icon, this is done by walking the system icons for the first child
     * 
     * @return string
     */
    WPGMZA.MarkerIconEditor.prototype.getDefaultIcon = function(){
        let icon = this.container.find('.wpgmza-marker-icon-editor-list *[data-type="system"]');
        if(icon.length){
            icon = $(icon.get(0));
            const source = icon.data('src');
            if(source){
                return source;
            } 
        }

        return false;
    }
    
    /**
     * Get a reference to the DOM Element based on the current data source
     * 
     * @returns Element
     */
    WPGMZA.MarkerIconEditor.prototype.getSourceElement = function(){
        if(this.source){
            const image = this.container.find('.wpgmza-marker-icon-editor-list *[data-src="' + this.source + '"] img');
            if(image.length){
                return image.get(0);
            }
        }
        return false;
    }

    /**
     * Returns the render dimensions
     * 
     * These are not necessarily the storage dimensions, but it is visualization 
     * 
     * If you are in image mode, the image dimensions are used instead, sorry 
     * 
     * @returns object
     */
    WPGMZA.MarkerIconEditor.prototype.getRenderDimensions = function(){
        if(this.renderMode === WPGMZA.MarkerIconEditor.RENDER_MODE_IMAGE){
            return this.getSourceDimensions();
        }

        /* Super Retina Started */
        return {
            width : 110,
            height : 110
        };
    }

    /**
     * Get the natural dimensions of the source
     * 
     * This is done by finding the source element in the dom and leveraging the natural width/height properties to get an accurate resolution
     * 
     * @returns object
     */
    WPGMZA.MarkerIconEditor.prototype.getSourceDimensions = function(){
        const dimensions = {
            width : 27,
            height: 43
        };

        if(this.source){
            const imageElement = this.getSourceElement();
            if(imageElement){
                dimensions.width = imageElement.naturalWidth;
                dimensions.height = imageElement.naturalHeight;
            }
        }

        return dimensions;
    }

    /**
     * Get storage dimensions 
     * 
     * @param string preset 
     * 
     * @return object
     */
    WPGMZA.MarkerIconEditor.prototype.getOutputDimensions = function(preset){
        const dimensions = this.getRenderDimensions();

        let multiplier = 1;
        switch(preset){
            case 'default':
                multiplier = 0.45;
                break;
            case 'retina':
                multiplier = 0.9;
                break;
        }

        for(let axis in dimensions){
            dimensions[axis] = parseInt(dimensions[axis] * multiplier);
        }

        return dimensions;
    }

    /**
     * Compiles all active editor controls into a single CSS filter which can be applied to the editor 
     * 
     * @returns string
     */
    WPGMZA.MarkerIconEditor.prototype.getFilters = function(){
        let filters = [];
        
        this.effectControls.each((index, elem) => {
            const input = $(elem);
            const filter = input.data('control');
            const suffix = input.data('suffix');
            if(filter){
                let compiled = filter.trim() + "(" + input.val().trim() + (suffix ? suffix.trim() : "") + ")";
                filters.push(compiled); 
            }
        });

        return filters.length ? filters.join(" ") : "";
    }

    /**
     * Compiles the shape configuration for the renderer
     * 
     * These are driven by controls within the dom 
     * 
     * @return object
     */
    WPGMZA.MarkerIconEditor.prototype.getShapeParams = function(unfiltered){
        unfiltered = typeof unfiltered !== "undefined" ? true : false;

        let config = {};
        this.shapeControls.each((index, elem) => {
            const input = $(elem);
            const control = input.data('control');
            const shape = input.data('shape');
            const type = input.data('type');

            let value = input.val();
            switch(type){
                case 'percentage':
                    value = unfiltered ? parseInt(value) : (parseInt(value) / 100);
                    break;
                case 'pixel':
                case 'deg':
                    value = parseInt(value);
                    break;
                
            }

            if(control){
                if(shape){
                    /* Shape specific param */
                    if(typeof config[shape] === 'undefined'){
                        config[shape] = {};
                    }
    
                    config[shape][control] = value;
                } else {
                    /* Generic param */
                    config[control] = value;
                }
            }
        });

        return config;
    }

    /**
     * Prepares the controls in the editor
     * 
     * This sets up defaults, restores values, and select the first icon
     * 
     * Usually, this is called after the editor is marked as dirty
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.prepareControls = function(){
        this.container.removeClass('view-history');

        this.layerInputs.each((index, element) => {
            const restore = $(element).data('restore');
            
            let fallback = "";
            const type = $(element).attr('type');
            switch(type){
                case 'number':
                    fallback = 0;
                    break;
                case 'checkbox':
                    $(element).prop('checked', false).trigger('change');
                    break;
            }

            if(type === 'checkbox'){
                return;
            }

            $(element).val(restore ? restore : fallback);
            $(element).trigger('change');
        });

        this.effectControls.each((index, element) => {
            const restore = $(element).data('restore');
            $(element).val(restore ? restore : 0);
            $(element).trigger('change');
        });

        this.templates.first().trigger('click');

        this.layerMode.val('text');
        this.layerMode.trigger('change');

        this.contentToggles.trigger('change');
        this.resolutionControl.trigger('change');
    }

    /**
     * Chain all the events for redrawing the canvas
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.redraw = function(){
        this.prepareCanvas();
        this.updatePreview();
    }

    /**
     * Prepare the canvas based on the current base icon
     * 
     * This loads the base canvas params, like widths etc, but does not actually handle drawing the layers in the canvas directly
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.prepareCanvas = function(){
        const canvas = this.preview.get(0);
        const dimensions = this.getRenderDimensions();

        canvas.width = dimensions.width;
        canvas.height = dimensions.height;
    }

    /**
     * Update the preview 
     * 
     * This will access the canvas, issue draw commands, and render accordingly using all layers
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.updatePreview = function(){
        const canvas = this.preview.get(0);
        const context = canvas.getContext("2d");
        
        context.clearRect(0, 0, canvas.width, canvas.height);
        switch(this.renderMode){
            case WPGMZA.MarkerIconEditor.RENDER_MODE_SHAPE:
                this.renderShape(canvas, context);
                this.renderOverlay(canvas, context);
                break;
            case WPGMZA.MarkerIconEditor.RENDER_MODE_IMAGE:
                if(this.source){
                    if(!this.imageData || this.imageData.src !== this.source){
                        this.imageData = new Image();
        
                        /* Link onload to the refresh method */
                        this.imageData.onload = () => {
                            this.renderImage(canvas, context);
                            this.renderOverlay(canvas, context);
                        };
        
                        this.imageData.src = this.source;
                    } else {
                        /* Refresh without reloading the image */
                        this.renderImage(canvas, context);
                        this.renderOverlay(canvas, context);
                    }
                }
                break;
        }
    }

    /**
     * Render the shape layers
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.renderShape = function(canvas, context){
        /* Get live params from the controllers */
        const params = this.getShapeParams();
        if(!params.body || !params.tail) {
            return;
        }

        /* Append padding for outer-edge effects, like shadow/stroke */
        params.padding = 30;

        /* Generate all the points needed for pathing, based on params */
        const compiled = this.compileShapeData(canvas, params);


        /* Draw all the paths needed to make up the icon */
        context.beginPath();

        /* Tail pathing */
        context.moveTo(compiled.tail.points.top.left.x, compiled.tail.points.top.left.y);
        context.lineTo(compiled.tail.points.top.right.x, compiled.tail.points.top.right.y);
        context.lineTo(compiled.tail.points.bottom.right.x, compiled.tail.points.bottom.right.y);
        context.quadraticCurveTo(compiled.tail.points.anchor.x, compiled.tail.points.anchor.y, compiled.tail.points.bottom.left.x, compiled.tail.points.bottom.left.y);
        context.lineTo(compiled.tail.points.top.left.x, compiled.tail.points.top.left.y);

        /* Body pathing */
        context.roundRect(compiled.body.position.x, compiled.body.position.y, compiled.body.width, compiled.body.height, compiled.body.roundness);

        context.closePath();


        /* Apply filters */
        const filters = this.getFilters();
        if(filters){
            context.filter = filters;
        }

        /* Set the draw color */
        context.fillStyle = params.color;
        if(params.colorMode){
            switch(params.colorMode){
                case 'gradient-linear':
                    /* Todo: Add an angle support */

                    const gradientCenter = {
                        x : canvas.width / 2,
                        y : canvas.height / 2 
                    };

                    const gradientAngle = params.colorGradientAngle ? params.colorGradientAngle : 0;
                    const gradientRad = gradientAngle * Math.PI / 180;
                    
                    const gradientPoints = {
                        start : {
                            x : gradientCenter.x + ((canvas.width / 2) * Math.cos(gradientRad)),
                            y : gradientCenter.y - ((canvas.width / 2) * Math.sin(gradientRad))
                        },
                        end : {
                            x : gradientCenter.x - ((canvas.width / 2) * Math.cos(gradientRad)),
                            y : gradientCenter.y + ((canvas.width / 2) * Math.sin(gradientRad))
                        }
                    }

                    const linearGradientFill = context.createLinearGradient(gradientPoints.start.x, gradientPoints.start.y, gradientPoints.end.x, gradientPoints.end.y);
                    linearGradientFill.addColorStop(0, params.colorGradientA);
                    linearGradientFill.addColorStop(1, params.colorGradientB);

                    context.fillStyle = linearGradientFill;
                    break;
                case 'gradient-radial':
                    /* Todo: Add an angle support */
                    const radialGradientFill = context.createRadialGradient(canvas.width / 2, canvas.height / 2, 1, canvas.width / 2, canvas.height / 2, canvas.width - params.padding);
                    radialGradientFill.addColorStop(0, params.colorGradientA);
                    radialGradientFill.addColorStop(1, params.colorGradientB);

                    context.fillStyle = radialGradientFill;
                    break;
            }
        }

        
        /* Shadows */
        if(params.shadowBlur && params.shadowColor){
            context.shadowBlur = params.shadowBlur;
            context.shadowColor = params.shadowColor;
            context.fill();

            context.globalCompositeOperation = "destination-out";
            context.fill();
            context.globalCompositeOperation = "source-over";

            context.shadowBlur = false;
        }
        
        /* Stroke */
        if(params.strokeWeight && params.strokeColor){
            context.strokeStyle = params.strokeColor;
            context.lineWidth = params.strokeWeight;
            context.stroke();

            context.globalCompositeOperation = "destination-out";
            context.fill();
            context.globalCompositeOperation = "source-over";
        }


        /* Fill it all now */
        context.fill();
    }

    /**
     * Renders the image layer
     * 
     * This is triggered by the image state, from the updatePreview method, instead of using a nested callback
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.renderImage = function(canvas, context){
        if(this.imageData){
            const filters = this.getFilters();
            if(filters){
                context.filter = filters;
            }
            context.drawImage(this.imageData, 0, 0);
        }
    }

    /**
     * Renders the overlay layer 
     * 
     * This is triggered after renderImage in most cases, to ensure the layer is drawn above the image
     *  
     * @param Element canvas The canvase element
     * @param Context context The canvas context
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.renderOverlay = function(canvas, context){
        const layerOptions = {};
        
        this.layerInputs.each((index, input) => {
            const control = $(input).data('control');
            if(control){
                if($(input).attr('type') === "checkbox"){
                    layerOptions[control] = $(input).prop('checked');
                } else {
                    layerOptions[control] = $(input).val();
                }
            }
        });

        layerOptions.size = (layerOptions.size ? parseInt(layerOptions.size) : 20);

        const position = {
            x : (canvas.width / 2),
            y : (canvas.width / 2)
        };

        if(layerOptions.xOffset){
            position.x += parseInt(layerOptions.xOffset);
        }

        if(layerOptions.yOffset){
            position.y += parseInt(layerOptions.yOffset);
        }

        switch(this.layerModeState){
            case 'text':
                if(layerOptions.content.trim().length){
                    context.textAlign = "center";
                    context.textBaseline = "middle";
                    context.font = layerOptions.size + "px sans-serif";

                    context.fillStyle = layerOptions.overlayColor ? layerOptions.overlayColor : "#FFFFFF";

                    context.fillText(layerOptions.content.trim(), position.x, position.y);
                }
                break;
            case 'icon':
                if(layerOptions.icon.trim().length){
                    const input = this.container.find('.icon-picker').get(0);
                    if(input && input.wpgmzaFaPicker){
                        const icons = input.wpgmzaFaPicker.getIcons();
                        if(icons.indexOf(layerOptions.icon.trim()) !== -1){
                            const faSlug = layerOptions.icon.trim();

                            /* Create a sampler element, so we can get the unicode or the icon */
                            const sampler = $('<i/>');
                            sampler.addClass(faSlug);

                            /* Append it to the DOM to be rendered */
                            this.container.append(sampler);

                            /* Get a raw reference for vanilla JS */
                            const ref = sampler.get(0);
                            let styles = window.getComputedStyle(ref,':before');

                            if(styles.content){
                                /* Pull the unicode from the before psuedo */
                                const content = styles.content.replaceAll('"', "");
                                
                                /* Get font name, to allow for FA 4 and 5 */
                                styles = window.getComputedStyle(ref);
                                context.textAlign = "center";
                                context.textBaseline = "middle";
                                context.font = styles.fontWeight + " " + layerOptions.size + "px " + styles.fontFamily;

                                context.fillStyle = layerOptions.overlayColor ? layerOptions.overlayColor : "#FFFFFF";

                                /* Write to canvas */
                                context.fillText(content, position.x, position.y);
                            }

                            /* Remove it from the dom to keep it clean */
                            sampler.remove();
                        }
                    }
                }
                break;
        }
    }

    /**
     * Compiled the various parts of the shape data, points specifically, that are needed 
     * to generate canvas shapes 
     * 
     * This is modularized to allow us to later, support additional layers, or even dynamically created/controlled layers if needed
     * 
     * @return object
     */
    WPGMZA.MarkerIconEditor.prototype.compileShapeData = function(canvas, params){
        const compiled = {};

        /* Body Compilation */
        compiled.body = {};

        /* Body - Width, Height, Roundness */
        compiled.body.width = params.body.width * (canvas.width - params.padding);
        compiled.body.height = params.body.height * (canvas.width - params.padding);
        compiled.body.roundness = params.body.roundness * (compiled.body.width / 2);

       
        /* Body Position */
        compiled.body.position = {
            x : ((canvas.width / 2) - (compiled.body.width / 2)),
            y : params.padding / 2
        };

        /* 
         * Note: We could add position nudging, but this would require blank space on the opposing side of the canvas
         * This in turn, would result in unexpected hitbox triggers, so for the moment, we'll leave this alone. 
         * 
         * A more complex solution is to allow the icon creator to alter the anchor point for the marker icon, this might be better long term anyways 
        */

        /* Tail Compilation */
        compiled.tail = {};

        /* Tail - Thickness, Length, Roundess */
        compiled.tail.thickness = params.tail.thickness * compiled.body.width;
        compiled.tail.length = params.tail.length * (canvas.height - (compiled.body.height + (params.padding / 2)));
        compiled.tail.roundness = params.tail.roundness * (compiled.tail.thickness / 2);

        /* Tail - Points - Anchor */
        compiled.tail.points = {
            anchor : {
                x : canvas.width / 2,
                y : (params.padding / 2) + compiled.body.height + compiled.tail.length
            }
        };

        /* Tail - Points - Top */
        compiled.tail.points.top = {
            left : { 
                x : compiled.tail.points.anchor.x - (compiled.tail.thickness / 2),
                y : ((compiled.body.height + params.padding) / 2)
            },
            right : { 
                x : compiled.tail.points.anchor.x + (compiled.tail.thickness / 2),
                y : ((compiled.body.height + params.padding) / 2)
            }
        }

        /* Tail - Points - Top - Stroke correction */
        if(params.strokeWeight){
            compiled.tail.points.top.right.x -= params.strokeWeight / 2;
            compiled.tail.points.top.left.x += params.strokeWeight / 2;
        }

        /* Tail - Points - Bottom */
        compiled.tail.points.bottom = {
            left : { 
                x : compiled.tail.points.anchor.x - ((compiled.tail.roundness / 2) / 2),
                y : compiled.tail.points.anchor.y - ((compiled.tail.roundness / 2) / 2)
            },
            right : { 
                x : compiled.tail.points.anchor.x + ((compiled.tail.roundness / 2) / 2),
                y : compiled.tail.points.anchor.y - ((compiled.tail.roundness / 2) / 2)
            }
        }

        /* Tail - Point - Bottom, Angle Correction */
        if(compiled.tail.points.bottom.left.x < compiled.tail.points.top.left.x){
            compiled.tail.points.bottom.left.x = compiled.tail.points.top.left.x;
        }

        if(compiled.tail.points.bottom.right.x > compiled.tail.points.top.right.x){
            compiled.tail.points.bottom.right.x = compiled.tail.points.top.right.x;
        }

        return compiled;
    }

    /**
     * Save the icon
     * 
     * Uploads created icon to WP Media, then calls the apply method to update the DOM fully 
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.saveIcon = function(){
        this.setSaving(true);

        const canvas = this.preview.get(0);
        let imageData = canvas.toDataURL();
        if(imageData){
            if(this.renderMode === WPGMZA.MarkerIconEditor.RENDER_MODE_SHAPE){
                const resolutionPreset = this.resolutionControl.val();
                const resolution = this.getOutputDimensions(resolutionPreset);

                this.resizeImage(imageData, resolution).then((resizedData) => {
                    this.trimImage(resizedData, resolution).then((trimmedData) => {
                        /* Now upload */
                        this.uploadIcon(trimmedData).then((data) => {
                            this.setSaving(false);
                            if(data.url){
                                this.applyIcon(data.url);
                            }
                        }).catch(() => {
                            this.setSaving(false);
                        });
                    }).catch(() => {
                        this.setSaving(false);
                    });

                    
                }).catch(() => {
                    this.setSaving(false);
                });;

                return;
            } else {
                /* No post processing */
                this.uploadIcon(imageData).then((data) => {
                    this.setSaving(false);
                    if(data.url){
                        this.applyIcon(data.url);
                    }
                }).catch(() => {
                    this.setSaving(false);
                });
            }

            
        } else {
            this.setSaving(false);
        }
    }

    /**
     * Upload the icon, once stored, the system can continue with additional processing 
     * 
     * @param string imageData 
     * 
     * @return Promise
     */
    WPGMZA.MarkerIconEditor.prototype.uploadIcon = function(imageData){
        return new Promise((resolve, reject) => {
            const template = this.exportTemplate();

            $.ajax({
				url  : WPGMZA.ajaxurl,
				type : "POST",
				data : {
					action   : "wpgmza_upload_base64_image",
					security : WPGMZA.legacyajaxnonce,
					data     : imageData.replace(/^data:.+?base64,/, ''),
                    folder   : 'wp-google-maps/icons', 
                    template : template ? JSON.stringify(template) : false,
					mimeType : "image/png"
				},
				success : (data) => {
                    resolve(data);
				},
                error : () => {
                    reject();
                }
			});
        });
    }

    /**
     * Resize to a target resolution, from image data
     * 
     * Pre save is the time to do this, likely only for shapes
     * 
     * @param string imageData 
     * @param object resolution 
     * 
     * @return string
     */
    WPGMZA.MarkerIconEditor.prototype.resizeImage = function(imageData, resolution) {
        return new Promise((resolve, reject) => {
            const shadowCanvas = document.createElement('canvas');
            const shadowContext = shadowCanvas.getContext('2d');
    
            shadowCanvas.width = resolution.width;
            shadowCanvas.height = resolution.height;
    
            const shadowImage = new Image();
            shadowImage.onload = () => {
                shadowContext.drawImage(shadowImage, 0, 0, resolution.width, resolution.height);
                let shadowData = shadowCanvas.toDataURL();
                resolve(shadowData);
            };

            shadowImage.onerror = () => {
                reject();
            };

            shadowImage.onabort = () => {
                reject();
            }
            
            shadowImage.src = imageData;
        });
    }

    /**
     * Trim the surrounding canvas data to only includ the actual icon, not any transparent data
     * 
     * @param string imageData 
     * @param object resolution 
     * 
     * @return string
     */
    WPGMZA.MarkerIconEditor.prototype.trimImage = function(imageData, resolution) {
        return new Promise((resolve, reject) => {
            const shadowCanvas = document.createElement('canvas');
            const shadowContext = shadowCanvas.getContext('2d');
    
            shadowCanvas.width = resolution.width;
            shadowCanvas.height = resolution.height;
    
            const shadowImage = new Image();
            shadowImage.onload = () => {
                shadowContext.drawImage(shadowImage, 0, 0, resolution.width, resolution.height);

                const pixels = {
                    x : [],
                    y : []
                };

                const data = shadowContext.getImageData(0, 0, shadowCanvas.width, shadowCanvas.height);

                for(let y = 0; y < resolution.height; y++){
                    for(let x = 0; x < resolution.width; x++){
                        let index = (y * resolution.width + x) * 4;
                        if (data.data[index + 3] > 0) {
                            pixels.x.push(x);
                            pixels.y.push(y);
                        } 
                    }
                }

                pixels.x.sort((a,b) => { return a - b; });
                pixels.y.sort((a,b) => { return a - b; });

                const n  = pixels.x.length - 1;
                const trimmedResolution = {
                    width : 1 + pixels.x[n] - pixels.x[0],
                    height : 1 + pixels.y[n] - pixels.y[0],
                };

                const trimmed = shadowContext.getImageData(pixels.x[0], pixels.y[0], trimmedResolution.width, trimmedResolution.height);

                shadowCanvas.width = trimmedResolution.width;
                shadowCanvas.height = trimmedResolution.height;

                shadowContext.putImageData(trimmed, 0, 0);
                
                let shadowData = shadowCanvas.toDataURL();
                resolve(shadowData);
            };

            shadowImage.onerror = () => {
                reject();
            };

            shadowImage.onabort = () => {
                reject();
            }
            
            shadowImage.src = imageData;
        });
    }

    /**
     * Apply the stored icon to the DOM, or rather, the marker picker module
     * 
     * @param string url The stored URL to the marker icon 
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.applyIcon = function(url){
        if(url && this.binding){
            this.hide(); //Hide but don't unbind, incase the user wants to make last minute changes

            const button = $(this.binding);
            const input = button.closest(".wpgmza-marker-icon-picker").find(".wpgmza-marker-icon-url");
            const preview = button.closest(".wpgmza-marker-icon-picker").find("img, .wpgmza-marker-icon-preview");

            if(preview.prop('tagName').match(/img/)){
                /* It's a standard image */
                preview.attr('src', url);
            } else {
                /* Its a background image element */
                preview.css("background-image", "url(" + url + ")");
            }

            input.val(url).trigger('change');
        }
    }

    /**
     * Show the editor
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.show = function(){
        this.setVisible(true);

        this.autoPlace();

        if(this.dirty){
            this.dirty = false;
            this.prepareControls();
        }
    }

    /**
     * Hide the editor
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.hide = function(){
        this.setVisible(false);
    }

    /**
     * Set the visibility of the editor
     * 
     * @param bool visible Visibility start 
     */
    WPGMZA.MarkerIconEditor.prototype.setVisible = function(visible){
        if(visible && this.binding){
            this.container.addClass('open');
        } else {
            this.container.removeClass('open');
        }
    }

    /**
     * Get the visibility of the editor
     * 
     * @returns bool
     */
    WPGMZA.MarkerIconEditor.prototype.isVisible = function(){
        return this.container.hasClass('open');
    } 

    /**
     * Set the current saving state of the editor
     * 
     * This effectively disables controls while uploading/storing the generated marker
     * 
     * @param bool busy If the system is currently saving
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.setSaving = function(busy){
        this.actions.each((index, button) => {
            button = $(button);

            if(busy){
                const busyText = button.data('busy');
                if(busyText){
                    if(!button.data('restore')){
                        button.attr('data-restore', button.text());
                    }
                    button.text(busyText);
                }
            } else {
                const restoreText = button.data('restore');
                if(restoreText){
                    button.text(restoreText);
                }
            }

        });

        if(busy){
            this.container.addClass('saving');
        } else {
            this.container.removeClass('saving');
        }
    }

    /**
     * Automatically places the container based on it's use case
     * 
     * Where a sidebar is present, it will be anchored based on the map
     * 
     * In other use cases, it will be placed alongside or below the input that triggers is
     * 
     * @return void
     */
    WPGMZA.MarkerIconEditor.prototype.autoPlace = function(){
        const position = {
            x : 0,
            y : 0
        };

        const ref = this.container.get(0);
        if($(document.body).find('.wpgmza-editor .sidebar').length){
            /* Placed in a fullscreen editor style window */
            const editor = $(document.body).find('.wpgmza-editor').get(0);
            const sidebar = $(document.body).find('.wpgmza-editor .sidebar').get(0);
            
            if(sidebar.offsetWidth && sidebar.offsetWidth < editor.offsetWidth){
                position.x = sidebar.offsetWidth;

                if(editor.offsetHeight){
                    position.y = (editor.offsetHeight / 2) - (ref.offsetHeight / 2);
                }
            } else {
                position.x = (editor.offsetWidth / 2) - (ref.offsetWidth / 2);
                position.y = sidebar.offsetHeight + 10;
            }

            
        } else {
            /* Place relative to the binding controller */
            if(this.binding){
                const binding = $(this.binding);
                const wrapper = binding.closest('.wpgmza-marker-icon-picker').get(0);

                if(wrapper){
                    const boundingRect = wrapper.getBoundingClientRect();

                    position.x = wrapper.offsetLeft; // We may need to change this to offset from bounding client
                    position.y = boundingRect.top + window.scrollY + 10;
                }
                
            }
        }

        this.container.css({
            left : position.x + "px",
            top : position.y + "px"
        });
    }

    /**
     * This will grab all the input data, and compile it into a handy template config object
     * 
     * This would then be pushed with the file upload, and stored a JSON template on the server, in a sub-directory, which will later be used to determine
     * which icons are static, and which icons are dynamic. Dynamic templates would be rebuilt, allowing modifications to be made to icons 
     * 
     * Standard images would just be served, with editor tools 
     * 
     * @return object
     */
    WPGMZA.MarkerIconEditor.prototype.exportTemplate = function(){
        if(this.renderMode === WPGMZA.MarkerIconEditor.RENDER_MODE_SHAPE){
            const params = this.getShapeParams(true);

            params.filters = {};
            this.effectControls.each((index, elem) => {
                const input = $(elem);
                params.filters[input.data('control')] = parseInt(input.val().trim());
            });

            params.overlay = {};
            this.layerInputs.each((index, input) => {
                params.overlay[$(input).data('control')] = $(input).val();
            });

            params.overlayMode = this.layerModeState;

            return params;
        }
        return false;
    }

    /**
     * Import a template into the editor
     * 
     * This will move over all the keys in the template and apply them to the sliders
     * 
     * Type dictates whether or not the template is partially replaced of fully. For system templates
     * things like colors will not be altered, but for user templates, they are replaced fully
     * 
     * @param object template 
     * @param string type
     * 
     * @return void 
     */
    WPGMZA.MarkerIconEditor.prototype.importTemplate = function(template, type){
        let ignore = [];
        if(type === 'system'){
            ignore = ['colorMode', 'color', 'colorGradientA', 'colorGradientB', 'colorGradientAngle', 'shadowColor', 'strokeColor', 'overlayColor'];
        }

        const dynamic = {
            overlayMode : this.layerMode
        };

        for(let key in template){
            if(ignore.indexOf(key) !== -1){
                continue;
            }

            let data = template[key];
            if(data instanceof Object){
                /* Grouping */
                for(let subKey in data){
                    if(ignore.indexOf(subKey) !== -1){
                        continue;
                    }

                    let subVal = data[subKey];
                    switch(key){
                        case 'body':
                        case 'tail':
                            /* Shape params */
                            const shapeController = this.container.find(`.wpgmza-icon-shape-control-wrapper input[data-control="${subKey}"][data-shape="${key}"]`);
                            if(shapeController.length){
                                /* Shape part controller */
                                shapeController.val(subVal).trigger('change');

                                /* Update properly for color picker */
                                shapeController.each((index, element) => {
                                    if(typeof element.wpgmzaColorInput !== 'undefined'){
                                        element.wpgmzaColorInput.parseColor(subVal);
                                    }
                                });
                            }
                            break;
                        case 'filters':
                            /* Filters */
                            const filterController = this.container.find(`.wpgmza-icon-effect-mode-sliders input[data-control="${subKey}"]`);
                            if(filterController.length){
                                filterController.val(subVal).trigger('change');
                            }
                            break;
                        case 'overlay':
                            /* Overlay */
                            const overlayController = this.container.find(`.wpgmza-icon-layer-control input[data-control="${subKey}"]`);
                            if(overlayController.length){
                                overlayController.val(subVal).trigger('change');
                            }

                            /* Update properly for color picker */
                            overlayController.each((index, element) => {
                                if(typeof element.wpgmzaColorInput !== 'undefined'){
                                    element.wpgmzaColorInput.parseColor(subVal);
                                }
                            });
                            break;
                    }
                }
            } else {
                /* Primary */
                const controllers = {
                    shape : this.container.find(`.wpgmza-icon-shape-control-wrapper input[data-control="${key}"]:not([data-shape]), .wpgmza-icon-shape-control-wrapper select[data-control="${key}"]:not([data-shape])`),
                }

                if(controllers.shape.length){
                    /* Global shape controller */
                    controllers.shape.val(data).trigger('change');

                    /* Update properly for color picker */
                    controllers.shape.each((index, element) => {
                        if(typeof element.wpgmzaColorInput !== 'undefined'){
                            element.wpgmzaColorInput.parseColor(data);
                        }
                    });
                } else if(typeof dynamic[key] !== 'undefined'){
                    /* Dynamic relay to an internal reference */
                    dynamic[key].val(data).trigger('change');
                }
            }
        }

    }

    /* Global initiaizer */
    $(document).ready(function(event) {
		const element = $(".wpgmza-marker-icon-editor");

        if(!element.length){
			return;
        }

        WPGMZA.markerIconEditor = new WPGMZA.MarkerIconEditor(element);
	});
 });