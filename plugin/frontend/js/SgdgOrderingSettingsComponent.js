'use strict';
var el = wp.element.createElement;

var SgdgOrderingSettingsComponent = function( attributes ) {
	var valueBy, valueOrder;
	this.block = attributes.block;
	this.name = attributes.name;
	valueBy = this.block.getAttribute( this.name + '_by' );
	valueOrder = this.block.getAttribute( this.name + '_order' );
	if ( undefined === valueBy ) {
		valueBy = sgdgBlockLocalize[this.name].default_by;
	}
	if ( undefined === valueOrder ) {
		valueOrder = sgdgBlockLocalize[this.name].default_order;
	}
	this.state = {valueBy: valueBy, valueOrder: valueOrder};
};
SgdgOrderingSettingsComponent.prototype = Object.create( wp.element.Component.prototype );
SgdgOrderingSettingsComponent.prototype.render = function() {
	var that = this;
	var valueBy = this.block.getAttribute( this.name + '_by' );
	var valueOrder = this.block.getAttribute( this.name + '_order' );
	return el( 'div', {className: 'sgdg-block-settings-row'}, [
		el( wp.components.ToggleControl, {checked: undefined !== valueBy && undefined !== valueOrder, className: 'sgdg-block-settings-checkbox', onChange: function( e ) {
			that.toggle();
		}}),
		el( 'span', {className: 'sgdg-block-settings-description'}, [
			sgdgBlockLocalize[this.name].name,
			':'
		]),
		el( 'select', {className: 'sgdg-block-settings-select', disabled: undefined === valueOrder, onChange: function( e ) {
			that.changeOrder( e );
		}, placeholder: sgdgBlockLocalize[this.name].default_order, type: 'number', value: this.state.valueOrder}, [
			el( 'option', {selected: 'ascending' === this.state.valueOrder, value: 'ascending'}, sgdgBlockLocalize['ordering_option_ascending']),
			el( 'option', {selected: 'descending' === this.state.valueOrder, value: 'descending'}, sgdgBlockLocalize['ordering_option_descending'])
		]),
		el( 'div', {className: 'sgdg-block-settings-radio'}, [
			el( 'label', {for: this.name + '_by_time'}, [
				el( 'input', {checked: 'time' === this.state.valueBy, disabled: undefined === valueBy, id: this.name + '_by_time', name: this.name + '_by', onChange: function( e ) {
				that.changeBy( e );
			}, type: 'radio', value: 'time'}),
				sgdgBlockLocalize['ordering_option_by_time']
			])
		]),
		el( 'div', {className: 'sgdg-block-settings-radio'}, [
			el( 'label', {for: this.name + '_by_name'}, [
				el( 'input', {checked: 'name' === this.state.valueBy, disabled: undefined === valueBy, id: this.name + '_by_name', name: this.name + '_by', onChange: function( e ) {
				that.changeBy( e );
			}, type: 'radio', value: 'name'}),
				sgdgBlockLocalize['ordering_option_by_name']
			])
		])
	]);
};
SgdgOrderingSettingsComponent.prototype.toggle = function() {
	this.block.setAttribute( this.name + '_by', undefined !== this.block.getAttribute( this.name + '_by' ) ? undefined : this.state.valueBy );
	this.block.setAttribute( this.name + '_order', undefined !== this.block.getAttribute( this.name + '_order' ) ? undefined : this.state.valueOrder );
};
SgdgOrderingSettingsComponent.prototype.changeBy = function( e ) {
	this.setState({valueBy: e.target.value});
	this.block.setAttribute( this.name + '_by', e.target.value );
};
SgdgOrderingSettingsComponent.prototype.changeOrder = function( e ) {
	this.setState({valueOrder: e.target.value});
	this.block.setAttribute( this.name + '_order', e.target.value );
};
