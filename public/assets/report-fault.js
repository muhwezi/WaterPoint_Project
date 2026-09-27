"use strict";


// =========================================================
// FORM FEEDBACK
// =========================================================

document
    .getElementById(
        "form-feedback"
    )
    ?.focus();


// =========================================================
// REPORT SCOPE
// =========================================================

const scopeInputs =
    document.querySelectorAll(
        'input[name="report_scope"]'
    );


const knownSection =
    document.getElementById(
        "known-water-point-section"
    );


const otherSection =
    document.getElementById(
        "other-infrastructure-section"
    );


const waterPointCode =
    document.getElementById(
        "water_point_code"
    );


const infrastructureType =
    document.getElementById(
        "infrastructure_type"
    );


const faultLocation =
    document.getElementById(
        "fault_location"
    );


const villageField =
    document.getElementById(
        "village"
    );


const selectedWaterPoint =
    document.getElementById(
        "selected-water-point"
    );


const directory =
    Array.isArray(
        window.waterPointDirectory
    )
        ? window.waterPointDirectory
        : [];


// =========================================================
// COORDINATES
// =========================================================

const latitudeField =
    document.getElementById(
        "latitude"
    );


const longitudeField =
    document.getElementById(
        "longitude"
    );


const locationButton =
    document.getElementById(
        "get-location"
    );


const locationStatus =
    document.getElementById(
        "location-status"
    );


let map = null;
let marker = null;


// =========================================================
// HELPERS
// =========================================================

function currentScope() {

    return document.querySelector(
        'input[name="report_scope"]:checked'
    )?.value ?? "";
}


function validCoordinates(
    latitude,
    longitude
) {

    return (
        Number.isFinite(
            latitude
        ) &&
        Number.isFinite(
            longitude
        ) &&
        latitude >= -90 &&
        latitude <= 90 &&
        longitude >= -180 &&
        longitude <= 180
    );
}


function setCoordinates(
    latitude,
    longitude
) {

    latitudeField.value =
        Number(latitude)
            .toFixed(7);


    longitudeField.value =
        Number(longitude)
            .toFixed(7);


    updateMarker(
        latitude,
        longitude,
        true
    );
}


// =========================================================
// SCOPE UI
// =========================================================

function refreshScope() {

    const scope =
        currentScope();


    const known =
        scope ===
        "KNOWN_WATER_POINT";


    const other =
        scope ===
        "OTHER_INFRASTRUCTURE";


    if (knownSection) {

        knownSection.hidden =
            !known;
    }


    if (otherSection) {

        otherSection.hidden =
            !other;
    }


    if (waterPointCode) {

        waterPointCode.required =
            known;
    }


    if (infrastructureType) {

        infrastructureType.required =
            other;
    }


    if (faultLocation) {

        faultLocation.required =
            other;
    }


    if (villageField) {

        villageField.required =
            other;
    }


    if (known) {

        refreshSelectedWaterPoint();
    }
}


scopeInputs.forEach(
    function (
        input
    ) {

        input.addEventListener(
            "change",
            refreshScope
        );
    }
);


// =========================================================
// WATER POINT LOOKUP
// =========================================================

function findWaterPoint(
    code
) {

    const normalized =
        String(code)
            .trim()
            .toUpperCase();


    return directory.find(
        function (
            point
        ) {

            return (
                String(
                    point.code
                )
                    .toUpperCase()
                === normalized
            );
        }
    ) ?? null;
}


function formatCodeLabel(
    value
) {

    return String(value)
        .replaceAll(
            "_",
            " "
        )
        .toLowerCase()
        .replace(
            /\b\w/g,
            function (
                character
            ) {

                return character
                    .toUpperCase();
            }
        );
}


