/* exported QueryParameter */

class QueryParameter {
	readonly hash: string;
	readonly name: string;

	constructor( hash: string, name: string ) {
		this.hash = hash;
		this.name = name;
	}

	public get(): string {
		const keyValuePair = new RegExp(
			'[?&]sgdg-' + this.name + '-' + this.hash + '=(([^&#]*)|&|#|$)'
		).exec( document.location.search );
		if ( ! keyValuePair || ! keyValuePair[ 2 ] ) {
			return '';
		}
		return decodeURIComponent( keyValuePair[ 2 ].replace( /\+/g, ' ' ) );
	}

	public remove(): string {
		let newQuery = window.location.search;
		const keyRegex1 = new RegExp(
			'\\?sgdg-' + this.name + '-' + this.hash + '=[^&]*'
		);
		const keyRegex2 = new RegExp(
			'&sgdg-' + this.name + '-' + this.hash + '=[^&]*'
		);
		if ( newQuery ) {
			newQuery = newQuery.replace( keyRegex1, '?' );
			newQuery = newQuery.replace( keyRegex2, '' );
		}
		return window.location.pathname + newQuery;
	}

	public add( value: string ): string {
		const query = window.location.search;
		const newField = 'sgdg-' + this.name + '-' + this.hash + '=' + value;
		let newQuery = '?' + newField;
		const keyRegex = new RegExp(
			'([?&])sgdg-' + this.name + '-' + this.hash + '=[^&]*'
		);
		if ( ! value ) {
			return this.remove();
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
}
