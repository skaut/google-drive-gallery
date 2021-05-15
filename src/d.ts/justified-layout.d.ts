declare interface SizeObject {
	height: number;
	width: number;
}

declare interface ContainerPadding {
	top: number;
	right: number;
	bottom: number;
	left: number;
}

declare interface BoxSpacing {
	horizontal: number;
	vertical: number;
}

declare interface Config {
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

declare interface Box {
	aspectRatio: number;
	top: number;
	width: number;
	height: number;
	left: number;
}

declare interface JustifiedLayoutResult {
	containerHeight: number;
	widowCount: number;
	boxes: Array< Box >;
}

declare type JustifiedLayout = (
	input: Array< number > | Array< SizeObject >,
	config: Partial< Config >
) => JustifiedLayoutResult;