function refreshSelectedWaterPoint() {

    if (
        !waterPointCode ||
        !selectedWaterPoint
    ) {

        return;
    }


    const point =
        findWaterPoint(
            waterPointCode.value
        );


    if (!point) {

        selectedWaterPoint.hidden =
            true;

        return;
    }


    waterPointCode.value =
        point.code;


    selectedWaterPoint
        .querySelector(
            ".selected-code"
        )
        .textContent =
            point.code;


    selectedWaterPoint
        .querySelector(
            ".selected-name"
        )
        .textContent =
            point.name;


    selectedWaterPoint
        .querySelector(
            ".selected-village"
        )
        .textContent =
            point.village +
            " • " +
            formatCodeLabel(
                point.type
            );


    selectedWaterPoint
        .querySelector(
            ".selected-status"
        )
        .textContent =
            "Current status: " +
            formatCodeLabel(
                point.status
            );


    selectedWaterPoint.hidden =
        false;


    const latitude =
        Number.parseFloat(
            point.latitude
        );


    const longitude =
        Number.parseFloat(
            point.longitude
        );


    if (
        validCoordinates(
            latitude,
            longitude
        )
    ) {

        const currentLatitude =
            Number.parseFloat(
                latitudeField.value
            );


        const currentLongitude =
            Number.parseFloat(
                longitudeField.value
            );


        if (
            !validCoordinates(
                currentLatitude,
                currentLongitude
            )
        ) {

            setCoordinates(
                latitude,
                longitude
            );

        } else {

            updateMarker(
                currentLatitude,
                currentLongitude,
                true
            );
        }
    }
}


waterPointCode
    ?.addEventListener(
        "change",
        refreshSelectedWaterPoint
    );


waterPointCode
    ?.addEventListener(
        "input",
        function () {

            if (
                findWaterPoint(
                    waterPointCode.value
                )
            ) {

                refreshSelectedWaterPoint();
            }
        }
    );


// =========================================================
// MAP
// =========================================================

const mapElement =
    document.getElementById(
        "fault-map"
    );


function updateMarker(
    latitude,
    longitude,
    zoom
) {

    if (
        !map ||
        !validCoordinates(
            latitude,
            longitude
        )
    ) {

        return;
    }


    if (!marker) {

        marker =
            L.marker(
                [
                    latitude,
                    longitude
                ],
                {
                    draggable:
                        true
                }
            ).addTo(map);


        marker.on(
            "dragend",
            function () {

                const position =
                    marker.getLatLng();


                latitudeField.value =
                    position.lat
                        .toFixed(7);


                longitudeField.value =
                    position.lng
                        .toFixed(7);


                if (
                    locationStatus
                ) {

                    locationStatus.textContent =
                        "Fault location adjusted.";
                }
            }
        );

    } else {

        marker.setLatLng([
            latitude,
            longitude
        ]);
    }


    if (zoom) {

        map.setView(
            [
                latitude,
                longitude
            ],
            17
        );
    }
}


if (
    mapElement &&
    typeof L !==
        "undefined"
) {

    map =
        L.map(
            "fault-map"
        ).setView(
            [
                1.3733,
                32.2903
            ],
            7
        );


    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            maxZoom:
                19,

            attribution:
                "&copy; OpenStreetMap contributors"
        }
    ).addTo(map);


    map.on(
        "click",
        function (
            event
        ) {

            setCoordinates(
                event.latlng.lat,
                event.latlng.lng
            );


            if (
                locationStatus
            ) {

                locationStatus.textContent =
                    "Fault location selected from the map.";
            }
        }
    );


    const startLatitude =
        Number.parseFloat(
            latitudeField?.value
        );


    const startLongitude =
        Number.parseFloat(
            longitudeField?.value
        );


    if (
        validCoordinates(
            startLatitude,
            startLongitude
        )
    ) {

        updateMarker(
            startLatitude,
            startLongitude,
            true
        );
    }


    window.setTimeout(
        function () {

            map.invalidateSize();

        },
        150
    );
}


// =========================================================
// MANUAL COORDINATE CHANGE
// =========================================================

function refreshMapFromCoordinates() {

    const latitude =
        Number.parseFloat(
            latitudeField.value
        );


    const longitude =
        Number.parseFloat(
            longitudeField.value
        );


    if (
        validCoordinates(
            latitude,
            longitude
        )
    ) {

        updateMarker(
            latitude,
            longitude,
            true
        );
    }
}


