/* exported SgdgSettingsOverrideComponent */

interface SgdgSettingsOverrideComponentProps {
	editor: SgdgEditorComponent;
}

class SgdgSettingsOverrideComponent extends wp.element
	.Component< SgdgSettingsOverrideComponentProps > {
	public render(): React.ReactNode {
		const el = wp.element.createElement;
		return el( wp.components.PanelBody, {
			title: sgdgBlockLocalize.settings_override,
			className: 'sgdg-block-settings',
			children: [
				el( 'h3', null, sgdgBlockLocalize.grid_section_name ),
				el( SgdgIntegerSettingsComponent, {
					editor: this.props.editor,
					name: 'grid_height',
				} ),
				el( SgdgIntegerSettingsComponent, {
					editor: this.props.editor,
					name: 'grid_spacing',
				} ),
				el( SgdgBooleanSettingsComponent, {
					editor: this.props.editor,
					name: 'dir_counts',
				} ),
				el( SgdgIntegerSettingsComponent, {
					editor: this.props.editor,
					name: 'page_size',
				} ),
				el( SgdgBooleanSettingsComponent, {
					editor: this.props.editor,
					name: 'page_autoload',
				} ),
				el( SgdgOrderingSettingsComponent, {
					editor: this.props.editor,
					name: 'image_ordering',
				} ),
				el( SgdgOrderingSettingsComponent, {
					editor: this.props.editor,
					name: 'dir_ordering',
				} ),
				el( 'h3', null, sgdgBlockLocalize.lightbox_section_name ),
				el( SgdgIntegerSettingsComponent, {
					editor: this.props.editor,
					name: 'preview_size',
				} ),
				el( SgdgBooleanSettingsComponent, {
					editor: this.props.editor,
					name: 'preview_loop',
				} ),
			],
		} );
	}
}
