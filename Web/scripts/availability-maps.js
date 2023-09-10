function AvailabilityMaps(props) {
    const searchForm = $('#resource-maps-search');
    const mapSelect = $('#map-select');
    const resourceSelect = $('#map-select-resources');
    let map;
    const selectedMapCookieName = 'selected-map';

    function init() {
        resourceSelect.select2();

        const selectedMapId = readCookie(selectedMapCookieName);

        if (selectedMapId && mapSelect.find(`option[value="${selectedMapId}"]`).length !== 0) {
            mapSelect.val(selectedMapId);
        }

        ConfigureAsyncForm(searchForm, undefined, res => showMap(res), undefined, undefined);
        searchForm.trigger('submit');
    }

    function showMap(data) {
        limitResourceOptionsInSelect(data);

        createCookie(selectedMapCookieName, mapSelect.val(), 30, props.scriptUrl);
        const imgUrl = data.imageUrl;
        const layers = parseLayers(data.layers);

        if (map) {
            map.off();
            map.remove();
        }

        map = L.map('map', {
            minZoom: 1,
            maxZoom: 3,
            center: [0, 0],
            zoom: 2,
            crs: L.CRS.Simple,
            scrollWheelZoom: false,
        });

        let img = document.createElement('img');
        img.classList = ["no-show"];
        img.src = imgUrl;

        img.onload = function () {
            let w = img.naturalWidth;
            let h = img.naturalHeight;

            const southWest = map.unproject([0, h], map.getMaxZoom() - 1);
            const northEast = map.unproject([w, 0], map.getMaxZoom() - 1);
            const bounds = new L.LatLngBounds(southWest, northEast);

            L.imageOverlay(imgUrl, bounds).addTo(map);

            map.setMaxBounds(bounds);

            const selectedResources = data.selectedResourceIds.map(id => id.toString());

            layers.forEach(l => {
                const resource = props.resources.find(r => (r.id.toString() === l.resourceId.toString()) && (selectedResources.length === 0 || selectedResources.includes(r.id.toString())));
                if (!resource) {
                    return;
                }

                const available = !data.unavailableIds.includes(Number.parseInt(l.resourceId));
                const color = available ? 'green' : '#f06eaa';
                const statusText = available ? props.text.statusAvailable : props.text.statusUnavailable;
                const reserveUrl = data.reserveTemplate.replace("[rid]", l.resourceId);
                const scheduleUrl = data.scheduleTemplate.replace("[rid]", l.resourceId).replace("[sid]",  resource.scheduleId);
                const buttonText = available ? props.text.reserve : props.text.viewSchedule;
                const buttonUrl = available ? reserveUrl : scheduleUrl;

                const popupContents = `<div class="view-map-resource-popup">
<div class="view-map-resource-popup-name">${resource.name}</div>
<div class="view-map-resource-popup-status">
<span class="status-circle" style="background-color:${color}"></span>${statusText}</span></div>
<div class="d-grid mt-3"><a href="${buttonUrl}" class="btn btn-primary">${buttonText}</a></div>
</div>`;

                let layer;
                if (l.radius) {
                    layer = L.circle(l.latLngs[0],
                        {
                            color: color,
                            fillColor: color,
                            fillOpacity: 0.2,
                            weight: 1,
                            radius: l.radius,
                        });
                } else {
                    layer = L.polygon(l.latLngs,
                        {
                            color: color,
                            fillColor: color,
                            fillOpacity: 0.2,
                            weight: 1,
                        });
                }

                layer.id = l.layerId;
                layer.bindPopup(popupContents);
                map.addLayer(layer);
            });
        };
    }

    function limitResourceOptionsInSelect(data) {
        resourceSelect.select2('destroy').empty().select2({
            data: props.resources
                .filter(r => data.layers.some(l => l.resourceId.toString() === r.id.toString()))
                .map(r => {
                    return {
                        id: r.id,
                        text: r.name,
                    };
                }),
            placeholder: props.text.allResources,
            allowClear: true,
            // width: "element",
        });

        resourceSelect.val(data.selectedResourceIds.map(id => id.toString()));
        resourceSelect.trigger('change');
    }

    function parseLayers(layers) {
        return layers.map(l => {
            let coordinates = l.coordinates;
            return {
                layerId: l.layerId,
                resourceId: l.resourceId,
                latLngs: coordinates.latLngs.map(ll => {
                    return {lat: Number.parseFloat(ll.lat), lng: Number.parseFloat(ll.lng)}
                }),
                radius: coordinates.radius ? Number.parseFloat(coordinates.radius) : undefined,
            };
        });
    }

    return {
        init,
    };
}
