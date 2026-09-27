"use strict";


// =========================================================
// FORM FEEDBACK
// =========================================================

const feedback =
    document.getElementById("form-feedback");

if (feedback) {
    feedback.focus();
}


// =========================================================
// ELEMENTS
// =========================================================

const locationButton =
    document.getElementById("get-location");

const latitudeField =
    document.getElementById("latitude");

const longitudeField =
    document.getElementById("longitude");

const locationStatus =
    document.getElementById("location-status");

const mapElement =
    document.getElementById("water-point-map");


const photoInput =
    document.getElementById("photo");

const photoPreviewContainer =
    document.getElementById(
        "photo-preview-container"
    );

const photoPreview =
    document.getElementById("photo-preview");

const removePhotoButton =
    document.getElementById("remove-photo");

const photoStatus =
    document.getElementById("photo-status");


// =========================================================
// MAP VARIABLES
// =========================================================

let map = null;
let marker = null;


// Approximate centre of Uganda.
const defaultLatitude = 1.3733;
const defaultLongitude = 32.2903;


// =========================================================
// COORDINATE HELPERS
// =========================================================

function coordinatesAreValid(
    latitude,
    longitude
) {

    return (
        Number.isFinite(latitude) &&
        Number.isFinite(longitude) &&
        latitude >= -90 &&
        latitude <= 90 &&
        longitude >= -180 &&
        longitude <= 180
    );
}


function setCoordinateFields(
    latitude,
    longitude
) {

    if (latitudeField) {

        latitudeField.value =
            Number(latitude).toFixed(7);
    }


    if (longitudeField) {

        longitudeField.value =
            Number(longitude).toFixed(7);
    }
}


// =========================================================
// SHOW LOCATION ON MAP
// =========================================================

function showLocationOnMap(
    latitude,
    longitude,
    zoomIn = true
) {

    if (!map) {
        return;
    }


    if (
        !coordinatesAreValid(
            latitude,
            longitude
        )
    ) {
        return;
    }


    const position = [
        latitude,
        longitude
    ];


    // -----------------------------------------------------
    // CREATE MARKER
    // -----------------------------------------------------

    if (!marker) {

        marker = L.marker(
            position,
            {
                draggable: true
            }
        ).addTo(map);


        marker.on(
            "dragend",
            function () {

                const newPosition =
                    marker.getLatLng();


                setCoordinateFields(
                    newPosition.lat,
                    newPosition.lng
                );


                if (locationStatus) {

                    locationStatus.textContent =
                        "Location adjusted using the map marker.";
                }

            }
        );

    } else {

        marker.setLatLng(position);
    }


    // -----------------------------------------------------
    // MOVE MAP
    // -----------------------------------------------------

    if (zoomIn) {

        map.setView(
            position,
            17
        );

    } else {

        map.panTo(position);
    }


    marker
        .bindPopup(
            "Selected water point location"
        )
        .openPopup();
}


// =========================================================
// INITIALISE MAP
// =========================================================

if (
    mapElement &&
    typeof L !== "undefined"
) {

    map = L.map(
        "water-point-map",
        {
            scrollWheelZoom: true
        }
    );


    map.setView(
        [
            defaultLatitude,
            defaultLongitude
        ],
        7
    );


    // -----------------------------------------------------
    // OPENSTREETMAP
    // -----------------------------------------------------

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            maxZoom: 19,

            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    // -----------------------------------------------------
    // DISPLAY EXISTING VALUES
    // -----------------------------------------------------

    if (
        latitudeField &&
        longitudeField
    ) {

        const startingLatitude =
            parseFloat(latitudeField.value);

        const startingLongitude =
            parseFloat(longitudeField.value);


        if (
            coordinatesAreValid(
                startingLatitude,
                startingLongitude
            )
        ) {

            showLocationOnMap(
                startingLatitude,
                startingLongitude,
                true
            );
        }
    }


    // -----------------------------------------------------
    // MAP CLICK
    // -----------------------------------------------------

    map.on(
        "click",
        function (event) {

            const latitude =
                event.latlng.lat;

            const longitude =
                event.latlng.lng;


            setCoordinateFields(
                latitude,
                longitude
            );


            showLocationOnMap(
                latitude,
                longitude,
                false
            );


            if (locationStatus) {

                locationStatus.textContent =
                    "Location selected from the map.";
            }

        }
    );


    // Leaflet sometimes needs this when inside
    // responsive containers.
    window.setTimeout(
        function () {
            map.invalidateSize();
        },
        150
    );
}


// =========================================================
// MANUAL COORDINATE ENTRY
// =========================================================

function updateMapFromFields() {

    if (
        !latitudeField ||
        !longitudeField
    ) {
        return;
    }


    const latitude =
        parseFloat(latitudeField.value);

    const longitude =
        parseFloat(longitudeField.value);


    if (
        coordinatesAreValid(
            latitude,
            longitude
        )
    ) {

        showLocationOnMap(
            latitude,
            longitude,
            true
        );


        if (locationStatus) {

            locationStatus.textContent =
                "Map updated from the entered coordinates.";
        }
    }
}


if (latitudeField) {

    latitudeField.addEventListener(
        "change",
        updateMapFromFields
    );
}


if (longitudeField) {

    longitudeField.addEventListener(
        "change",
        updateMapFromFields
    );
}


// =========================================================
// CURRENT LOCATION
// =========================================================

