function renderFrontend(): null {
	return null;
}

function extractFromShortcode( attributes: ShortcodeToBlockTransformAttributes ): Array<string> {
	if ( ! attributes.named.path ) {
		return [];
	}
	return attributes.named.path.trim().replace( /^\/+|\/+$/g, '' ).split( '/' );
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
		grid_height: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'number',
		},
		grid_spacing: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'number',
		},
		dir_counts: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'string',
		},
		page_size: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'number',
		},
		page_autoload: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'string',
		},
		image_ordering_order: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'string',
		},
		image_ordering_by: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'string',
		},
		dir_ordering_order: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'string',
		},
		dir_ordering_by: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'string',
		},
		preview_size: { // eslint-disable-line @typescript-eslint/camelcase
			type: 'number',
		},
		preview_loop: { // eslint-disable-line @typescript-eslint/camelcase
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
