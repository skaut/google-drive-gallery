/* exported isError */

function isError( data: GalleryResponse|ListGalleryDirResponse|ListGdriveDirResponse|PageResponse ): data is ErrorResponse {
	return ( data as ErrorResponse ).error !== undefined;
}
