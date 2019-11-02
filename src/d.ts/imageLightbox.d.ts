declare interface JQuery {
	addToImageLightbox: ( elements: JQuery ) => void;
	imageLightbox: ( opts: ILB.Options ) => JQuery;
	openHistory: () => void;
}

declare namespace ILB {
	interface Options {
		selector?: string;
		id?: string;
		allowedTypes?: string;
		animationSpeed?: number;
		activity?: boolean;
		arrows?: boolean;
		button?: boolean;
		caption?: boolean;
		enableKeyboard?: boolean;
		history?: boolean;
		fullscreen?: boolean;
		gutter?: number;
		offsetY?: number;
		navigation?: boolean;
		overlay?: boolean;
		preloadNext?: boolean;
		quitOnEnd?: boolean;
		quitOnImgClick?: boolean;
		quitOnDocClick?: boolean;
		quitOnEscKey?: boolean;
	}
}
