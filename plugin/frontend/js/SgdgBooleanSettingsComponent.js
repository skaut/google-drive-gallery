'use strict';
var el = wp.element.createElement;

var SgdgBooleanSettingsComponent = function( attributes ) {
	SgdgSettingsComponent.call( this, attributes );
};
SgdgBooleanSettingsComponent.prototype = Object.create( SgdgSettingsComponent.prototype );
SgdgBooleanSettingsComponent.prototype.renderInput = function() {
	var that = this;
	var value = this.block.getAttribute( this.name );
	return el( 'input', {checked: 'true' === this.state.value, className: 'sgdg-block-settings-boolean', disabled: undefined === value, onChange: function( e ) {
		that.change( e );
	}, type: 'checkbox'});
};
SgdgBooleanSettingsComponent.prototype.getValue = function( element ) {
	return element.checked ? 'true' : 'false';
};
