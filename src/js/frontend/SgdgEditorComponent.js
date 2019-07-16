'use strict';
var el = wp.element.createElement;

var SgdgEditorComponent = function( props ) {
	this.props = props;
	this.state = {error: undefined, list: undefined};
};
SgdgEditorComponent.prototype = Object.create( wp.element.Component.prototype );
SgdgEditorComponent.prototype.componentDidMount = function() {
	this.ajax();
};
SgdgEditorComponent.prototype.ajax = function() {
	var that = this;
	$.get( sgdgBlockLocalize.ajax_url, {
		_ajax_nonce: sgdgBlockLocalize.nonce, // eslint-disable-line camelcase
		action: 'list_gallery_dir',
		'path': this.getAttribute( 'path' )
		}, function( data ) {
			if ( data.directories ) {
				that.setState({list: data.directories});
			} else if ( data.error ) {
				that.setState({error: data.error});
			}
		}
	);
};
SgdgEditorComponent.prototype.render = function() {
	var that = this;
	var children = [];
	var path = [ el( 'a', {onClick: function( e ) {
		that.pathClick( that, e );
	}}, sgdgBlockLocalize.root_name ) ];
	var i, lineClass;
	if ( this.state.error ) {
		return el( 'div', {class: 'notice notice-error'}, el( 'p', {}, this.state.error ) );
	}
	if ( this.state.list ) {
		if ( 0 < this.getAttribute( 'path' ).length ) {
			children.push( el( 'tr', {}, el( 'td', {class: 'row-title'}, el( 'label', {onClick: function( e ) {
				that.labelClick( that, e );
			}}, '..' ) ) ) );
		}
		for ( i = 0; i < this.state.list.length; i++ ) {
		lineClass = ( 0 === this.getAttribute( 'path' ).length && 1 === i % 2 ) || ( 0 < this.getAttribute( 'path' ).length && 0 === i % 2 ) ? 'alternate' : '';
			children.push( el( 'tr', {class: lineClass}, el( 'td', {class: 'row-title'}, el( 'label', {onClick: function( e ) {
				that.labelClick( that, e );
			}}, this.state.list[i]) ) ) );
		}
		for ( i = 0; i < this.getAttribute( 'path' ).length; i++ ) {
			path.push( ' > ' );
			path.push( el( 'a', { 'data-id': this.getAttribute( 'path' )[i], onClick: function( e ) {
				that.pathClick( that, e );
			}}, this.getAttribute( 'path' )[i]) );
		}
	}
	return el( wp.element.Fragment, {}, [
		el( wp.editor.InspectorControls, {},
			el( SgdgSettingsOverrideComponent, {block: this})
		),
		el( 'table', { class: 'widefat' }, [
			el( 'thead', {},
				el( 'tr', {},
					el( 'th', {class: 'sgdg-block-editor-path'}, path )
				)
			),
			el( 'tbody', {}, children ),
			el( 'tfoot', {},
				el( 'tr', {},
					el( 'th', {class: 'sgdg-block-editor-path'}, path )
				)
			)
		])
	]);
};
SgdgEditorComponent.prototype.pathClick = function( that, e ) {
	var path = that.getAttribute( 'path' );
	path = path.slice( 0, path.indexOf( $( e.currentTarget ).data( 'id' ) ) + 1 );
	that.setAttribute( 'path', path );
	that.setState({error: undefined, list: undefined}, that.ajax );
};
SgdgEditorComponent.prototype.labelClick = function( that, e ) {
	var newDir = $( e.currentTarget ).html();
	var path;
	if ( '..' === newDir ) {
		path = that.getAttribute( 'path' ).slice( 0, that.getAttribute( 'path' ).length - 1 );
	} else {
		path = that.getAttribute( 'path' ).concat( newDir );
	}
	that.setAttribute( 'path', path );
	that.setState({error: undefined, list: undefined}, that.ajax );
};
SgdgEditorComponent.prototype.getAttribute = function( name ) {
	return this.props.attributes[name];
};
SgdgEditorComponent.prototype.setAttribute = function( name, value ) {
	var attr = {};
	attr[name] = value;
	this.props.setAttributes( attr );
};
