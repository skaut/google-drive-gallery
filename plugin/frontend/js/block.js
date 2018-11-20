'use strict';
wp.blocks.registerBlockType( 'skaut-google-drive-gallery/gallery', {
	title: sgdgBlockLocalize.block_name,
	description: sgdgBlockLocalize.block_description,
	category: 'common',
	icon: iconSvg,
	attributes: {
		path: {
			type: 'array',
			default: []
		},
		grid_height: { // eslint-disable-line camelcase
			type: 'int'
		},
		grid_spacing: { // eslint-disable-line camelcase
			type: 'int'
		},
		dir_counts: { // eslint-disable-line camelcase
			type: 'string'
		},
		image_ordering_order: { // eslint-disable-line camelcase
			type: 'string'
		},
		image_ordering_by: { // eslint-disable-line camelcase
			type: 'string'
		},
		dir_ordering_order: { // eslint-disable-line camelcase
			type: 'string'
		},
		dir_ordering_by: { // eslint-disable-line camelcase
			type: 'string'
		},
		preview_size: { // eslint-disable-line camelcase
			type: 'int'
		},
		preview_loop: { // eslint-disable-line camelcase
			type: 'string'
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
