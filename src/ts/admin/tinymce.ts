jQuery( document ).ready( function( $ ) {
	let path: Array<string> = [];

	function tinymceSubmit(): void {
		if ( $( '#sgdg-tinymce-insert' ).attr( 'disabled' ) ) {
			return;
		}
		tinymce.activeEditor.insertContent( '[sgdg path="' + path.join( '/' ) + '"]' );
		tb_remove();
	}

	function tinymceHtml(): void {
		let html = '<div id="sgdg-tinymce-overflow">';
		html += '<table id="sgdg-tinymce-table" class="widefat">';
		html += '<thead>';
		html += '<tr>';
		html += '<th class="sgdg-tinymce-path">' + sgdgTinymceLocalize.root_name + '</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody id="sgdg-tinymce-list"></tbody>';
		html += '<tfoot>';
		html += '<tr>';
		html += '<td class="sgdg-tinymce-path">' + sgdgTinymceLocalize.root_name + '</td>';
		html += '</tr>';
		html += '</tfoot>';
		html += '</table>';
		html += '</div>';
		html += '<div class="sgdg-tinymce-footer">';
		html += '<a id="sgdg-tinymce-insert" class="button button-primary">' + sgdgTinymceLocalize.insert_button + '</a>';
		html += '</div>';
		$( '#sgdg-tinymce-modal' ).html( html );
		$( '#sgdg-tinymce-insert' ).click( function() {
			tinymceSubmit();
		} );
	}

	function pathClick( this: HTMLElement ): void {
		path = path.slice( 0, path.indexOf( $( this ).data( 'name' ) ) + 1 );
		ajaxQuery(); // eslint-disable-line @typescript-eslint/no-use-before-define
	}

	function click( this: HTMLElement ): void {
		const newDir = $( this ).text();
		if ( '..' === newDir ) {
			path.pop();
		} else {
			path.push( newDir );
		}
		ajaxQuery(); // eslint-disable-line @typescript-eslint/no-use-before-define
	}

	function success( data: Array<string> ): void {
		let html = '';
		let len = data.length;
		$( '#sgdg-tinymce-insert' ).removeAttr( 'disabled' );
		if ( 0 < path.length ) {
			html += '<tr><td class="row-title"><label>..</label></td></tr>';
		}
		for ( let i = 0; i < len; i++ ) {
			html += '<tr class="';
			if ( ( 0 === path.length && 1 === i % 2 ) || ( 0 < path.length && 0 === i % 2 ) ) {
				html += 'alternate';
			}
			html += '"><td class="row-title"><label>' + data[ i ] + '</label></td></tr>';
		}
		$( '#sgdg-tinymce-list' ).html( html );
		html = '<a>' + sgdgTinymceLocalize.root_name + '</a>';
		len = path.length;
		for ( let i = 0; i < len; i++ ) {
			html += ' > ';
			html += '<a data-name="' + path[ i ] + '">' + path[ i ] + '</a>';
		}
		$( '.sgdg-tinymce-path' ).html( html );
		$( '.sgdg-tinymce-path a' ).click( pathClick );
		$( '#sgdg-tinymce-list label' ).click( click );
	}

	function error( message: string ): void {
		const html = '<div class="notice notice-error"><p>' + message + '</p></div>';
		$( '#TB_ajaxContent' ).html( html );
	}

	function isError( data: ListGalleryDirResponse ): data is ErrorResponse {
		return ( data as ErrorResponse ).error !== undefined;
	}

	function ajaxQuery(): void {
		$( '#sgdg-tinymce-list' ).html( '' );
		$( '#sgdg-tinymce-insert' ).attr( 'disabled', 'disabled' );
		$.get( sgdgTinymceLocalize.ajax_url, {
			_ajax_nonce: sgdgTinymceLocalize.nonce, // eslint-disable-line @typescript-eslint/camelcase
			action: 'list_gallery_dir',
			path,
		}, function( data: ListGalleryDirResponse ) {
			if ( isError( data ) ) {
				error( data.error );
			} else {
				success( data.directories );
			}
		} );
	}

	function tinymceOnclick(): void {
		tinymceHtml();
		tb_show( sgdgTinymceLocalize.dialog_title, '#TB_inline?inlineId=sgdg-tinymce-modal' );
		path = [];
		ajaxQuery();
	}

	function init(): void {
		const html = '<div id="sgdg-tinymce-modal"></div>';

		$( '#sgdg-tinymce-button' ).click( tinymceOnclick );
		$( 'body' ).append( html );
	}
	init();
} );
