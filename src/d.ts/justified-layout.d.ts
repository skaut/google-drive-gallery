declare module 'justified-layout' {
	interface SizeObject {
		height: number;
		width: number;
	}

	interface ContainerPadding {
		top: number;
		right: number;
		bottom: number;
		left: number;
	}

	interface BoxSpacing {
		horizontal: number;
		vertical: number;
	}

	interface Config {
		containerWidth: number;
		containerPadding: number | ContainerPadding;
		boxSpacing: number | BoxSpacing;
		targetRowHeight: number;
		targetRowHeightTolerance: number;
		maxNumRows: number;
		forceAspectRatio: boolean | number;
		showWidows: boolean;
		fullWidthBreakoutRowCadence: boolean | number;
		widowLayoutStyle: 'left' | 'justify' | 'center';
		edgeCaseMinRowHeight: number;
		edgeCaseMaxRowHeight: number;
	}

	interface Box {
		aspectRatio: number;
		top: number;
		width: number;
		height: number;
		left: number;
	}

	interface Result {
		containerHeight: number;
		widowCount: number;
		boxes: Array< Box >;
	}

	function convertSizesToAspectRatios(
		input: Array< number > | Array< SizeObject >,
		config: Partial< Config >
	): Result;

	export = convertSizesToAspectRatios;
}
