/* exported SgdgSettingsOverrideComponent */

interface SgdgSettingsOverrideComponentProps {
	block: SgdgEditorComponent;
}

class SgdgSettingsOverrideComponent extends wp.element.Component<SgdgSettingsOverrideComponentProps, {}> {
	private block: SgdgEditorComponent;

	public constructor( props: SgdgSettingsOverrideComponentProps ) {
		super( props );
		this.block = props.block;
	}

	public render(): React.ReactNode {
		const el = wp.element.createElement;
		return el( wp.components.PanelBody, { title: sgdgBlockLocalize.settings_override, className: 'sgdg-block-settings', children: [
			el( 'h3', null, sgdgBlockLocalize.grid_section_name ),
			el( SgdgIntegerSettingsComponent, { block: this.block, name: 'grid_height' } ),
			el( SgdgIntegerSettingsComponent, { block: this.block, name: 'grid_spacing' } ),
			el( SgdgBooleanSettingsComponent, { block: this.block, name: 'dir_counts' } ),
			el( SgdgIntegerSettingsComponent, { block: this.block, name: 'page_size' } ),
			el( SgdgBooleanSettingsComponent, { block: this.block, name: 'page_autoload' } ),
			el( SgdgOrderingSettingsComponent, { block: this.block, name: 'image_ordering' } ),
			el( SgdgOrderingSettingsComponent, { block: this.block, name: 'dir_ordering' } ),
			el( 'h3', null, sgdgBlockLocalize.lightbox_section_name ),
			el( SgdgIntegerSettingsComponent, { block: this.block, name: 'preview_size' } ),
			el( SgdgBooleanSettingsComponent, { block: this.block, name: 'preview_loop' } ),
		] } );
	}
}