latitudeField
    ?.addEventListener(
        "change",
        refreshMapFromCoordinates
    );


longitudeField
    ?.addEventListener(
        "change",
        refreshMapFromCoordinates
    );


// =========================================================
// GEOLOCATION
// =========================================================

locationButton
    ?.addEventListener(
        "click",
        function () {

            if (
                !navigator.geolocation
            ) {

                locationStatus.textContent =
                    "Location services are not supported by this browser.";

                return;
            }


            locationButton.disabled =
                true;


            locationStatus.textContent =
                "Getting current location...";


            navigator.geolocation
                .getCurrentPosition(

                    function (
                        position
                    ) {

                        setCoordinates(
                            position.coords
                                .latitude,

                            position.coords
                                .longitude
                        );


                        locationStatus.textContent =
                            "Location captured. Estimated accuracy: " +
                            Math.round(
                                position.coords
                                    .accuracy
                            ) +
                            " metres.";


                        locationButton.disabled =
                            false;
                    },


                    function (
                        error
                    ) {

                        let message =
                            "Location could not be obtained.";


                        if (
                            error.code ===
                            error.PERMISSION_DENIED
                        ) {

                            message =
                                "Location permission was denied.";

                        } else if (
                            error.code ===
                            error.POSITION_UNAVAILABLE
                        ) {

                            message =
                                "Current location is unavailable.";

                        } else if (
                            error.code ===
                            error.TIMEOUT
                        ) {

                            message =
                                "Getting the location took too long.";
                        }


                        locationStatus.textContent =
                            message;


                        locationButton.disabled =
                            false;
                    },


                    {
                        enableHighAccuracy:
                            true,

                        timeout:
                            15000,

                        maximumAge:
                            0
                    }
                );
        }
    );


// =========================================================
// CAMERA / PHOTO
// =========================================================

const photoInput =
    document.getElementById(
        "photo"
    );


const openCameraButton =
    document.getElementById(
        "open-camera"
    );


const cameraPanel =
    document.getElementById(
        "camera-panel"
    );


const cameraVideo =
    document.getElementById(
        "camera-stream"
    );


const cameraCanvas =
    document.getElementById(
        "camera-canvas"
    );


const captureButton =
    document.getElementById(
        "capture-photo"
    );


const closeCameraButton =
    document.getElementById(
        "close-camera"
    );


const cameraStatus =
    document.getElementById(
        "camera-status"
    );


const previewContainer =
    document.getElementById(
        "photo-preview-container"
    );


const previewImage =
    document.getElementById(
        "photo-preview"
    );


const removePhotoButton =
    document.getElementById(
        "remove-photo"
    );


const photoStatus =
    document.getElementById(
        "photo-status"
    );


let cameraStream =
    null;


let previewUrl =
    null;


// =========================================================
// PREVIEW
// =========================================================

function showPreview(
    file
) {

    if (!file) {
        return;
    }


    if (previewUrl) {

        URL.revokeObjectURL(
            previewUrl
        );
    }


    previewUrl =
        URL.createObjectURL(
            file
        );


    previewImage.src =
        previewUrl;


    previewContainer.hidden =
        false;


    photoStatus.textContent =
        "Photo ready.";
}


// =========================================================
// FILE PICKER
// =========================================================

photoInput
    ?.addEventListener(
        "change",
        function () {

            const file =
                photoInput.files?.[0];


            if (!file) {
                return;
            }


            const allowed = [

                "image/jpeg",

                "image/png",

                "image/webp"
            ];


            if (
                !allowed.includes(
                    file.type
                )
            ) {

                photoInput.value =
                    "";


                photoStatus.textContent =
                    "Use a JPG, PNG or WebP image.";

                return;
            }


            if (
                file.size >
                5 * 1024 * 1024
            ) {

                photoInput.value =
                    "";


                photoStatus.textContent =
                    "The photo must be smaller than 5 MB.";

                return;
            }


            showPreview(
                file
            );
        }
    );


