/* exported SgdgBooleanSettingsComponent */

class SgdgBooleanSettingsComponent extends SgdgSettingsComponent {
	protected renderInput(): React.ReactNode {
		const el = wp.element.createElement;
		const disabled =
			undefined === this.props.editor.getAttribute( this.props.name );
		return el( 'input', {
			checked: 'true' === this.state.value,
			className: 'sgdg-block-settings-boolean',
			disabled,
			onChange: ( e: React.FormEvent< Element > ) => {
				this.change( e );
			},
			type: 'checkbox',
		} );
	}

	protected getValue( element: EventTarget ): string {
		return ( element as HTMLInputElement ).checked ? 'true' : 'false';
	}
}