if (
    locationButton &&
    latitudeField &&
    longitudeField
) {

    locationButton.addEventListener(
        "click",
        function () {

            // -------------------------------------------------
            // GEOLOCATION SUPPORT
            // -------------------------------------------------

            if (!navigator.geolocation) {

                if (locationStatus) {

                    locationStatus.textContent =
                        "Location services are not supported by this browser.";
                }

                return;
            }


            locationButton.disabled = true;

            locationButton.textContent =
                "Getting location...";


            if (locationStatus) {

                locationStatus.textContent =
                    "Getting your current coordinates...";
            }


            // -------------------------------------------------
            // GET POSITION
            // -------------------------------------------------

            navigator.geolocation.getCurrentPosition(

                // =============================================
                // SUCCESS
                // =============================================

                function (position) {

                    const latitude =
                        position.coords.latitude;

                    const longitude =
                        position.coords.longitude;

                    const accuracy =
                        position.coords.accuracy;


                    setCoordinateFields(
                        latitude,
                        longitude
                    );


                    showLocationOnMap(
                        latitude,
                        longitude,
                        true
                    );


                    if (locationStatus) {

                        locationStatus.textContent =
                            "Location captured successfully. " +
                            "Estimated accuracy: " +
                            Math.round(accuracy) +
                            " metres.";
                    }


                    locationButton.disabled =
                        false;

                    locationButton.textContent =
                        "Refresh current location";
                },


                // =============================================
                // ERROR
                // =============================================

                function (error) {

                    let message =
                        "Location could not be obtained.";


                    switch (error.code) {

                        case error.PERMISSION_DENIED:

                            message =
                                "Location permission was denied. " +
                                "Allow location access or select the location on the map.";

                            break;


                        case error.POSITION_UNAVAILABLE:

                            message =
                                "Your current location is unavailable. " +
                                "Select the location manually on the map.";

                            break;


                        case error.TIMEOUT:

                            message =
                                "Getting your location took too long. " +
                                "Please try again.";

                            break;


                        default:

                            message =
                                "Location could not be obtained. " +
                                "You can select it manually on the map.";
                    }


                    if (locationStatus) {

                        locationStatus.textContent =
                            message;
                    }


                    locationButton.disabled =
                        false;

                    locationButton.textContent =
                        "Use current location";
                },


                // =============================================
                // OPTIONS
                // =============================================

                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }

            );

        }
    );
}


// =========================================================
// PHOTO PREVIEW
// =========================================================

let currentPhotoPreviewUrl = null;


function clearPhotoPreview() {

    if (currentPhotoPreviewUrl) {

        URL.revokeObjectURL(
            currentPhotoPreviewUrl
        );

        currentPhotoPreviewUrl = null;
    }


    if (photoPreview) {

        photoPreview.src = "";
    }


    if (photoPreviewContainer) {

        photoPreviewContainer.hidden = true;
    }


    if (photoInput) {

        photoInput.value = "";
    }


    if (photoStatus) {

        photoStatus.textContent =
            "No photo selected.";
    }
}


// =========================================================
// PHOTO INPUT CHANGE
// =========================================================

if (
    photoInput &&
    photoPreview &&
    photoPreviewContainer
) {

    photoInput.addEventListener(
        "change",
        function () {

            const file =
                photoInput.files &&
                photoInput.files.length > 0
                    ? photoInput.files[0]
                    : null;


            // -------------------------------------------------
            // NO FILE
            // -------------------------------------------------

            if (!file) {

                clearPhotoPreview();

                return;
            }


            // -------------------------------------------------
            // ALLOWED TYPES
            // -------------------------------------------------

            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                clearPhotoPreview();


                if (photoStatus) {

                    photoStatus.textContent =
                        "Use a JPG, PNG or WebP image.";
                }

                return;
            }


            // -------------------------------------------------
            // 5 MB SIZE LIMIT
            // -------------------------------------------------

            const maximumSize =
                5 * 1024 * 1024;


            if (file.size > maximumSize) {

                clearPhotoPreview();


                if (photoStatus) {

                    photoStatus.textContent =
                        "The photo must be smaller than 5 MB.";
                }

                return;
            }


            // -------------------------------------------------
            // RELEASE OLD PREVIEW
            // -------------------------------------------------

            if (currentPhotoPreviewUrl) {

                URL.revokeObjectURL(
                    currentPhotoPreviewUrl
                );
            }


            // -------------------------------------------------
            // CREATE PREVIEW
            // -------------------------------------------------

            currentPhotoPreviewUrl =
                URL.createObjectURL(file);


            photoPreview.src =
                currentPhotoPreviewUrl;


            photoPreviewContainer.hidden =
                false;


            if (photoStatus) {

                const sizeInMb =
                    (
                        file.size /
                        (1024 * 1024)
                    ).toFixed(2);


                photoStatus.textContent =
                    "Photo selected: " +
                    file.name +
                    " (" +
                    sizeInMb +
                    " MB).";
            }

        }
    );
}


// =========================================================
// REMOVE PHOTO
// =========================================================

if (removePhotoButton) {

    removePhotoButton.addEventListener(
        "click",
        function () {

            clearPhotoPreview();

            photoInput?.focus();
        }
    );
}


// =========================================================
// CLEAN UP PHOTO OBJECT URL
// =========================================================

window.addEventListener(
    "beforeunload",
    function () {

        if (currentPhotoPreviewUrl) {

            URL.revokeObjectURL(
                currentPhotoPreviewUrl
            );
        }
    }
);