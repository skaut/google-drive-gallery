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
	category: 'common',
	icon: SgdgBlockIconComponent,
	attributes: {
		path: {
			type: 'array',
			default: [],
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		grid_height: {
			type: 'number',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		grid_spacing: {
			type: 'number',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		dir_counts: {
			type: 'string',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		page_size: {
			type: 'number',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		page_autoload: {
			type: 'string',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		image_ordering_order: {
			type: 'string',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		image_ordering_by: {
			type: 'string',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		dir_ordering_order: {
			type: 'string',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		dir_ordering_by: {
			type: 'string',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
		preview_size: {
			type: 'number',
		},
		// eslint-disable-next-line @typescript-eslint/camelcase
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
