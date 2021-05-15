function renderFrontend(): null {
	return null;
}

function extractFromShortcode(
	attributes: ShortcodeToBlockTransformAttributes
): Array< string > {
	if ( ! attributes.named.path ) {
		return [];
	}
	return attributes.named.path
		.trim()
		.replace( /^\/+|\/+$/g, '' )
		.split( '/' );
}

wp.blocks.registerBlockType( 'skaut-google-drive-gallery/gallery', {
	title: sgdgBlockLocalize.block_name,
	description: sgdgBlockLocalize.block_description,
	category: 'media',
	icon: SgdgBlockIconComponent,
	attributes: {
		path: {
			type: 'array',
			default: [],
		},
		grid_height: {
			type: 'number',
		},
		grid_spacing: {
			type: 'number',
		},
		dir_counts: {
			type: 'string',
		},
		page_size: {
			type: 'number',
		},
		page_autoload: {
			type: 'string',
		},
		image_ordering_order: {
			type: 'string',
		},
		image_ordering_by: {
			type: 'string',
		},
		dir_ordering_order: {
			type: 'string',
		},
		dir_ordering_by: {
			type: 'string',
		},
		preview_size: {
			type: 'number',
		},
		preview_loop: {
			type: 'string',
		},
	},
	edit: SgdgEditorComponent,
	save: renderFrontend,
	transforms: {
		from: [
			{
				type: 'shortcode',
				tag: 'sgdg',
				priority: 15,
				attributes: {
					path: {
						type: 'string',
						shortcode: extractFromShortcode,
					},
				},
			},
		],
	},
} );
