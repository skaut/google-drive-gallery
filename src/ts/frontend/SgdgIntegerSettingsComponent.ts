/* exported SgdgIntegerSettingsComponent */

class SgdgIntegerSettingsComponent extends SgdgSettingsComponent {
	protected renderInput(): React.ReactNode {
		const el = wp.element.createElement;
		const value = this.block.getAttribute( this.name );
		return el( 'input', { className: 'sgdg-block-settings-integer components-range-control__number', disabled: undefined === value, onChange: ( e: React.FormEvent<Element> ) => {
			this.change( e );
		}, placeholder: sgdgBlockLocalize[ this.name ].default, type: 'number', value: this.state.value } );
	}

	protected getValue( element: EventTarget ): number|undefined {
		const value = parseInt( ( element as HTMLInputElement ).value );
		if ( isNaN( value ) ) {
			return undefined;
		}
		return value;
	}
}
