interface Coords {
	top: number
	left: number
	width: number
	height: number
}

interface ImageTransforms {
	rotate: number
	flip: {
		horizontal: boolean
		vertical: boolean
	}
}

interface CropperResults {
	coords: Coords
	transforms: ImageTransforms
}
