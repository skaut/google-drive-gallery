/* exported SgdgSettingsComponent */

interface SgdgSettingsComponentProps {
	block: SgdgEditorComponent;
	name: BlockOptions;
}

interface SgdgSettingsComponentState {
	value: number|string|undefined;
}

abstract class SgdgSettingsComponent extends wp.element.Component<SgdgSettingsComponentProps, SgdgSettingsComponentState> {
	protected block: SgdgEditorComponent;
	protected name: BlockOptions;

	public constructor( props: SgdgSettingsComponentProps ) {
		super( props );
		this.block = props.block;
		this.name = props.name;
		let value = this.block.getAttribute( this.name ) as string|undefined;
		if ( undefined === value ) {
			value = sgdgBlockLocalize[ this.name ].default;
		}
		this.state = { value };
	}

	public render(): React.ReactNode {
		const el = wp.element.createElement;
		const value = this.block.getAttribute( this.name );
		return el( 'div', { className: 'sgdg-block-settings-row ' }, [
			el( wp.components.ToggleControl, { checked: undefined !== value, className: 'sgdg-block-settings-checkbox', onChange: () => {
				this.toggle();
			} } ),
			el( 'span', { className: 'sgdg-block-settings-description' }, [
				sgdgBlockLocalize[ this.name ].name,
				':',
			] ),
			this.renderInput(),
		] );
	}

	protected change( e: React.FormEvent<Element> ): void {
		const value = this.getValue( e.target );
		this.setState( { value } );
		this.block.setAttribute( this.name, undefined === value ? sgdgBlockLocalize[ this.name ].default : value );
	}

	private toggle(): void {
		this.block.setAttribute( this.name, undefined !== this.block.getAttribute( this.name ) ? undefined : this.state.value );
	}

	protected abstract renderInput(): void;

	protected abstract getValue( element: EventTarget ): string|number|undefined;
}
