declare interface ListGdriveDirSuccessResponse {
	directories: Array< PartialDirectory >;
	path: Array< string >;
	resetWarn: string;
}

declare type ListGdriveDirResponse =
	| ListGdriveDirSuccessResponse
	| ErrorResponse;
