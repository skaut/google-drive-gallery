/* exported SgdgIntegerSettingsComponent */

class SgdgIntegerSettingsComponent extends SgdgSettingsComponent {
	protected renderInput(): React.ReactNode {
		const el = wp.element.createElement;
		const disabled =
			undefined === this.props.editor.getAttribute( this.props.name );
		return el( 'input', {
			className:
				'sgdg-block-settings-integer components-range-control__number',
			disabled,
			onChange: ( e: React.FormEvent< Element > ) => {
				this.change( e );
			},
			placeholder: sgdgBlockLocalize[ this.props.name ].default,
			type: 'number',
			value: this.state.value,
		} );
	}

	protected getValue( element: EventTarget ): number | undefined {
		const value = parseInt( ( element as HTMLInputElement ).value );
		if ( isNaN( value ) ) {
			return undefined;
		}
		return value;
	}
}
