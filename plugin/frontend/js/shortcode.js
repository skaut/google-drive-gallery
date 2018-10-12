'use strict';
jQuery( document ).ready( function( $ ) {
	var loading = [];

	function reflow( element ) {
		var val, bbox, positions, sizes, containerPosition;
		var j = 0;
		var loaded = [];
		var ratios = [];
		element.find( '.sgdg-gallery' ).children().each( function( i ) {
			$( this ).css( 'display', 'inline-block' );
			val = this.firstChild.naturalWidth / this.firstChild.naturalHeight;
			if ( 0 < $( this ).find( 'svg' ).length ) {
				bbox = $( this ).find( 'svg' )[0].getBBox();
				val = bbox.width / bbox.height;
			}
			if ( $( this ).hasClass( 'sgdg-grid-square' ) ) {
				val = 1;
			}
			if ( isNaN( val ) ) {
				loaded[i] = false;
			} else {
				loaded[i] = true;
				ratios.push( val );
			}
			$( this ).css( 'position', 'absolute' );
		});
		if ( 0 < ratios.length ) {
			element.find( '.sgdg-spinner' ).remove();
		}
		positions = require( 'justified-layout' )( ratios, {
			containerWidth: element.find( '.sgdg-gallery' ).width(),
			containerPadding: {top: 10, left: 0, right: 0, bottom: 0},
			boxSpacing: parseInt( sgdgShortcodeLocalize.grid_spacing ),
			targetRowHeight: parseInt( sgdgShortcodeLocalize.grid_height ),
			edgeCaseMinRowHeight: 0
		});
		element.find( '.sgdg-gallery' ).children().each( function( i ) {
			if ( ! loaded[i]) {
				$( this ).css( 'display', 'none' );
				return;
			}
			sizes = positions.boxes[j];
			j++;
			containerPosition = element.find( '.sgdg-gallery' ).position();
			$( this ).css( 'top', sizes.top + containerPosition.top );
			$( this ).css( 'left', sizes.left + containerPosition.left );
			$( this ).width( sizes.width );
			$( this ).height( sizes.height );
		});
		element.find( '.sgdg-gallery' ).height( positions.containerHeight );
	}

	function getQueryPath( hash ) {
		var keyValuePair = new RegExp( '[?&]sgdg-path-' + hash + '=(([^&#]*)|&|#|$)' ).exec( document.location.search );
		if ( ! keyValuePair || ! keyValuePair[2]) {
			return '';
		}
		return decodeURIComponent( keyValuePair[2].replace( /\+/g, ' ' ) );
	}

	function addQueryPath( hash, value ) {
		var query = window.location.search;
		var newField = 'sgdg-path-' + hash + '=' + value;
		var newQuery = '?' + newField;
		var keyRegex = new RegExp( '([?&])sgdg-path-' + hash + '=[^&]*' );
		if ( ! value ) {
			return removeQueryPath( hash );
		}

		if ( query ) {
			if ( null !== query.match( keyRegex ) ) {
				newQuery = query.replace( keyRegex, '$1' + newField );
			} else {
				newQuery = query + '&' + newField;
			}
		}
		return window.location.pathname + newQuery;
	}

	function removeQueryPath( hash ) {
		var newQuery = window.location.search;
		var keyRegex1 = new RegExp( '[?]sgdg-path-' + hash + '=[^&]*' );
		var keyRegex2 = new RegExp( '&sgdg-path-' + hash + '=[^&]*' );
		if ( newQuery ) {
			newQuery = newQuery.replace( keyRegex1, '?' );
			newQuery = newQuery.replace( keyRegex2, '' );
		}
		return window.location.pathname + newQuery;
	}

	function renderBreadcrumbs( hash, path ) {
		var html = '<div><a data-sgdg-path="" href="' + removeQueryPath( hash ) + '">' + sgdgShortcodeLocalize.breadcrumbs_top + '</a>';
		var field = '';
		$.each( path, function( _, crumb ) {
			field += crumb.id + '/';
			html += ' > <a data-sgdg-path="' + field.slice( 0, -1 ) + '" href="' + addQueryPath( hash, field.slice( 0, -1 ) ) + '">' + crumb.name + '</a>';
		});
		html += '</div>';
		return html;
	}

	function renderDirectories( hash, directories ) {
		var html = '';
		$.each( directories, function( _, dir ) {
			var newPath = getQueryPath( hash );
			var iconClass = '';
			newPath = ( newPath ? newPath + '/' : '' ) + dir.id;
			html += '<a class="sgdg-grid-a sgdg-grid-square" data-sgdg-path="' + newPath + '" href="';
			html += addQueryPath( hash, newPath );
			html += '"';
			if ( false !== dir.thumbnail ) {
				html += ' style="background-image: url(\'' + dir.thumbnail + '\');">';
			} else {
				html += '><svg class="sgdg-dir-icon" x="0px" y="0px" focusable="false" viewBox="0 0 24 24" fill="#8f8f8f"><path d="M10 4H4c-1.1 0-2 .9-2 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path></svg>';
			}
			html += '<div class="sgdg-dir-overlay"><div class="sgdg-dir-name">' + dir.name + '</div>';
			if ( dir.dircount ) {
				html += '<span class="sgdg-count-icon dashicons dashicons-category"></span> ' + dir.dircount + ( 1000 === dir.dircount ? '+' : '' );
			}
			if ( dir.imagecount ) {
				if ( dir.dircount ) {
					iconClass = ' sgdg-count-icon-indent';
				}
				html += '<span class="sgdg-count-icon dashicons dashicons-format-image' + iconClass + '"></span> ' + dir.imagecount + ( 1000 === dir.imagecount ? '+' : '' );
			}
			html += '</div></a>';
		});
		return html;
	}

	function renderImages( hash, images ) {
		var html = '';
		$.each( images, function( _, image ) {
			html += '<a class="sgdg-grid-a" data-imagelightbox="' + hash + '"';
			html += 'data-ilb2-id="' + image.id + '"';
			html += ' href="' + image.image + '"><img class="sgdg-grid-img" src="' + image.thumbnail + '"></a>';
		});
		return html;
	}

	function reflowTimer( hash ) {
		reflow( $( '[data-sgdg-hash=' + hash + ']' ) );
		$( '.sgdg-gallery-container[data-sgdg-hash!=' + hash + ']' ).each( function() {
			reflow( $( this ) );
		});
		if ( -1 !== loading.indexOf( hash ) ) {
			setTimeout( function() {
				reflowTimer( hash );
			}, 1000 );
		}
	}

	function get( hash ) {
		var container = $( '[data-sgdg-hash=' + hash + ']' );
		var path = getQueryPath( hash );
		container.data( 'sgdgPath', path );
		container.find( '.sgdg-gallery' ).replaceWith( '<div class="sgdg-spinner"></div>' );
		$( '.sgdg-gallery-container[data-sgdg-hash!=' + hash + ']' ).each( function() {
			reflow( $( this ) );
		});
		$.get( sgdgShortcodeLocalize.ajax_url, {
			action: 'list_dir',
			nonce: $( '[data-sgdg-hash=' + hash + ']' ).data( 'sgdgNonce' ),
			path: path
		}, function( data ) {
			var html = '';
			if ( data.error ) {
				container.html( data.error );
				return;
			}
			if ( ( data.path && 0 < data.path.length ) || 0 < data.directories.length ) {
				html += renderBreadcrumbs( hash, data.path );
			}
			if ( 0 < data.directories.length || 0 < data.images.length ) {
				html += '<div class="sgdg-spinner"></div>';
				html += '<div class="sgdg-gallery">';
				html += renderDirectories( hash, data.directories );
				html += renderImages( hash, data.images );
				html += '</div>';
			} else {
				html += '<div class="sgdg-gallery">' + sgdgShortcodeLocalize.empty_gallery + '</div>';
			}
			container.html( html );
			container.find( 'a[data-sgdg-path]' ).click( function() {
				history.pushState({}, '', addQueryPath( hash, $( this ).data( 'sgdgPath' ) ) );
				get( hash );
				return false;
			});

			loading.push( hash );
			container.find( '.sgdg-gallery' ).imagesLoaded({background: true}, function() {
				loading.splice( loading.indexOf( hash ), 1 );
				reflow( container );
				$( '.sgdg-gallery-container[data-sgdg-hash!=' + hash + ']' ).each( function() {
					reflow( $( this ) );
				});
			});
			reflowTimer( hash );

			container.find( 'a[data-imagelightbox]' ).imageLightbox({
				allowedTypes: '',
				animationSpeed: parseInt( sgdgShortcodeLocalize.preview_speed, 10 ),
				activity: ( 'true' === sgdgShortcodeLocalize.preview_activity ),
				arrows: ( 'true' === sgdgShortcodeLocalize.preview_arrows ),
				button: ( 'true' === sgdgShortcodeLocalize.preview_closebutton ),
				fullscreen: true,
				history: true,
				overlay: true,
				quitOnEnd: ( 'true' === sgdgShortcodeLocalize.preview_quitOnEnd )
			});
		});
	}

	function reinit() {
		$( '.sgdg-gallery-container' ).each( function() {
			var hash = $( this ).data( 'sgdgHash' );
			if ( $( this ).data( 'sgdgPath' ) !== getQueryPath( hash ) ) {
				get( hash );
			}
		});
	}
	$( window ).on( 'popstate', reinit );
	reinit();

	$( window ).resize( function() {
		$( '.sgdg-gallery-container' ).each( function() {
			reflow( $( this ) );
		});
	});
});
