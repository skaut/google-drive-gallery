jQuery( document ).ready( function( $ ) {
	function resetWarn( message: string ): void {
		const html = '<div class="notice notice-warning"><p>' + message + '</p></div>';
		$( html ).insertBefore( '.sgdg_root_selection' );
	}

	function pathClick( path: Array<string>, el: HTMLElement ): void {
		const stop = $( el ).data( 'id' );
		listGdriveDir( path.slice( 0, path.indexOf( stop ) + 1 ) ); // eslint-disable-line @typescript-eslint/no-use-before-define
	}

	function click( path: Array<string>, el: HTMLElement ): void {
		const newId = $( el ).data( 'id' );
		if ( newId ) {
			path.push( newId );
		} else {
			path.pop();
		}
		listGdriveDir( path ); // eslint-disable-line @typescript-eslint/no-use-before-define
	}

	function success( path: Array<string>, data: ListGdriveDirSuccessResponse ): void {
		let html = '';
		let len = data.directories.length;
		if ( 0 < path.length ) {
			html += '<tr><td class="row-title"><label>..</label></td></tr>';
		}
		for ( let i = 0; i < len; i++ ) {
			html += '<tr class="';
			if ( ( 0 === path.length && 1 === i % 2 ) || ( 0 < path.length && 0 === i % 2 ) ) {
				html += 'alternate';
			}
			html += '"><td class="row-title"><label data-id="' + data.directories[ i ].id + '">' + data.directories[ i ].name + '</label></td></tr>';
		}
		$( '#sgdg_root_selection_body' ).html( html );
		html = '';
		if ( 0 === path.length ) {
			html = sgdgRootpathLocalize.drive_list;
		} else {
			$( '#submit' ).removeAttr( 'disabled' );
		}
		len = path.length;
		for ( let i = 0; i < len; i++ ) {
			if ( 0 < i ) {
				html += ' > ';
			}
			html += '<a data-id="' + path[ i ] + '">' + data.path[ i ] + '</a>';
		}
		$( '.sgdg_root_selection_path' ).html( html );
		$( '.sgdg_root_selection_path a' ).click( function() {
			pathClick( path, this );
		} );
		$( '#sgdg_root_selection_body label' ).click( function() {
			click( path, this );
		} );
		$( '#sgdg_root_path' ).val( JSON.stringify( path ) );
	}

	function error( message: string ): void {
		const html = '<div class="notice notice-error"><p>' + message + '</p></div>';
		$( '.sgdg_root_selection' ).replaceWith( html );
	}

	function isError( data: ListGdriveDirResponse ): data is ErrorResponse {
		return ( data as ErrorResponse ).error !== undefined;
	}

	function listGdriveDir( path: Array<string> ): void {
		$( '#sgdg_root_selection_body' ).html( '' );
		$( '#submit' ).attr( 'disabled', 'disabled' );
		$.get( sgdgRootpathLocalize.ajax_url, {
			_ajax_nonce: sgdgRootpathLocalize.nonce, // eslint-disable-line @typescript-eslint/camelcase
			action: 'list_gdrive_dir',
			path,
		}, function( data: ListGdriveDirResponse ) {
			if ( isError( data ) ) {
				error( data.error );
				return;
			}
			if ( data.resetWarn ) {
				path = [];
				resetWarn( data.resetWarn );
			}
			if ( data.directories ) {
				success( path, data );
			}
		} );
	}

	listGdriveDir( sgdgRootpathLocalize.root_dir );
} );
