/**
 * @namespace WPGMZA
 * @module ProShape
 * @requires WPGMZA.Shape
 */
jQuery(function($) {
	
	var Parent = WPGMZA.Shape;

    /** 
     * A generic shape relay so that shapes can share common polygon features
    */
    WPGMZA.ProShape = function(options, engineFeature)
    {
        var self = this;
        WPGMZA.assertInstanceOf(this, "ProShape");
        
        Parent.apply(this, arguments);

        this.on("mouseover", function(event) {
            self.onMouseOver(event);
        });

        this.on("mouseout", function(event) {
            self.onMouseOut(event);
        });
        
        this.on("click", function(event) {
            self.onClick(event);
        });
    }
    
    WPGMZA.extend(WPGMZA.ProShape, WPGMZA.Shape);

    WPGMZA.ProShape.BASE_LAYER_INDEX       = 99999;

    Object.defineProperty(WPGMZA.ProShape.prototype, "hoverFillColor", {
        enumerable: true,
        
        "get": function()
        {
            if(!this.ohFillColor || !this.ohFillColor.length)
                return "#000000";
            
            return "#" + this.ohFillColor.replace(/^#/, "");
        },
        "set": function(a){
            this.ohFillColor = a;
        }
        
    });
    
    Object.defineProperty(WPGMZA.ProShape.prototype, "hoverStrokeColor", {
        enumerable: true,
        
        "get": function()
        {
            if(!this.ohLineColor || !this.ohLineColor.length)
                return "#000000";
            
            return  "#" + this.ohLineColor.replace(/^#/, "");
        },
        "set": function(a){
            this.ohLineColor = a;
        }
        
    });
    
    Object.defineProperty(WPGMZA.ProShape.prototype, "hoverFillOpacity", {
        enumerable: true,
        
        "get": function()
        {
            if(!this.ohFillOpacity){
                return 0.5;
            }
            
            return this.ohFillOpacity;
        },
        "set": function(a){
            this.ohFillOpacity = a;
        }
        
    });

    Object.defineProperty(WPGMZA.ProShape.prototype, "hoverLineOpacity", {
        enumerable: true,
        
        "get": function()
        {
            if(!this.ohLineOpacity){
                return 0.5;
            }
            
            return this.ohLineOpacity;
        },
        "set": function(a){
            this.ohLineOpacity = a;
        }
        
    });

    Object.defineProperty(WPGMZA.ProShape.prototype, "layergroup", {
        enumerable : true,
        get: function() {
            if(this._layergroup){
                return this._layergroup;
            }
            return 0;
        },
        set: function(value) {
            if(parseInt(value)){
                this._layergroup = parseInt(value) + WPGMZA.ProShape.BASE_LAYER_INDEX;
            }
        }
    });

    WPGMZA.ProShape.prototype.onClick = function(event){
        if(!this.map || !this.map.settings){
            return;
        }

        if(this.map.settings.disable_polygon_info_windows){
            return;
        }
        this.openInfoWindow();
    }

    WPGMZA.ProShape.prototype.onMouseOver = function(event){
        if(!parseInt(this.hoverEnabled)){
            return;
        }

        this.revertOptions = this.getScalarProperties();

        var options = {
            fillColor:      this.hoverFillColor,
            strokeColor:    this.hoverStrokeColor,
            fillOpacity:    this.hoverFillOpacity,
            strokeOpacity:  this.hoverLineOpacity
        };

        this.setOptions(options);
    }

    WPGMZA.ProShape.prototype.onMouseOut = function(event){
        if(!parseInt(this.hoverEnabled)){
            return;
        }

        var options = {
            fillColor:      this.fillColor,
            strokeColor:    this.strokeColor,
            fillOpacity:    this.fillOpacity,
            strokeOpacity:  this.strokeOpacity
        };

        if(this.revertOptions){
            options =  this.revertOptions;
            this.revertOptions = false;
        }
        
        this.setOptions(options);
    }

    WPGMZA.ProShape.prototype.onAdded = function(){
        WPGMZA.Shape.prototype.onAdded.call(this, arguments);

        if(this.layergroup){
            this.setLayergroup(this.layergroup);
        }
    }

    WPGMZA.ProShape.prototype.openInfoWindow = function() {
        if(!this.map) {
            console.warn("Cannot open infowindow for shape with no map");
            return;
        }
        
        if(this.map.lastInteractedMarker){
            this.map.lastInteractedMarker.infoWindow.close();
        }

        this.map.lastInteractedMarker = this;
        
        this.initInfoWindow();

        this.title = this.name;
        this.pic = "";
        this.infoWindow.open(this.map, this);
        this.infoWindow.setPosition(this.getPosition());

        if(this.infoWindow.element && this.infoWindow.element.classList){
            this.infoWindow.element.classList.add('ol-info-window-shape');
        }

        if(!this.infoWindow.getContent().length){
            this.infoWindow.close();
        }
    }

    WPGMZA.ProShape.prototype.initInfoWindow = function(){
        if(this.infoWindow){
            return;
        }
        
        this.infoWindow = WPGMZA.InfoWindow.createInstance();
    }



    WPGMZA.ProShape.prototype.getPosition = function(){
        return this.position;
    }

    WPGMZA.ProShape.prototype.initShapeLabels = function(){
        if(WPGMZA.getMapByID(this.map_id)){
            var settings = WPGMZA.getMapByID(this.map_id).settings;
            if(settings && settings.polygon_labels){
                if(this.name){
                    var pos = this.getPosition();
                    var text = WPGMZA.Text.createInstance({
                        text: this.name,
                        map: WPGMZA.getMapByID(this.map_id),
                        position: new WPGMZA.LatLng(pos)
                    });
                }
            }
        }
    }

    WPGMZA.ProShape.prototype.setLayergroup = function(layergroup){
        this.layergroup = layergroup;
        if(this.layergroup){
            this.setOptions({
                zIndex: this.layergroup
            });
        }
    } 
});