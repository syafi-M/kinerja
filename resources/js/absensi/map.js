// Map utilities and tile prefetching
export function initMap(elementId = 'map') {
    return L.map(elementId);
}

export function createLayerGroup(map) {
    return L.layerGroup().addTo(map);
}

export function ensureTileLayer(map, tileLayerRef) {
    if (tileLayerRef.current) {
        return tileLayerRef.current;
    }

    tileLayerRef.current = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    return tileLayerRef.current;
}

export function drawLocationCircle(layerGroup, latitude, longitude, radius) {
    return L.circle([latitude, longitude], {
        color: 'crimson',
        fillColor: '#f09',
        fillOpacity: 0.5,
        radius: radius
    }).addTo(layerGroup);
}

export function latLngToTile(latitude, longitude, zoom) {
    const latRad = latitude * Math.PI / 180;
    const scale = Math.pow(2, zoom);

    return {
        x: Math.floor((longitude + 180) / 360 * scale),
        y: Math.floor((1 - Math.log(Math.tan(latRad) + 1 / Math.cos(latRad)) / Math.PI) / 2 * scale)
    };
}

export function prefetchTilesAround(prefetchedTiles, latitude, longitude, zoom = 15, range = 1) {
    const parsedLatitude = parseFloat(latitude);
    const parsedLongitude = parseFloat(longitude);

    if (!Number.isFinite(parsedLatitude) || !Number.isFinite(parsedLongitude)) {
        return;
    }

    const centerTile = latLngToTile(parsedLatitude, parsedLongitude, zoom);
    const subdomains = ['a', 'b', 'c'];

    for (let xOffset = -range; xOffset <= range; xOffset++) {
        for (let yOffset = -range; yOffset <= range; yOffset++) {
            const x = centerTile.x + xOffset;
            const y = centerTile.y + yOffset;
            const key = `${zoom}/${x}/${y}`;

            if (prefetchedTiles.has(key)) {
                continue;
            }

            prefetchedTiles.add(key);
            const img = new Image();
            img.decoding = 'async';
            img.src = `https://${subdomains[prefetchedTiles.size % subdomains.length]}.tile.openstreetmap.org/${zoom}/${x}/${y}.png`;
        }
    }
}

export function prefetchLocationTiles(prefetchedTiles, latitude, longitude, options = {}) {
    const {
        zoomLevels = [14, 15, 16],
        range = 2
    } = options;

    const runPrefetch = () => {
        zoomLevels.forEach((zoom) => prefetchTilesAround(prefetchedTiles, latitude, longitude, zoom, range));
    };

    if ('requestIdleCallback' in window) {
        requestIdleCallback(() => runPrefetch(), {
            timeout: 2000
        });
        return;
    }

    setTimeout(() => runPrefetch(), 500);
}

export function adjustMapViewForBothPoints(map, userLat, userLng, mitraLat, mitraLng, isInsideRadius) {
    if (isInsideRadius) {
        map.setView([userLat, userLng], 14);
        return;
    }

    const bounds = L.latLngBounds([
        [userLat, userLng],
        [mitraLat, mitraLng]
    ]);

    map.fitBounds(bounds, {
        padding: [50, 50],
        maxZoom: 15
    });
}
