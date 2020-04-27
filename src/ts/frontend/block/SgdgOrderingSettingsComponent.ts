/* exported SgdgOrderingSettingsComponent */

interface SgdgOrderingSettingsComponentProps {
	editor: SgdgEditorComponent;
	name: BlockOrderingOptions;
}

interface SgdgOrderingSettingsComponentState {
	valueBy: string;
	valueOrder: string;
}

class SgdgOrderingSettingsComponent extends wp.element.Component<
	SgdgOrderingSettingsComponentProps,
	SgdgOrderingSettingsComponentState
> {
	public constructor( props: SgdgOrderingSettingsComponentProps ) {
		super( props );
		let valueBy = this.props.editor.getAttribute(
			this.props.name + '_by'
		) as string;
		let valueOrder = this.props.editor.getAttribute(
			this.props.name + '_order'
		) as string;
		if ( undefined === valueBy ) {
			valueBy = sgdgBlockLocalize[ this.props.name ].default_by;
		}
		if ( undefined === valueOrder ) {
			valueOrder = sgdgBlockLocalize[ this.props.name ].default_order;
		}
		this.state = { valueBy, valueOrder };
	}

	public render(): React.ReactNode {
		const el = wp.element.createElement;
		const disabledBy =
			undefined ===
			this.props.editor.getAttribute( this.props.name + '_by' );
		const disabledOrder =
			undefined ===
			this.props.editor.getAttribute( this.props.name + '_order' );
		const valueBy = this.state.valueBy;
		const valueOrder = this.state.valueOrder;
		return el( 'div', { className: 'sgdg-block-settings-row' }, [
			el( wp.components.ToggleControl, {
				checked: ! disabledBy && ! disabledOrder,
				className: 'sgdg-block-settings-checkbox',
				onChange: () => {
					this.toggle();
				},
			} ),
			el( 'span', { className: 'sgdg-block-settings-description' }, [
				sgdgBlockLocalize[ this.props.name ].name,
				':',
			] ),
			el(
				'select',
				{
					className: 'sgdg-block-settings-select',
					disabled: disabledOrder,
					onChange: ( e: React.FormEvent< Element > ) => {
						this.changeOrder( e );
					},
					placeholder:
						sgdgBlockLocalize[ this.props.name ].default_order,
					type: 'number',
					value: valueOrder,
				},
				[
					el(
						'option',
						{
							selected: 'ascending' === valueOrder,
							value: 'ascending',
						},
						sgdgBlockLocalize.ordering_option_ascending
					),
					el(
						'option',
						{
							selected: 'descending' === valueOrder,
							value: 'descending',
						},
						sgdgBlockLocalize.ordering_option_descending
					),
				]
			),
			el(
				'label',
				{
					className: 'sgdg-block-settings-radio',
					for: this.props.name + '_by_time',
				},
				[
					el( 'input', {
						checked: 'time' === valueBy,
						disabled: disabledBy,
						id: this.props.name + '_by_time',
						name: this.props.name + '_by',
						onChange: ( e ) => {
							this.changeBy( e );
						},
						type: 'radio',
						value: 'time',
					} ),
					sgdgBlockLocalize.ordering_option_by_time,
				]
			),
			el(
				'label',
				{
					className: 'sgdg-block-settings-radio',
					for: this.props.name + '_by_name',
				},
				[
					el( 'input', {
						checked: 'name' === valueBy,
						disabled: disabledBy,
						id: this.props.name + '_by_name',
						name: this.props.name + '_by',
						onChange: ( e ) => {
							this.changeBy( e );
						},
						type: 'radio',
						value: 'name',
					} ),
					sgdgBlockLocalize.ordering_option_by_name,
				]
			),
		] );
	}

	private toggle(): void {
		this.props.editor.setAttribute(
			this.props.name + '_by',
			undefined !==
				this.props.editor.getAttribute( this.props.name + '_by' )
				? undefined
				: this.state.valueBy
		);
		this.props.editor.setAttribute(
			this.props.name + '_order',
			undefined !==
				this.props.editor.getAttribute( this.props.name + '_order' )
				? undefined
				: this.state.valueOrder
		);
	}

	private changeBy( e: React.FormEvent< Element > ): void {
		const target = e.target as HTMLInputElement;
		this.setState( { valueBy: target.value } );
		this.props.editor.setAttribute( this.props.name + '_by', target.value );
	}

	private changeOrder( e: React.FormEvent< Element > ): void {
		const target = e.target as HTMLSelectElement;
		this.setState( { valueOrder: target.value } );
		this.props.editor.setAttribute(
			this.props.name + '_order',
			target.value
		);
	}
}
