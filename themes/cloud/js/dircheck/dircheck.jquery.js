/**
 * "InstantCheck" ajax widget
 *
 * Periodically check the values of a set of controls ; if a value changes, the set is sent
 * to the server which answers about the validity of the data ; this validity is then
 * displayed as an icon
 */


$.widget("ui.instantcheck", {
	options: {
		delay: 300,
		icons: {
			'yes': 'ui-icon ui-icon-check left',
			'no': 'ui-icon ui-icon-alert left',
			'na': 'ui-icon ui-icon-refresh left',
			'error': 'ui-icon ui-icon-notice left'
		},
		defaultIcon: 'ui-icon ui-icon-arrowreturnthick-1-e left',
		controls: []
	},
	_create: function() {
		var self = this;
		self.to = setTimeout(function() { self.check(); },  self.options.delay);
		self.oldValues = {};
	},
	check: function()  {
		var self = this;
		clearTimeout( self.to );

		params = { _target: self.element[0].id};
		changed = false;
		for (var i = 0; i < self.options.controls.length; i++) {
			control = $(self.options.controls[i]);
			if (control.length) {
				controlName = control.attr('name');
				newValue = control.val();
				params[controlName] = newValue;
				if ((self.oldValues[controlName] == undefined) || (newValue != self.oldValues[controlName])) {
					changed = true;
					self.oldValues[controlName] = newValue;
				}
			}
		}

		if (changed) {
			$(self.element).addClass('ui-state-disabled');

			$.ajax({
				url:  document.URL,
				dataType: 'json',
				data: params,
				headers:  { 'X-Requested-Type': 'json' },
				beforeSend : function(xhr) {
						xhr.setRequestHeader('X-Requested-Type','json');
				},
				success: function(data) {
					if (data) {
						element =	$(self.element);
						element.removeClass('ui-state-disabled');
						if (data.result && self.options.icons[data.result]) src = self.options.icons[data.result];
						else src = self.options.defaultIcon;
						element.html('<span class=\"' + src + '\">&nbsp;</span>');
						element.attr('title', data.tooltip);
						self.to = setTimeout(function() { self.check(); }, self.options.delay);
						self._trigger( "success", 0, data );
					}
				}
			});
		} else {
			self.to = setTimeout(function() { self.check(); },  self.options.delay);
		}
	},

	destroy: function() {
			$.Widget.prototype.destroy.apply(this, arguments);
	}
});



