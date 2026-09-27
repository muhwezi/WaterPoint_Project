"use strict";


// =========================================================
// PUBLIC WATER POINT MAP
// =========================================================

const mapElement =
    document.getElementById(
        "public-water-point-map"
    );


if (
    mapElement &&
    typeof L !== "undefined"
) {

    const latitude =
        Number.parseFloat(
            mapElement.dataset.latitude
        );

    const longitude =
        Number.parseFloat(
            mapElement.dataset.longitude
        );

    const waterPointName =
        mapElement.dataset.name
        || "Water point";

    const waterPointCode =
        mapElement.dataset.code
        || "";


    if (
        Number.isFinite(
            latitude
        ) &&
        Number.isFinite(
            longitude
        )
    ) {

        const map =
            L.map(
                "public-water-point-map",
                {
                    scrollWheelZoom:
                        false
                }
            ).setView(
                [
                    latitude,
                    longitude
                ],
                16
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


        const marker =
            L.marker(
                [
                    latitude,
                    longitude
                ]
            ).addTo(map);


        marker
            .bindPopup(
                "<strong>" +
                escapeHtml(
                    waterPointName
                ) +
                "</strong><br>" +
                escapeHtml(
                    waterPointCode
                )
            )
            .openPopup();


        window.setTimeout(
            function () {

                map.invalidateSize();

            },
            150
        );
    }
}


// =========================================================
// SAFE POPUP TEXT
// =========================================================

function escapeHtml(
    value
) {

    const element =
        document.createElement(
            "div"
        );

    element.textContent =
        String(value);

    return element.innerHTML;
}