declare interface GallerySuccessResponse extends PageSuccessResponse {
	path: Array< PartialDirectory >;
}

declare type GalleryResponse = GallerySuccessResponse | ErrorResponse;
