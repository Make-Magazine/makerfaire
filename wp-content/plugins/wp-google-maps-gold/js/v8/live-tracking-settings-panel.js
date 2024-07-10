/**
 * @namespace WPGMZA
 * @module LiveTrackingSettingsPanel
 */
jQuery(function($) {
	
	WPGMZA.LiveTrackingSettingsPanel = function()
	{
		var self = this;
		
		this.templateTableItem = $("#wpgmza-live-tracking-devices>tbody>tr");
		this.templateTableItem.remove();
		
		this.refresh();
		
		$("#wpgmza-refresh-live-tracking-devices").on("click", function() {
			self.refresh();
		});
		
		$("#wpgmza-live-tracking-devices").on("change", "input, select, textarea", function(event) {
			self.onDeviceChanged(event);
		});
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.clear = function()
	{
		$("#wpgmza-live-tracking-devices>tbody").html("");
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.refresh = function()
	{
		var self = this;
		
		$("#wpgmza-live-tracking-devices").css("opacity", 0.5);
		
		WPGMZA.restAPI.call("/live-tracker/devices/", {
			success: function(data, status, xhr) {
				self.populate(data);
			}
		});
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.populate = function(devices)
	{
		var self = this;
		var tbody = $("#wpgmza-live-tracking-devices>tbody");
		
		this.clear();
		
		devices.forEach(function(data) {
			
			var item = self.templateTableItem.clone();
			
			for(var name in data)
			{
				var el = $(item).find("[data-name='" + name + "'], [data-ajax-name='" + name + "']");
				
				if(!el.length)
					continue;
				
				if(el.prop("tagName").toLowerCase() != 'input')
				{
					el.text(data[name]);
					continue;
				}
					
				switch(el.attr("type"))
				{
					case "checkbox":
						$(el).prop("checked", data[name] == 1);
						break;
					
					default:
						$(el).val(data[name]);
						break;
				}
			}
			
			tbody.append(item);
			
		});
		
		$("#wpgmza-live-tracking-devices").css("opacity", 1);
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.onDeviceChanged = function(event)
	{
		var row = $(event.target).closest("tr");
		var fields = $(row).find("input[data-name], input[data-ajax-name]");
		var data = {};
		var id = $(row).find("input[data-ajax-name='id']").val();
		
		$("#wpgmza-live-tracking-devices").css("opacity", 0.5);
		
		fields.each(function(index, el) {
			
			var name = $(el).attr("data-name");
			if(!name || !name.length)
				name = $(el).attr("data-ajax-name");
			
			switch($(el).attr("type"))
			{
				case "checkbox":
					data[name] = $(el).prop("checked") ? 1 : 0;
					break;
				
				default:
					data[name] = $(el).val();
					break;
			}
			
		});
		
		WPGMZA.restAPI.call("/live-tracker/devices/" + id, {
			method: "POST",
			data: data,
			success: function(data, status, xhr) {
				
				$("#wpgmza-live-tracking-devices").css("opacity", 1);

				
			}
		});
		
		// console.log(data);
		
	}
	
	$(document).ready(function() {
		
		if(WPGMZA.currentPage == "map-settings")
			WPGMZA.liveTrackingSettingsPanel = new WPGMZA.LiveTrackingSettingsPanel();
		
	});
	
});