// =========================================================
// CAMERA
// =========================================================

function stopCamera() {

    if (cameraStream) {

        cameraStream
            .getTracks()
            .forEach(
                function (
                    track
                ) {

                    track.stop();
                }
            );


        cameraStream =
            null;
    }


    if (cameraVideo) {

        cameraVideo.srcObject =
            null;
    }


    if (cameraPanel) {

        cameraPanel.hidden =
            true;
    }
}


openCameraButton
    ?.addEventListener(
        "click",
        async function () {

            if (
                !navigator.mediaDevices
                    ?.getUserMedia
            ) {

                photoInput?.click();

                return;
            }


            try {

                cameraStream =
                    await navigator
                        .mediaDevices
                        .getUserMedia({

                            video: {

                                facingMode: {
                                    ideal:
                                        "environment"
                                }
                            },

                            audio:
                                false
                        });


                cameraVideo.srcObject =
                    cameraStream;


                cameraPanel.hidden =
                    false;


                cameraStatus.textContent =
                    "Camera ready.";


                cameraPanel
                    .scrollIntoView({
                        behavior:
                            "smooth",

                        block:
                            "center"
                    });


            } catch (
                error
            ) {

                cameraStatus.textContent =
                    "Camera permission was denied or the camera is unavailable.";
            }
        }
    );


captureButton
    ?.addEventListener(
        "click",
        function () {

            if (
                !cameraVideo.videoWidth ||
                !cameraVideo.videoHeight
            ) {

                cameraStatus.textContent =
                    "The camera is not ready yet.";

                return;
            }


            let width =
                cameraVideo.videoWidth;


            let height =
                cameraVideo.videoHeight;


            const maximumDimension =
                1600;


            if (
                width >
                    maximumDimension ||
                height >
                    maximumDimension
            ) {

                const scale =
                    Math.min(
                        maximumDimension /
                            width,

                        maximumDimension /
                            height
                    );


                width =
                    Math.round(
                        width *
                        scale
                    );


                height =
                    Math.round(
                        height *
                        scale
                    );
            }


            cameraCanvas.width =
                width;


            cameraCanvas.height =
                height;


            const context =
                cameraCanvas
                    .getContext(
                        "2d"
                    );


            context.drawImage(
                cameraVideo,
                0,
                0,
                width,
                height
            );


            cameraCanvas.toBlob(

                function (
                    blob
                ) {

                    if (!blob) {
                        return;
                    }


                    const file =
                        new File(
                            [blob],

                            "fault-" +
                            Date.now() +
                            ".jpg",

                            {
                                type:
                                    "image/jpeg"
                            }
                        );


                    const transfer =
                        new DataTransfer();


                    transfer.items.add(
                        file
                    );


                    photoInput.files =
                        transfer.files;


                    showPreview(
                        file
                    );


                    photoStatus.textContent =
                        "Photo captured successfully.";


                    stopCamera();
                },

                "image/jpeg",

                0.88
            );
        }
    );


closeCameraButton
    ?.addEventListener(
        "click",
        stopCamera
    );


removePhotoButton
    ?.addEventListener(
        "click",
        function () {

            photoInput.value =
                "";


            if (previewUrl) {

                URL.revokeObjectURL(
                    previewUrl
                );


                previewUrl =
                    null;
            }


            previewImage.src =
                "";


            previewContainer.hidden =
                true;


            photoStatus.textContent =
                "Photo removed.";
        }
    );


// =========================================================
// INITIAL STATE
// =========================================================

refreshScope();


if (
    currentScope() ===
    "KNOWN_WATER_POINT"
) {

    refreshSelectedWaterPoint();
}


// =========================================================
// CLEANUP
// =========================================================

window.addEventListener(
    "beforeunload",
    function () {

        stopCamera();


        if (previewUrl) {

            URL.revokeObjectURL(
                previewUrl
            );
        }
    }
);