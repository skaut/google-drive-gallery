/* exported SgdgEditorComponent */

type SgdgEditorComponentProps = import('wordpress__blocks').BlockEditProps<
	Attributes
>;

interface SgdgEditorComponentState {
	error?: string;
	list?: Array< string >;
}

class SgdgEditorComponent extends wp.element.Component<
	SgdgEditorComponentProps,
	SgdgEditorComponentState
> {
	public constructor( props: SgdgEditorComponentProps ) {
		super( props );
		this.state = { error: undefined, list: undefined };
	}

	public componentDidMount(): void {
		this.ajax();
	}

	public render(): React.ReactNode {
		const el = wp.element.createElement;
		if ( this.state.error ) {
			return el(
				'div',
				{ class: 'notice notice-error' },
				el( 'p', null, this.state.error )
			);
		}
		const children = [];
		const path = this.getAttribute( 'path' ) as Array< string >;
		const pathElements: Array< React.ReactNode > = [
			el(
				'a',
				{
					onClick: ( e: Event ) => {
						this.pathClick( e );
					},
				},
				sgdgBlockLocalize.root_name
			),
		];
		if ( this.state.list ) {
			if ( 0 < path.length ) {
				children.push(
					el(
						'tr',
						null,
						el(
							'td',
							{ class: 'row-title' },
							el(
								'label',
								{
									onClick: ( e: Event ) => {
										this.labelClick( e );
									},
								},
								'..'
							)
						)
					)
				);
			}
			for ( let i = 0; i < this.state.list.length; i++ ) {
				const lineClass =
					( 0 === path.length && 1 === i % 2 ) ||
					( 0 < path.length && 0 === i % 2 )
						? 'alternate'
						: '';
				children.push(
					el(
						'tr',
						{ class: lineClass },
						el(
							'td',
							{ class: 'row-title' },
							el(
								'label',
								{
									onClick: ( e: Event ) => {
										this.labelClick( e );
									},
								},
								this.state.list[ i ]
							)
						)
					)
				);
			}
			for ( let i = 0; i < path.length; i++ ) {
				pathElements.push( ' > ' );
				pathElements.push(
					el(
						'a',
						{
							'data-id': path[ i ],
							onClick: ( e: Event ) => {
								this.pathClick( e );
							},
						},
						path[ i ]
					)
				);
			}
		}
		return el( wp.element.Fragment, null, [
			el(
				wp.editor.InspectorControls,
				null,
				el( SgdgSettingsOverrideComponent, { editor: this } )
			),
			el( 'table', { class: 'widefat' }, [
				el(
					'thead',
					null,
					el(
						'tr',
						null,
						el(
							'th',
							{ class: 'sgdg-block-editor-path' },
							pathElements
						)
					)
				),
				el( 'tbody', null, children ),
				el(
					'tfoot',
					null,
					el(
						'tr',
						null,
						el(
							'th',
							{ class: 'sgdg-block-editor-path' },
							pathElements
						)
					)
				),
			] ),
		] );
	}

	public getAttribute(
		name: string
	): number | string | Array< string > | undefined {
		return this.props.attributes[ name ];
	}

	public setAttribute(
		name: string,
		value: number | string | Array< string > | undefined
	): void {
		const attr: Attributes = {};
		attr[ name ] = value;
		this.props.setAttributes( attr );
	}

	private ajax(): void {
		$.get(
			sgdgBlockLocalize.ajax_url,
			{
				_ajax_nonce: sgdgBlockLocalize.nonce, // eslint-disable-line @typescript-eslint/camelcase
				action: 'list_gallery_dir',
				path: this.getAttribute( 'path' ),
			},
			( data: ListGalleryDirResponse ) => {
				if ( isError( data ) ) {
					this.setState( { error: data.error } );
				} else {
					this.setState( { list: data.directories } );
				}
			}
		);
	}

	private pathClick( e: Event ): void {
		let path = this.getAttribute( 'path' ) as Array< string >;
		path = path.slice(
			0,
			path.indexOf( $( e.currentTarget! ).data( 'id' ) ) + 1
		);
		this.setAttribute( 'path', path );
		this.setState( { error: undefined, list: undefined }, () =>
			this.ajax()
		);
	}

	private labelClick( e: Event ): void {
		const newDir = $( e.currentTarget! ).text();
		let path = this.getAttribute( 'path' ) as Array< string >;
		if ( '..' === newDir ) {
			path = path.slice( 0, path.length - 1 );
		} else {
			path = path.concat( newDir );
		}
		this.setAttribute( 'path', path );
		this.setState( { error: undefined, list: undefined }, () =>
			this.ajax()
		);
	}
}
