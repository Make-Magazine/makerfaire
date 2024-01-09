/* Selectize deselect function */
Selectize.define('click2deselect', function(options) {
	var self = this;
	var setup = self.setup;
	this.setup = function() {
		setup.apply(self, arguments);
		// add additional handler
		self.$dropdown.on('click', '[data-selectable]', function(e) {
			let value = this.getAttribute('data-value');
			if( this.classList.contains('selected') ) {
				self.removeItem(value);
				self.refreshItems();
				self.refreshOptions();
			}
			return false;
		});
		self.on('item_remove', function (value) {
			self.getOption(value).removeClass('selected')
		});
	}
});