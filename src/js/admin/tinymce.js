'use strict';
jQuery( document ).ready( function( $ ) {
	var path = [];
	var html = '<div id="sgdg-tinymce-modal"></div>';

	$( '#sgdg-tinymce-button' ).click( tinymceOnclick );
	$( 'body' ).append( html );

	function tinymceOnclick() {
		tinymceHtml();
		tb_show( sgdgTinymceLocalize.dialog_title, '#TB_inline?inlineId=sgdg-tinymce-modal' );
		path = [];
		ajaxQuery();
	}
	function tinymceHtml() {
		var html = '<table id="sgdg-tinymce-table" class="widefat">';
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
		html += '<div class="sgdg-tinymce-footer">';
		html += '<a id="sgdg-tinymce-insert" class="button button-primary">' + sgdgTinymceLocalize.insert_button + '</a>';
		html += '</div>';
		$( '#sgdg-tinymce-modal' ).html( html );
		$( '#sgdg-tinymce-insert' ).click( function() {
			tinymceSubmit();
		});
	}

	function tinymceSubmit() {
		if ( $( '#sgdg-tinymce-insert' ).attr( 'disabled' ) ) {
			return;
		}
		tinymce.activeEditor.insertContent( '[sgdg path="' + path.join( '/' ) + '"]' );
		tb_remove();
	}

	function ajaxQuery() {
		$( '#sgdg-tinymce-list' ).html( '' );
		$( '#sgdg-tinymce-insert' ).attr( 'disabled', 'disabled' );
		$.get( sgdgTinymceLocalize.ajax_url, {
			_ajax_nonce: sgdgTinymceLocalize.nonce, // eslint-disable-line camelcase
			action: 'list_gallery_dir',
			path: path
			}, function( data ) {
				if ( data.directories ) {
					success( data.directories );
				} else if ( data.error ) {
					error( data.error );
				}
			}
		);
	}

	function success( data ) {
		var i;
		var html = '';
		var len = data.length;
		$( '#sgdg-tinymce-insert' ).removeAttr( 'disabled' );
		if ( 0 < path.length ) {
			html += '<tr><td class="row-title"><label>..</label></td></tr>';
		}
		for ( i = 0; i < len; i++ ) {
			html += '<tr class="';
			if ( ( 0 === path.length && 1 === i % 2 ) || ( 0 < path.length && 0 === i % 2 ) ) {
				html += 'alternate';
			}
			html += '"><td class="row-title"><label>' + data[i] + '</label></td></tr>';
		}
		$( '#sgdg-tinymce-list' ).html( html );
		html = '<a>' + sgdgTinymceLocalize.root_name + '</a>';
		len = path.length;
		for ( i = 0; i < len; i++ ) {
			html += ' > ';
			html += '<a data-name="' + path[i] + '">' + path[i] + '</a>';
		}
		$( '.sgdg-tinymce-path' ).html( html );
		$( '.sgdg-tinymce-path a' ).click( pathClick );
		$( '#sgdg-tinymce-list label' ).click( click );
	}

	function pathClick() {
		path = path.slice( 0, path.indexOf( $( this ).data( 'name' ) ) + 1 );
		ajaxQuery();
	}

	function click() {
		var newDir = $( this ).html();
		if ( '..' === newDir ) {
			path.pop();
		} else {
			path.push( newDir );
		}
		ajaxQuery();
	}

	function error( message ) {
		var html = '<div class="notice notice-error"><p>' + message + '</p></div>';
		$( '#TB_ajaxContent' ).html( html );
	}
});
