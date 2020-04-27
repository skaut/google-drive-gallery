declare interface PageSuccessResponse {
	directories: Array< Directory >;
	images: Array< Image >;
	more: boolean;
	videos: Array< Video >;
}

type PageResponse = PageSuccessResponse | ErrorResponse;
