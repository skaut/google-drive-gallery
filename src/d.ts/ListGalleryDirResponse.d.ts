declare interface ListGalleryDirSuccessResponse {
	directories: Array< string >;
}

type ListGalleryDirResponse = ListGalleryDirSuccessResponse | ErrorResponse;
