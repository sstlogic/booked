function Maps(opts) {
    const {url, allResources, text} = opts;
    const contents = $('#manage-maps-contents');
    const deleteModal = $("#delete-map-modal");
    const deleteForm = $('#delete-map-form');
    const deleteMapId = $('#delete-map-id');

    /**
     * @type {{layerId: string, resourceId: string, latLngs: object[]|undefined, radius: string|undefined}[]}
     */
    let layers = [];

    const resourceList = allResources.map(r => `<option value="${r.id}">${r.name}</option>`);
    resourceList.unshift(`<option value=""></option>`);

    let map;

    function getId() {
        let result = '';
        let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let charactersLength = characters.length;
        for (let i = 0; i < 20; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function init() {
        listenForHashChange();

        const dropzoneId = "#drop-file";
        const addMapFileId = '#add-map-file';

        $('#add-map-button').on('click', () => {
            loadAdd();
            window.location.href = window.location.pathname + "#add";
        });

        ConfigureAsyncForm(deleteForm);

        contents.on('click', '.edit', e => {
            e.preventDefault();
            e.stopPropagation();

            const href = window.location.href + `?id=${e.currentTarget.dataset.id}#edit`;
            window.history.pushState(undefined, undefined, href);
            loadEdit(e.currentTarget.dataset.id);
        });

        contents.on('click', '.delete', e => {
            e.preventDefault();
            e.stopPropagation();

            deleteMapId.val(e.currentTarget.dataset.id);
            deleteModal.modal('show');
        });

        contents.on('click', dropzoneId, e => {
            $(addMapFileId).trigger('click');
        });

        contents.on('change', addMapFileId, (e) => {
            addMapFile(e.target.files[0]);
        });

        contents.on("dragover", dropzoneId, e => {
            e.preventDefault();
            e.stopPropagation();
            $(e.target).addClass("dropzone-active");
        });

        contents.on("dragLeave", dropzoneId, e => {
            e.preventDefault();
            e.stopPropagation();
            $(e.target).removeClass("dropzone-active");
        });

        contents.on("drop", dropzoneId, e => {
            e.preventDefault();
            e.stopPropagation();
            $(e.target).removeClass("dropzone-active");
            let files = e.originalEvent.dataTransfer.files;
            let file = files[0];

            $('#add-map-file').files = files;

            if (file.type.startsWith("image")) {
                addMapFile(file);
            } else {
                // todo error only image
            }
        });

        contents.on("submit", "#add-map-form", e => {
            e.preventDefault();
            saveMap($('#add-map-form'));
        });

        contents.on("click", "#save-map-button", e => {
            saveMap($('#add-map-form'))
        });

        contents.on("submit", "#update-map-form", e => {
            e.preventDefault();
            saveMap($('#update-map-form'));
        });

        contents.on("click", "#update-map-button", e => {
            saveMap($('#update-map-form'))
        });

        $(document).on("change", ".map-resource-selection", e => {
            const layerId = e.target.dataset.layerid;
            const resourceId = e.target.value;
            const layer = layers.find(lr => lr.layerId == layerId);
            if (resourceId != "") {
                layer.resourceId = resourceId;
            } else {
                layer.resourceId = undefined;
            }
            if (map) {
                map.closePopup();
            }
        });
    }

    function addMapFile(file) {
        $('#add-map-file-selector').hide();
        $('#map-form-controls').removeClass('no-show');
        $('#image-error').addClass('no-show');
        let w = 800;
        let h = 400;
        const twoMb = 2 * 1024 * 1024;

        const img = new Image();
        const url = URL.createObjectURL(file);
        img.src = url;
        img.onload = function () {
            w = this.width;
            h = this.height;

            if (w < 800 || w > 2500 || file.size > twoMb) {
                $('#image-error').removeClass('no-show');
                return;
            }

            showMap(url);
        };
    }

    function showMap(url, initialLayers = undefined) {
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
        img.src = url;

        img.onload = function () {
            let w = img.naturalWidth;
            let h = img.naturalHeight;

            const southWest = map.unproject([0, h], map.getMaxZoom() - 1);
            const northEast = map.unproject([w, 0], map.getMaxZoom() - 1);
            const bounds = new L.LatLngBounds(southWest, northEast);

            L.imageOverlay(url, bounds).addTo(map);

            map.setMaxBounds(bounds);

            const drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            const drawControl = new L.Control.Draw({
                draw: {
                    polyline: false,
                    marker: false,
                    circlemarker: false,
                    polygon: {
                        shapeOptions: {
                            weight: 1,
                            showArea: false,
                        },
                        metric: false,
                    },
                    rectangle: {
                        shapeOptions: {
                            weight: 1,
                            showArea: false,
                        },
                        metric: false,
                    },
                    circle: {
                        shapeOptions: {
                            weight: 1,
                        },
                        showRadius: false,
                    },
                },
                edit: {
                    featureGroup: drawnItems,
                }
            });
            map.addControl(drawControl);

            map.on(L.Draw.Event.CREATED, function (e) {
                const type = e.layerType;
                const layer = e.layer;
                const layerId = getId();
                layer.id = layerId;

                const popupContents = `<div><label class="form-label" for="resource-select">${text.resource}</label><select id="resource-select" class="map-resource-selection form-select w-auto" data-layerid="${layerId}">${resourceList.join()}</select></div>`;

                layer.bindPopup(popupContents);
                drawnItems.addLayer(layer);
                const latLngs = type !== "circle" ? layer.getLatLngs()[0] : [layer.getLatLng()];
                const radius = type === "circle" ? layer.getRadius().toString() : undefined;
                layers.push({
                    layerId,
                    resourceId: undefined,
                    radius,
                    latLngs: latLngs.map(l => {
                        return {
                            lat: l.lat.toString(),
                            lng: l.lng.toString()
                        }
                    }),
                });
            });

            const layerColor = '#3388ff';
            if (initialLayers) {
                layers = parseInitialLayers(initialLayers);
                layers.forEach(l => {
                    const popupContents = `<div><label class="form-label" for="resource-select">${text.resource}</label><select id="resource-select" class="map-resource-selection form-select w-auto" data-layerid="${l.layerId}">${resourceList.join()}</select></div>`;
                    let layer;
                    if (l.radius) {
                        layer = L.circle(l.latLngs[0],
                            {
                                color: layerColor,
                                fillColor: layerColor,
                                fillOpacity: 0.2,
                                weight: 1,
                                radius: l.radius,
                            });
                    } else {
                        layer = L.polygon(l.latLngs,
                            {
                                color: layerColor,
                                fillColor: layerColor,
                                fillOpacity: 0.2,
                                weight: 1,
                            });
                    }
                    layer.id = l.layerId;
                    layer.bindPopup(popupContents);
                    drawnItems.addLayer(layer);
                });
            }

            map.on('draw:deleted', e => {
                const layer = e.layer;
                const id = getId();
                layer.id = id;
                layers = layers.filter(l => l.layerId !== id);
            });

            map.on('popupopen', e => {
                const layerId = e.popup._source.id;
                const select = $(e.popup._contentNode).find('.map-resource-selection');
                // select.find("option").removeAttr("disabled").removeClass('map-resource-selected');
                // locationResources.forEach(lr => {
                //     const option = select.find(`option[value="${lr.resourceId}"]`);
                //     option.attr('disabled', true);
                //     option.addClass('map-resource-selected');
                // });
                const lr = layers.find(lr => lr.layerId === layerId);
                if (lr) {
                    select.val(lr.resourceId);
                }
            });
        };
    }

    function saveMap(form) {
        const mapName = $('#map-name');
        mapName.removeClass('is-invalid');

        if (mapName.val().trim() === "") {
            mapName.addClass('is-invalid');
            return;
        }

        const data = {
            layers: layers
                .filter(l => l.resourceId !== undefined)
                .map(l => {
                    return {layerId: l.layerId, resourceId: l.resourceId, latLngs: l.latLngs, radius: l.radius}
                }),
        };

        $('#map-data').val(JSON.stringify(data));

        form.addClass('no-show');
        $('#map').addClass('no-show');
        $('#add-map-indicator').removeClass('no-show');

        ajaxPost(form, undefined, undefined, () => window.location.href = url)
    }

    function loadAdd() {
        ajaxGet(url + '?dr=add', undefined, (data) => {
            contents.html(data);
        });
    }

    function loadEdit(mapId) {
        ajaxGet(url + `?dr=edit&id=${mapId}`, undefined, (data) => {
            contents.html(data);
            showMap($('#map-image-url').val(), JSON.parse($("#map-data").val()));
        });
    }

    function parseInitialLayers(initialLayers) {
        return initialLayers.layers.map(l => {
            let coordinates = l.coordinates;
            return {
                layerId: l.layerId,
                resourceId: l.resourceId,
                latLngs: coordinates.latLngs.map(ll => {
                    return {lat: Number.parseFloat(ll.lat), lng: Number.parseFloat(ll.lng)}
                }),
                radius: coordinates.radius ? Number.parseFloat(coordinates.radius) : undefined,
            }
        });
    }

    function listenForHashChange() {
        window.addEventListener('hashchange', () => {
            if (window.location.hash === "") {
                window.location.reload();
            }
        }, false);
    }

    return {
        init,
        loadAdd,
        loadEdit,
    };
}