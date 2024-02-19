class MapifyMapStyles {
	constructor(L) {
		this.L = L;
		this.customStyles = {};
	}

	add(customStyles) {
		this.customStyles = { ...this.customStyles, ...customStyles };
	}

	get() {
		const styles = {
			'osm': new this.L.maplibreGL({
				style: 'https://tiles.mapifypro.com/api/maps/streets/style.json',
				attribution: '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
				minZoom : 0,
				maxZoom : 19,
			}),
			'mapifypro-basic': new this.L.maplibreGL({
				style: 'https://tiles.mapifypro.com/api/maps/basic/style.json',
				attribution: '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
				minZoom : 0,
				maxZoom : 19,
			}),
			'mapifypro-bright': new this.L.maplibreGL({
				style: 'https://tiles.mapifypro.com/api/maps/bright/style.json',
				attribution: '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
				minZoom : 0,
				maxZoom : 19,
			}),
			'mapifypro-streets': new this.L.maplibreGL({
				style: 'https://tiles.mapifypro.com/api/maps/streets/style.json',
				attribution: '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
				minZoom : 0,
				maxZoom : 19,
			}),
			'road': new this.L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				minZoom     : 2,
				maxZoom     : 19,
				attribution : '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
			}),
			'terrain': new this.L.TileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
				minZoom     : 2,
				maxZoom     : 19,
				attribution : '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
			}),
			'watercolor': new this.L.TileLayer('https://stamen-tiles.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.{ext}', {
				attribution : '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
				minZoom     : 2,
				maxZoom     : 19,
				ext         : 'jpg'
			}),
			'ink': new this.L.TileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.{ext}', {
				attribution : '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
				subdomains  : 'abcd',
				minZoom     : 2,
				maxZoom     : 19,
				ext         : 'png'
			}),
			'pastel': new this.L.TileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
				minZoom     : 2,
				maxZoom     : 19,
				attribution : '© <a target="_blank" href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
			}),
			'stamen-toner-background': new this.L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner-background/{z}/{x}/{y}{r}.{ext}', {
				attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				subdomains: 'abcd',
				minZoom: 0,
				maxZoom: 19,
				ext: 'png'
			}),
			'stamen-toner-lite': new this.L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}{r}.{ext}', {
				attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				subdomains: 'abcd',
				minZoom: 0,
				maxZoom: 19,
				ext: 'png'
			}),
			'cartodb-positron': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'cartodb-positron-no-labels': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'cartodb-dark-matter': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'cartodb-dark-matter-no-labels': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'cartodb-voyager': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'cartodb-voyager-grey-labels': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'cartodb-voyager-no-labels': new this.L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}),
			'esri-delorme': new this.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Specialty/DeLorme_World_Base_Map/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri &mdash; Copyright: &copy;2012 DeLorme',
				minZoom: 1,
				maxZoom: 12,
			}),
			'esri-world-street-map': new this.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
				maxZoom: 16,
			}),
			'esri-world-topo-map': new this.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community',
				maxZoom: 16,
			}),
			'esri-world-imagery': new this.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
				maxZoom: 16,
			}),
			'esri-world-gray-canvas': new this.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
				maxZoom: 16,
			}),
			...this.customStyles
		};

		return styles;
	}
}