'use strict';
jQuery( document ).ready( function( $ ) {
	var el = wp.element.createElement;
	var iconSvg = el( 'svg', {class: 'sgdg-block-icon', viewBox: '0 0 103 89'}, [
		el( 'defs', {}, [
			el( 'style', {}, '.cls-2 {opacity: 0.5;} .cls-3 {fill: #ffcf4a;} .cls-4 {fill: #25a366;} .cls-5 {fill: #4385f4;} .cls-6 {fill: #0da960;} .cls-7 {fill: url(#a);} .cls-8 {fill: url(#b);} .cls-9 {fill: #2d6fdd;} .cls-10 {fill: #e5b93c;} .cls-11 {fill: #0c9b57;}' ),
			el( 'radialGradient', {id: 'a', cx: '2799.2', cy: '3846.9', r: '21.21', gradientTransform: 'matrix(2.83 1.63 1.63 -2.83 -14102 6364.8)', gradientUnits: 'userSpaceOnUse'}, [
				el( 'stop', {'stop-color': '#4387fd', offset: '0'}),
				el( 'stop', {'stop-color': '#3078f0', offset: '.65'}),
				el( 'stop', {'stop-color': '#2b72ea', offset: '.91'}),
				el( 'stop', {'stop-color': '#286ee6', offset: '1'})
			]),
			el( 'radialGradient', {id: 'b', cx: '2799.2', cy: '3846.9', r: '21.21', gradientTransform: 'matrix(2.83 1.63 1.63 -2.83 -14102 6364.8)', gradientUnits: 'userSpaceOnUse'} [
				el( 'stop', {'stop-color': '#ffd24d', offset: '0'}),
				el( 'stop', {'stop-color': '#f6c338', offset: '1'})
			])
		]),
		el( 'title', {}, 'skaut-google-drive-gallery-icon' ),
		el( 'g', {transform: 'translate(-13.66,-20.66)'}, [
			el( 'image', {class: 'cls-2', transform: 'translate(38.66,46.66)', width: '78', height: '63', opacity: '.5', 'xlink:href': 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE8AAABACAYAAABbYipTAAAACXBIWXMAAAsSAAALEgHS3X78AAAB5UlEQVR4Xu3cMW6jUBSF4f89MnJB9gB1InkLLllBevdZz/TuswKX2QLSuLb3EAoX8KbASbADZCans8/XIKFL8+thKt+QUsJ+5u67AQDqKqfNCmJ2/93o1ejaN7L2wHLbTI2EyZM3DJZSAekJKMaHr9IBwgshHKZCjserq5wuriCu6YPdn6751+Gr1QAH4K2/dhti9zoM+DXeR7jsGdKK2wo2pYHwCu3vYcB4NuJwU/K+R/ZMF1fUVQ6XH4y0KKFdO9yoU8DYkH4dgD+fJ6+uclL3AOkBh5uSAwWkHIavbVqU/Rc1lGNP2buUk1JBXeXx7ObtfVF/IJSQnkiL8vyDYf/i49V1PIHjCRxP4HgCxxM4nsDxBI4ncDyB4wkcT+B4AscTOJ7A8QSOJ3A8geMJHE/geALHEziewPEEjidwPIHjCRxP4HgCxxM4nsDxBI4ncDyB4wkcT+B4AscTOJ7A8QSOJ3A8geMJHE/geALH+3+n3QOhGcQL7wsJJldhGEDa91svjvvPeOHY3yTtpx4zGgg7Qtyx3A5O3nLbEOIOwg6fvjGnDRfdpj9ol7954biHbtMPOeDA6GqQmb0qcQ084r/Oj4aDuY0+bVYQ4uMNrkG6NLrNB+Z2ScFtLuC6NLOQaz6ezfoL4Yavo8+beCoAAAAASUVORK5CYII='}),
			el( 'rect', {class: 'cls-1', x: '42.7', y: '50.11', width: '70.5', height: '55.68', rx: '6.75', ry: '6.75', fill: '#fff'}),
			el( 'path', {class: 'cls-3', d: 'm106.45 51.72a5.16 5.16 0 0 1 5.14 5.14v42.14a5.16 5.16 0 0 1 -5.14 5.14h-57a5.16 5.16 0 0 1 -5.14 -5.14v-42.14a5.16 5.16 0 0 1 5.14 -5.14h57m0-3.22h-57a8.37 8.37 0 0 0 -8.36 8.36v42.14a8.37 8.37 0 0 0 8.36 8.36h57a8.37 8.37 0 0 0 8.36 -8.36v-42.14a8.37 8.37 0 0 0 -8.36 -8.36z', fill: '#ffcf4a'}),
			el( 'g', {fill: '#ffcf4a'}, [
				el( 'circle', {class: 'cls-3', cx: '104.25', cy: '65.75', r: '5.24'}),
				el( 'polygon', {class: 'cls-3', points: '63.76 74.09 45 102.03 47.68 104.18 108.08 104.18 110.9 102.15 87.75 60.09 71.49 85.98'})
			]),
			el( 'image', {class: 'cls-2', transform: 'translate(26.66,33.66)', width: '77', height: '63', opacity: '.5', 'xlink:href': 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE4AAABACAYAAAC0oEFtAAAACXBIWXMAAAsSAAALEgHS3X78AAACFElEQVR4Xu3cMW7iYBCG4W+GFBEO4gimzhYcgVNQIqXfmlNQp0eipE/PEShwjY+AiFGKZWYLMOvAGsJstfH31GMkvxrjyr+4O+h+D7cGAKA/Gyc73aa6l6dbs/87a/l72zr5cjQprs3J3zbuPJSJpaoYOjy9GP5mBJKbYa6u+bWIF+H6s3Hyge0A6i+nUC5PgKeAJOc/8P14AUgO8fcyIlRW5wE/hSujudhPAINmhLrmEFEEK5hMH9FZlPFO4RjtGi8ALMT1tYx3ejnsbNPTB3mBM9olSQAfQL3Y/drkAFYKHLYNKs8OPDNaHUkcnqq0EgBQ4LhtiiHce1evbTxJTCztz8aJAoBKK/HGvDX/gXtPFcOdbXp6a5aq/jyuDBfEcEEMF8RwQQwXxHBBDBfEcEEMF8RwQQwXxHBBDBfEcEEMF8RwQQwXxHBBDBfEcEEMF8RwQQwXxHBBDBfEcEEMF8RwQQwXxHBBDBfEcEEMF8RwQQwXxHBBDBfEcEEMF8RwQQwXxHBBDBfEcEEMdxcvBJKb7wsFAPN9IZD8+Ak11RFZm2He1u5aAaCt3bUZ5hBZ37i0wbwQIIN5thxNDhu3HE0KmGcCZNy6GpVtAyr/cW3trmEyBbBgvHOftw2ohFuOJsUjOgtxfRWRN3D7cLz/TETeYDIttw2oOelmp9sU5j+acixQnS+fdFPVpIOo6tx1thJ9zW+sRPfOhOkHWAAAAABJRU5ErkJggg=='}),
			el( 'rect', {class: 'cls-1', x: '29.85', y: '37.25', width: '70.5', height: '55.68', rx: '6.75', ry: '6.75', fill: '#fff'}),
			el( 'path', {class: 'cls-4', d: 'm93.59 38.87a5.15 5.15 0 0 1 5.14 5.13v42.18a5.16 5.16 0 0 1 -5.14 5.15h-57a5.16 5.16 0 0 1 -5.14 -5.15v-42.18a5.15 5.15 0 0 1 5.14 -5.14h57m0-3.23h-57a8.38 8.38 0 0 0 -8.35 8.37v42.18a8.37 8.37 0 0 0 8.36 8.37h57a8.38 8.38 0 0 0 8.4 -8.37v-42.18a8.39 8.39 0 0 0 -8.37 -8.37z', fill: '#25a366'}),
			el( 'g', {fill: '#25a366'}, [
				el( 'circle', {class: 'cls-4', cx: '65.1', cy: '63.1', r: '9'}),
				el( 'path', {class: 'cls-4', d: 'm47.23 91.76c1.76-9.28 7.6-16.15 14.48-16.15h6.77c6.88 0 12.72 6.87 14.48 16.15z'}),
				el( 'circle', {class: 'cls-4', cx: '87.85', cy: '58.32', r: '5.43'}),
				el( 'path', {class: 'cls-4', d: 'm77.07 75.61c1.06-5.6 4.58-9.74 8.73-9.74h4.09c4.15 0 7.68 4.14 8.74 9.74z'}),
				el( 'circle', {class: 'cls-4', cx: '42.34', cy: '58.32', r: '5.43'}),
				el( 'path', {class: 'cls-4', d: 'm31.56 75.61c1.06-5.6 4.59-9.74 8.74-9.74h4.09c4.15 0 7.67 4.14 8.73 9.74z'})
			]),
			el( 'image', {class: 'cls-2', transform: 'translate(13.66,20.66)', width: '78', height: '63', opacity: '.5', 'xlink:href': 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE8AAABACAYAAABbYipTAAAACXBIWXMAAAsSAAALEgHS3X78AAACHElEQVR4Xu3cPXLaUBTF8XOfBGNGmRRsAGq8CEpm3NOnT5tl0KanZwUpWYRdow1QeKyBQejdFCCDiTAkp4vOr9VH8Z+rN9Bcc3fIvwm3bpDr0msXJjNkVbkbeNx+uXbP/87Cw1vS6ea/fqBovH752Z6i7R5DCFN3DJoebAMz5DHGhYXuc1PED/EmM2Tl9nWcJOk39/gI2ABAdvnSFikAz83Cc1Xt552Hr8vzgB/OvHKzHh7C+RNgI7Q7HABkgI3c/SlJ0u/l9nU8mZ2avJ95kxkyC92RA4r2p8zdx0mSFuVmnQP9Z+Bs8srNehhCmMJ9ePUV7ZY5MLLQHdXTFwBN3d3chyGEablZD4FjPE3d3TJ3DCykp8mzkGbHnySaur+gfxgExSMoHkHxCIpHUDyC4hEUj6B4BMUjKB5B8QiKR1A8guIRFI+geATFIygeQfEIikdQPILiERSPoHgExSMoHkHxCIpHUDyC4hEUj6B4BMUjKB5B8QiKR1A8guIRFI+geATFIygeQfEIAQA87gsz5EDzIgJpFgCg0+uvYowLmK1u3N92hRlyj/sCOMY7LB2IOVyT9ymzVYxx0en1V8DZmadP96bCgBePu5d6w8V7vE6vv6qq/dzMllDAS4WZLatqP6+nDtBelXvU4X5e7lW5Y6NP6yIWgOdAeAMOW32aFtIADfFqbV2LVK9AAmIOfL5P6mq8WtsWct1avnXuN8rX846ofphwAAAAAElFTkSuQmCC'}),
			el( 'rect', {class: 'cls-1', x: '16.99', y: '24.4', width: '70.5', height: '55.68', rx: '6.75', ry: '6.75', fill: '#fff'}),
			el( 'path', {class: 'cls-5', d: 'm80.74 26a5.16 5.16 0 0 1 5.14 5.14v42.19a5.16 5.16 0 0 1 -5.14 5.14h-57a5.16 5.16 0 0 1 -5.14 -5.14v-42.18a5.16 5.16 0 0 1 5.14 -5.15h57m0-3.22h-57a8.37 8.37 0 0 0 -8.36 8.36v42.19a8.37 8.37 0 0 0 8.36 8.36h57a8.37 8.37 0 0 0 8.36 -8.36v-42.18a8.37 8.37 0 0 0 -8.36 -8.36z', fill: '#4385f4'})
		]),
		el( 'g', {transform: 'translate(-13.66,-20.66)'}, [
			el( 'path', {class: 'cls-6', d: 'm40.66 67.63-6.18-10.71 11.58-20.07 6.18 10.71z', fill: '#0da960'}),
			el( 'path', {class: 'cls-7', d: 'm40.66 67.63 6.19-10.71h23.15l-6.18 10.71z', fill: 'url(#a)'}),
			el( 'path', {class: 'cls-8', d: 'm70 56.92h-12.37l-11.57-20.07h12.37z', fill: 'url(#b)'}),
			el( 'path', {class: 'cls-9', d: 'm52.24 56.92h-5.39l2.68-4.68-8.87 15.39z', fill: '#2d6fdd'}),
			el( 'path', {class: 'cls-10', d: 'm57.63 56.92h12.37l-15-4.68z', fill: '#e5b93c'}),
			el( 'path', {class: 'cls-11', d: 'm49.53 52.24 2.71-4.68-6.18-10.71z', fill: '#0c9b57'})
		])
	]);

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
			'path': this.props.attributes.path
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
			if ( 0 < this.props.attributes.path.length ) {
				children.push( el( 'tr', {}, el( 'td', {class: 'row-title'}, el( 'label', {onClick: function( e ) {
					that.labelClick( that, e );
				}}, '..' ) ) ) );
			}
			for ( i = 0; i < this.state.list.length; i++ ) {
			lineClass = ( 0 === this.props.attributes.path.length && 1 === i % 2 ) || ( 0 < this.props.attributes.path.length && 0 === i % 2 ) ? 'alternate' : '';
				children.push( el( 'tr', {class: lineClass}, el( 'td', {class: 'row-title'}, el( 'label', {onClick: function( e ) {
					that.labelClick( that, e );
				}}, this.state.list[i]) ) ) );
			}
			for ( i = 0; i < this.props.attributes.path.length; i++ ) {
				path.push( ' > ' );
				path.push( el( 'a', { 'data-id': this.props.attributes.path[i], onClick: function( e ) {
					that.pathClick( that, e );
				}}, this.props.attributes.path[i]) );
			}
		}
		return el( 'table', { class: 'widefat' }, [
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
		]);
	};
	SgdgEditorComponent.prototype.pathClick = function( that, e ) {
		var path = that.props.attributes.path;
		path = path.slice( 0, path.indexOf( $( e.currentTarget ).data( 'id' ) ) + 1 );
		that.props.setAttributes({'path': path});
		that.setState({error: undefined, list: undefined}, that.ajax );
	};
	SgdgEditorComponent.prototype.labelClick = function( that, e ) {
		var newDir = $( e.currentTarget ).html();
		var path;
		if ( '..' === newDir ) {
			path = that.props.attributes.path.slice( 0, that.props.attributes.path.length - 1 );
		} else {
			path = that.props.attributes.path.concat( newDir );
		}
		that.props.setAttributes({'path': path});
		that.setState({error: undefined, list: undefined}, that.ajax );
	};

	wp.blocks.registerBlockType( 'skaut-google-drive-gallery/gallery', {
		title: sgdgBlockLocalize.block_name,
		description: sgdgBlockLocalize.block_description,
		category: 'common',
		icon: iconSvg,
		attributes: {
			path: {
				type: 'array',
				default: []
			}
		},
		edit: SgdgEditorComponent,
		save: renderFrontend,
		transforms: {
			from: [
				{
					type: 'shortcode',
					tag: [ 'sgdg' ],
					priority: 15,
					attributes: {
						path: {
							type: 'string',
							shortcode: extractFromShortcode
						}
					}
			}
			]
		}
	});

	function renderFrontend( props ) {
		return null;
	}

	function extractFromShortcode( named ) {
		if ( ! named.named.path ) {
			return [];
		}
		return named.named.path.trim().replace( /^\/+|\/+$/g, '' ).split( '/' );
	}
});
