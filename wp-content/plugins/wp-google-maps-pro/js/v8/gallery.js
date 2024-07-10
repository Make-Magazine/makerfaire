/**
 * @namespace WPGMZA
 * @module WPGMZA.Gallery
 * @requires WPGMZA
 * 
 * Replaced WPGMZA.MarkerGallery in the AtlasNovus engine, so remove dependency on libraries
 */
jQuery(function($) {
    WPGMZA.Gallery = function(items, feature, fullSize){
        var self = this; 

        this.guid = WPGMZA.guid();

        const settings = this.parseFeatureSettings(feature);

        this.config = this.parseConfig(settings, fullSize);
        this.config.feature = feature;

        this.element = $("<div class='wpgmza-gallery' id='" + this.guid + "'/>");
        this.stage = $("<div class='wpgmza-gallery-stage'/>");

        this.index = 0;
        this.sources = [];
        if(items && items instanceof Array){
            this.sources = items;
        } 

        this.prepare();

        this.element.append(this.stage);

        if(this.config.navigation && this.sources.length > 1){
            this.navigation = {
                left : $("<div class='navigation left' />"),
                right : $("<div class='navigation right' />")
            };

            this.element.append(this.navigation.left);
            this.element.append(this.navigation.right);
        }

        this.bindEvents();
    }

    WPGMZA.Gallery.prototype.bindEvents = function(){
        var self = this;

        this.element.on('mouseenter', function(){
            self.hasFocus = true;
        });

        this.element.on('mouseleave', function(){
            self.hasFocus = false;
        });

        this.element.on('click', '.navigation', function(){
            if($(this).hasClass('left')){
                self.previous();
            } else {
                self.next();
            }
        });

        this.element.on('click', '.wpgmza-gallery-item', function(){
            self.lightbox();
        });

        if(this.config.autoplay && this.sources.length > 1){
            this.interval = setInterval(function(){
                self.ticker();
            }, this.config.timer);
        }
    }

    WPGMZA.Gallery.prototype.parseConfig = function(settings, fullSize){
        var config = {
            autoplay : true,
            timer : 5000,
            pauseOnFocus : true,
            resize : true,
            navigation : true,
            lightbox : true
        };

        if(fullSize){
            config.fullSize = true;
        }

        if(settings && settings instanceof Object){
            for(var i in settings){
                if(config[i]){
                    config[i] = settings[i];
                }
            }
        }

        return config;
    }

    WPGMZA.Gallery.prototype.parseFeatureSettings = function(feature){
        const settings = {};
        if(typeof feature._internal === 'undefined'){
            /* Feature Approach 
             *
             * These settings are being pulled directly from the feature, or alternatively, from the map settings
             * 
             * Lots of magic, and dynamic code runs here
            */
            const remap = {
                'disable_lightbox_images' : 'lightbox'
            };


            for(let originalKey in remap){
                const remappedKey = remap[originalKey];
                let remappedValue = false;

                if(feature && feature.map && feature.map.settings && feature.map.settings[originalKey]){
                    remappedValue = feature.map.settings[originalKey];
                } 

                if(WPGMZA.settings && WPGMZA.settings[originalKey]){
                    remappedValue = WPGMZA.settings[originalKey];
                }

                if(remappedValue){
                    switch(remappedKey){
                        case 'lightbox':
                            /* Invert it, disabled option is checked, so feature is disabled */
                            settings[remappedKey] = false;
                            break;
                        default: 
                            settings[remappedKey] = remappedValue;
                            break;
                    }
                }
            }
        } else {
            /* Object Approach 
             * 
             * Usually internally repassed by the lightbox system, we should leave this unchanged 
             * 
             * In other words, these don't reparse settings automatically, or at least not as automatically as the feature approach
            */
            return feature;
        }

        return settings;
    }

    WPGMZA.Gallery.prototype.prepare = function(){
        /* Setup the default height for the stage, before images are loaded */
        if(WPGMZA.settings && WPGMZA.settings.infoWindowImageResizing && WPGMZA.settings.infoWindowImageHeight){
            this.stage.css('--wpgmza-gallery-stage-dynamic-height', parseInt(WPGMZA.settings.infoWindowImageHeight) + 'px');
        }

        if(this.sources.length > 0){
            for(var i in this.sources){
                var source = this.sources[i];

                if(typeof source.url === 'undefined' || typeof source.url !== 'string'){
                    /* The URL is unset, sometimes the case when pulling from posts, ACF, Woo, etc */
                    if(source.thumbnail){
                        source.url = source.thumbnail;
                    }
                }

                if(source.url){
                    let imageUrl = source.url;
                    if(imageUrl instanceof Array){
                        imageUrl = imageUrl.shift();
                    }

                    if(!this.config.fullSize && source.thumbnail && typeof source.thumbnail === 'string'){
                        imageUrl = source.thumbnail;
                    }

                    var item = $("<div class='wpgmza-gallery-item'/>");
                    var img = $("<img/>").attr('src', imageUrl);

                    if(WPGMZA.settings && WPGMZA.settings.infoWindowImageResizing){
                        if(WPGMZA.settings.infoWindowImageWidth){
                            img.css('max-width', parseInt(WPGMZA.settings.infoWindowImageWidth) + "px");
                        }

                        if(WPGMZA.settings.infoWindowImageHeight){
                            img.css('max-height', parseInt(WPGMZA.settings.infoWindowImageHeight) + "px");
                        }
                    }

                    item.append(img);
                    this.stage.append(item);

                    img.on('load', () => {
                        /* Trigger the stage resize */
                        this.resize();
                    });
                }
            }
        }
    }

    WPGMZA.Gallery.prototype.ticker = function(){
        if(this.config.pauseOnFocus && this.hasFocus){
            return;
        }
        this.next();
    }

    WPGMZA.Gallery.prototype.next = function(){
        this.move(this.index + 1);
    }

    WPGMZA.Gallery.prototype.previous = function(){
        this.move(this.index - 1);
    }

    WPGMZA.Gallery.prototype.move = function(index){
        this.index = index;
        if(this.index >= this.sources.length){
            this.index = 0;
        } else if (this.index < 0){
            this.index = this.sources.length - 1;
        }

        this.stage.css('--wpgmza-gallery-index', this.index);

        this.resize();
    }

    WPGMZA.Gallery.prototype.place = function(container){
        var self = this;
        if(container){
            $(container).find('.wpgmza-gallery').remove();
            
            $(container).append(this.element);
            
            setTimeout(function(){
                self.resize();
            }, 10);

            try{
                const nativeElement = this.element.get(0);
                nativeElement.__wpgmzaGallery = this;
            } catch(ex){
                /* Don't do anything */
            }
        }
    }

    WPGMZA.Gallery.prototype.resize = function(){
        if(this.config.resize){
            var child = this.stage.find('.wpgmza-gallery-item:nth-child(' + (this.index + 1) + ')');
            var imageHeight = child.find('img').height() + 'px';
            this.stage.css('max-height', imageHeight);
            this.stage.css('--wpgmza-gallery-stage-dynamic-height', imageHeight);

        }
    }

    WPGMZA.Gallery.prototype.lightbox = function(){
        if(!this.config.lightbox){
            return;
        }

        if(WPGMZA.is_admin && parseInt(WPGMZA.is_admin)){
            return;
        }

        var wrap = $('<div class="wpgmza-gallery-lightbox" />');
        var inner = $('<div class="wpgmza-gallery-lightbox-inner" />');

        wrap.append(inner);

        var settings  = Object.assign({}, this.config);
        settings.lightbox = false;
        settings.autoplay = false;
        settings._internal = true;
        this.lightboxElement = new WPGMZA.Gallery(this.sources, settings, true); 
        this.lightboxElement.place(inner);

        this.lightboxElement.move(this.index);

        wrap.on('click', function(event){
            if(event.target.classList.contains('wpgmza-gallery-lightbox')){
                wrap.remove();
            }
        });

        $(document.body).append(wrap);

        if(this.config.feature && this.config.feature.map) {
            if(this.config.feature.map.isFullScreen()){
                /* The source feature map is in fullscreen, we should move the lightbox into the fullscreen wrapper */
                const anchor = WPGMZA.settings.engine === 'google-maps' ? this.config.feature.map.element.firstChild : this.config.feature.map.element;  
                wrap.appendTo(anchor);
            }
        }
    }

});