import justifiedLayout = require( 'justified-layout' );

jQuery( document ).ready( function( $ ) {
	const loading: Array<string> = [];
	const lightboxes: Record<string, JQuery> = {};

	function reflow( element: JQuery ): void {
		let j = 0;
		const loaded: Array<boolean> = [];
		const ratios: Array<number> = [];
		let bbox, sizes, val;
		element.find( '.sgdg-gallery' ).children().each( function( i ) {
			$( this ).css( 'display', 'inline-block' );
			val = ( this.firstChild as HTMLImageElement ).naturalWidth / ( this.firstChild as HTMLImageElement ).naturalHeight;
			if ( 0 < $( this ).find( 'svg' ).length ) {
				bbox = ( $( this ).find( 'svg' )[ 0 ] as unknown as SVGGraphicsElement ).getBBox();
				val = bbox.width / bbox.height;
			}
			if ( $( this ).hasClass( 'sgdg-grid-square' ) ) {
				val = 1;
			}
			if ( isNaN( val ) ) {
				loaded[ i ] = false;
			} else {
				loaded[ i ] = true;
				ratios.push( val );
			}
			$( this ).css( 'position', 'absolute' );
		} );
		if ( 0 < ratios.length ) {
			element.find( '.sgdg-loading' ).remove();
		}
		const positions = justifiedLayout( ratios, {
			containerWidth: element.find( '.sgdg-gallery' ).width(),
			containerPadding: { top: 10, left: 0, right: 0, bottom: 0 },
			boxSpacing: parseInt( sgdgShortcodeLocalize.grid_spacing ),
			targetRowHeight: parseInt( sgdgShortcodeLocalize.grid_height ),
			targetRowHeightTolerance: 0.15,
			edgeCaseMinRowHeight: 0,
		} );
		element.find( '.sgdg-gallery' ).children().each( function( i ) {
			if ( ! loaded[ i ] ) {
				$( this ).css( 'display', 'none' );
				return;
			}
			sizes = positions.boxes[ j ];
			j++;
			const containerPosition = element.find( '.sgdg-gallery' ).position();
			$( this ).css( 'top', sizes.top + containerPosition.top );
			$( this ).css( 'left', sizes.left + containerPosition.left );
			$( this ).width( sizes.width );
			$( this ).height( sizes.height );
		} );
		element.find( '.sgdg-gallery' ).height( positions.containerHeight );
	}

	function getQueryParameter( hash: string, name: string ): string {
		const keyValuePair = new RegExp( '[?&]sgdg-' + name + '-' + hash + '=(([^&#]*)|&|#|$)' ).exec( document.location.search );
		if ( ! keyValuePair || ! keyValuePair[ 2 ] ) {
			return '';
		}
		return decodeURIComponent( keyValuePair[ 2 ].replace( /\+/g, ' ' ) );
	}

	function removeQueryParameter( hash: string, name: string ): string {
		let newQuery = window.location.search;
		const keyRegex1 = new RegExp( '\\?sgdg-' + name + '-' + hash + '=[^&]*' );
		const keyRegex2 = new RegExp( '&sgdg-' + name + '-' + hash + '=[^&]*' );
		if ( newQuery ) {
			newQuery = newQuery.replace( keyRegex1, '?' );
			newQuery = newQuery.replace( keyRegex2, '' );
		}
		return window.location.pathname + newQuery;
	}

	function addQueryParameter( hash: string, name: string, value: string ): string {
		const query = window.location.search;
		const newField = 'sgdg-' + name + '-' + hash + '=' + value;
		let newQuery = '?' + newField;
		const keyRegex = new RegExp( '([?&])sgdg-' + name + '-' + hash + '=[^&]*' );
		if ( ! value ) {
			return removeQueryParameter( hash, name );
		}

		if ( query ) {
			if ( null !== keyRegex.exec( query ) ) {
				newQuery = query.replace( keyRegex, '$1' + newField );
			} else {
				newQuery = query + '&' + newField;
			}
		}
		return window.location.pathname + newQuery;
	}

	function renderBreadcrumbs( hash: string, path: Array<PartialDirectory> ): string {
		let html = '<div><a data-sgdg-path="" href="' + removeQueryParameter( hash, 'path' ) + '">' + sgdgShortcodeLocalize.breadcrumbs_top + '</a>';
		let field = '';
		$.each( path, function( _, crumb ) {
			field += crumb.id + '/';
			html += ' > <a data-sgdg-path="' + field.slice( 0, -1 ) + '" href="' + addQueryParameter( hash, 'path', field.slice( 0, -1 ) ) + '">' + crumb.name + '</a>';
		} );
		html += '</div>';
		return html;
	}

	function renderDirectory( hash: string, directory: Directory ): string {
		let html = '';
		let newPath = getQueryParameter( hash, 'path' );
		let iconClass = '';
		newPath = ( newPath ? newPath + '/' : '' ) + directory.id;
		html += '<a class="sgdg-grid-a sgdg-grid-square" data-sgdg-path="' + newPath + '" href="';
		html += addQueryParameter( hash, 'path', newPath );
		html += '"';
		if ( directory.thumbnail ) {
			html += ' style="background-image: url(\'' + directory.thumbnail + '\');">';
		} else {
			html += '><svg class="sgdg-dir-icon" x="0px" y="0px" focusable="false" viewBox="0 0 24 24" fill="#8f8f8f"><path d="M10 4H4c-1.1 0-2 .9-2 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path></svg>';
		}
		html += '<div class="sgdg-dir-overlay"><div class="sgdg-dir-name">' + directory.name + '</div>';
		if ( directory.dircount ) {
			html += '<span class="sgdg-count-icon dashicons dashicons-category"></span> ' + directory.dircount + ( 1000 === directory.dircount ? '+' : '' );
		}
		if ( directory.imagecount ) {
			if ( directory.dircount ) {
				iconClass = ' sgdg-count-icon-indent';
			}
			html += '<span class="sgdg-count-icon dashicons dashicons-format-image' + iconClass + '"></span> ' + directory.imagecount + ( 1000 === directory.imagecount ? '+' : '' );
		}
		iconClass = '';
		if ( directory.videocount ) {
			if ( directory.dircount || directory.imagecount ) {
				iconClass = ' sgdg-count-icon-indent';
			}
			html += '<span class="sgdg-count-icon dashicons dashicons-video-alt3' + iconClass + '"></span> ' + directory.videocount;
		}
		html += '</div></a>';
		return html;
	}

	function renderImage( hash: string, page: number, image: Image ): string {
		let html = '<a class="sgdg-grid-a" data-imagelightbox="' + hash + '"';
		html += 'data-ilb2-id="' + image.id + '"';
		html += 'data-ilb2-caption="' + image.description + '"';
		html += 'data-sgdg-page="' + page + '"';
		html += ' href="' + image.image + '"><img class="sgdg-grid-img" src="' + image.thumbnail + '"></a>';
		return html;
	}

	function renderVideo( hash: string, page: number, video: Video ): string {
		let html = '<a class="sgdg-grid-a" data-imagelightbox="' + hash + '"';
		html += 'data-ilb2-id="' + video.id + '"';
		html += 'data-sgdg-page="' + page + '"';
		html += ' data-ilb2-video=\'' + JSON.stringify( { controls: 'controls', autoplay: 'autoplay', sources: [ { src: video.src, type: video.mimeType } ] } ) + '\'>';
		html += '<img class="sgdg-grid-img" src="' + video.thumbnail + '">';
		html += '</a>';
		return html;
	}

	function renderMoreButton(): string {
		return '<div class="sgdg-more-button"><div>' + sgdgShortcodeLocalize.load_more + '</div></div>';
	}

	function reflowTimer( hash: string ): void {
		reflow( $( '[data-sgdg-hash=' + hash + ']' ) );
		$( '.sgdg-gallery-container[data-sgdg-hash!=' + hash + ']' ).each( function() {
			reflow( $( this ) );
		} );
		if ( -1 !== loading.indexOf( hash ) ) {
			setTimeout( function() {
				reflowTimer( hash );
			}, 1000 );
		}
	}

	function postLoad( hash: string, page: number ): void {
		const container = $( '[data-sgdg-hash=' + hash + ']' );
		container.find( 'a[data-sgdg-path]' ).off( 'click' ).click( function() {
			history.pushState( {}, '', addQueryParameter( hash.substr( 0, 8 ), 'path', $( this ).data( 'sgdgPath' ) ) );
			get( hash ); // eslint-disable-line @typescript-eslint/no-use-before-define
			return false;
		} );
		container.find( '.sgdg-more-button' ).click( function() {
			add( hash, page + 1 ); // eslint-disable-line @typescript-eslint/no-use-before-define
			return false;
		} );

		loading.push( hash );
		container.find( '.sgdg-gallery' ).imagesLoaded( { background: true }, function() {
			loading.splice( loading.indexOf( hash ), 1 );
			reflow( container );
			$( '.sgdg-gallery-container[data-sgdg-hash!=' + hash + ']' ).each( function() {
				reflow( $( this ) );
			} );
		} );
		reflowTimer( hash );

		lightboxes[ hash ].addToImageLightbox( container.find( 'a[data-imagelightbox]' ) );
		if ( 'true' === sgdgShortcodeLocalize.page_autoload ) {
			$( window ).off( 'scroll' ).scroll( function() {
				const el = $( '.sgdg-more-button' );
				if ( undefined === el.offset() ) {
					return;
				}
				const inView = $( this ).scrollTop()! + $( window ).height()! > el.offset()!.top + el.outerHeight()!;
				if ( inView && -1 === loading.indexOf( hash ) ) {
					add( hash, page + 1 ); // eslint-disable-line @typescript-eslint/no-use-before-define
				}
			} );
		}
	}

	function isError( data: GalleryResponse|PageResponse ): data is ErrorResponse {
		return ( data as ErrorResponse ).error !== undefined;
	}

	function get( hash: string ): void {
		const shortHash = hash.substr( 0, 8 );
		const container = $( '[data-sgdg-hash=' + hash + ']' );
		const path = getQueryParameter( shortHash, 'path' );
		const page = parseInt( getQueryParameter( shortHash, 'page' ) ) || 1;
		lightboxes[ hash ] = $().imageLightbox( {
			allowedTypes: '',
			animationSpeed: parseInt( sgdgShortcodeLocalize.preview_speed, 10 ),
			activity: ( 'true' === sgdgShortcodeLocalize.preview_activity ),
			arrows: ( 'true' === sgdgShortcodeLocalize.preview_arrows ),
			button: ( 'true' === sgdgShortcodeLocalize.preview_closebutton ),
			fullscreen: true,
			history: true,
			overlay: true,
			caption: ( 'true' === sgdgShortcodeLocalize.preview_captions ),
			quitOnEnd: ( 'true' === sgdgShortcodeLocalize.preview_quitOnEnd ),
		} );
		container.data( 'sgdgPath', path );
		container.data( 'sgdgLastPage', '1' );
		container.find( '.sgdg-gallery' ).replaceWith( '<div class="sgdg-loading"><div></div></div>' );
		container.find( '.sgdg-more-button' ).remove();
		$( '.sgdg-gallery-container[data-sgdg-hash!=' + hash + ']' ).each( function() {
			reflow( $( this ) );
		} );
		$.get( sgdgShortcodeLocalize.ajax_url, {
			action: 'gallery',
			hash,
			path,
			page,
		}, function( data: GalleryResponse ) {
			if ( isError( data ) ) {
				container.html( data.error );
				return;
			}
			const pageLength = ( ( data.directories ? data.directories.length : 0 ) + ( data.images ? data.images.length : 0 ) + ( data.videos ? data.videos.length : 0 ) ) / page;
			let html = '';
			let currentPage = 1;
			let remaining = pageLength;
			if ( ( data.path && 0 < data.path.length ) || 0 < data.directories.length ) {
				html += renderBreadcrumbs( shortHash, data.path );
			}
			if ( 0 < data.directories.length || 0 < data.images.length || 0 < data.videos.length ) {
				html += '<div class="sgdg-loading"><div></div></div>';
				html += '<div class="sgdg-gallery">';
				if ( data.directories ) {
					$.each( data.directories, function( _, directory ) {
						html += renderDirectory( shortHash, directory );
						remaining--;
						if ( 0 === remaining ) {
							remaining = pageLength;
							currentPage++;
						}
					} );
				}
				if ( data.images ) {
					$.each( data.images, function( _, image ) {
						html += renderImage( shortHash, currentPage, image );
						remaining--;
						if ( 0 === remaining ) {
							remaining = pageLength;
							currentPage++;
						}
					} );
				}
				if ( data.videos ) {
					$.each( data.videos, function( _, video ) {
						if ( '' !== document.createElement( 'video' ).canPlayType( video.mimeType ) ) {
							html += renderVideo( shortHash, currentPage, video );
						}
						remaining--;
						if ( 0 === remaining ) {
							remaining = pageLength;
							currentPage++;
						}
					} );
				}
				html += '</div>';
				if ( data.more ) {
					html += renderMoreButton();
				}
			} else {
				html += '<div class="sgdg-gallery">' + sgdgShortcodeLocalize.empty_gallery + '</div>';
			}
			container.html( html );
			container.data( 'sgdgHasMore', data.more );
			postLoad( hash, page );
			lightboxes[ hash ].openHistory();
		} );
	}

	function add( hash: string, page: number ): void {
		const shortHash = hash.substr( 0, 8 );
		const container = $( '[data-sgdg-hash=' + hash + ']' );
		if ( page <= container.data( 'sgdgLastPage' ) ) {
			return;
		}
		container.data( 'sgdgLastPage', page );
		container.find( '.sgdg-gallery' ).after( '<div class="sgdg-loading"><div></div></div>' );
		container.find( '.sgdg-more-button' ).remove();
		$.get( sgdgShortcodeLocalize.ajax_url, {
			action: 'page',
			hash,
			path: getQueryParameter( shortHash, 'path' ),
			page,
		}, function( data: PageResponse ) {
			if ( isError( data ) ) {
				container.find( '.sgdg-loading' ).replaceWith( data.error );
				container.find( '.sgdg-more-button' ).remove();
				return;
			}
			let html = '';
			$.each( data.directories, function( _, directory ) {
				html += renderDirectory( shortHash, directory );
			} );
			$.each( data.images, function( _, image ) {
				html += renderImage( shortHash, page, image );
			} );
			$.each( data.videos, function( _, video ) {
				html += renderVideo( shortHash, page, video );
			} );
			container.find( '.sgdg-gallery' ).append( html );
			container.data( 'sgdgHasMore', data.more );
			if ( data.more ) {
				container.append( renderMoreButton() );
			}
			container.find( '.sgdg-loading' ).remove();
			postLoad( hash, page );
		} );
	}

	function reinit(): void {
		$( '.sgdg-gallery-container' ).each( function() {
			const hash = $( this ).data( 'sgdgHash' );
			if ( $( this ).data( 'sgdgPath' ) !== getQueryParameter( hash.substr( 0, 8 ), 'path' ) ) {
				get( hash );
			}
		} );
	}
	$( window ).on( 'popstate', reinit );

	$( window ).resize( function() {
		$( '.sgdg-gallery-container' ).each( function() {
			reflow( $( this ) );
		} );
	} );

	$( document ).on( 'start.ilb2 next.ilb2 previous.ilb2', function( _, e ) {
		const hash = $( e ).data( 'imagelightbox' );
		const page = $( e ).data( 'sgdg-page' );
		const children = $( e ).parent().children().length;
		history.replaceState( history.state, '', addQueryParameter( hash, 'page', page ) );
		if ( 'true' === sgdgShortcodeLocalize.page_autoload && $( e ).parent().parent().data( 'sgdgHasMore' ) && $( e ).index() >= Math.min( children - 2, Math.floor( 0.9 * children ) ) ) {
			add( $( e ).parent().parent().data( 'sgdgHash' ), page + 1 );
		}
	} );
	$( document ).on( 'quit.ilb2', function() {
		history.replaceState( history.state, '', removeQueryParameter( '[^-]+', 'page' ) );
	} );

	reinit();
} );
