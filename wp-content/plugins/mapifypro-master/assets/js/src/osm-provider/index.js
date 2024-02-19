const { OpenStreetMapProvider } = require('leaflet-geosearch');

class CustomOSMProvider extends OpenStreetMapProvider {
    constructor(options = {}) {
        super(options);

        const host = 'https://nominatim.openstreetmap.org';
        this.searchUrl = options.searchUrl || `${host}/search`;
        this.reverseUrl = options.reverseUrl || `${host}/reverse`;
    }

    endpoint({ query, type, country, postalcode }) {
        const params = typeof query === 'string' ? { q: query } : query;
        params.format = 'jsonv2';
        params.countrycodes = country;
        params.postalcode = postalcode;

        switch (type) {
            // RequestType.REVERSE === 1
            case 1:
            return this.getUrl(this.reverseUrl, params);

            default:
            return this.getUrl(this.searchUrl, params);
        }
    }

    parse(response) {
        const records = Array.isArray(response.data)
          ? response.data
          : [response.data];

        return records.map((r) => ({
          x: Number(r.lon),
          y: Number(r.lat),
          label: r.display_name,
          bounds: [
            [parseFloat(r.boundingbox[0]), parseFloat(r.boundingbox[2])], // s, w
            [parseFloat(r.boundingbox[1]), parseFloat(r.boundingbox[3])], // n, e
          ],
          raw: r,
        }));
    }

    getParamString(params = {}) {
        const set = Object.assign({}, this.options.params, params);
        return Object.keys(set)
            .filter(key => !!set[key])
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(set[key])}`)
            .join('&');
    }

    getUrl(url, params) {
        return `${url}?${this.getParamString(params)}`;
    }

    search(options) {
        return new Promise((resolve) => {
            const url = this.endpoint({
                query: options.query,
                // RequestType.SEARCH === 0
                type: 0,
                country: options.country,
                postalcode: options.postalcode
            });

            return resolve(fetch(url));
        })
        .then(request => request.json())
        .then(json => this.parse({ data: json }));;
    }
}

module.exports = CustomOSMProvider;
