"use strict";

const faultMapElement =
    document.getElementById(
        "staff-fault-map"
    );


if (
    faultMapElement &&
    typeof L !== "undefined"
) {

    const latitude =
        Number.parseFloat(
            faultMapElement
                .dataset
                .latitude
        );


    const longitude =
        Number.parseFloat(
            faultMapElement
                .dataset
                .longitude
        );


    const reference =
        faultMapElement
            .dataset
            .reference
        || "Fault";


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
                "staff-fault-map"
            ).setView(
                [
                    latitude,
                    longitude
                ],
                17
            );


        L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            {
                maxZoom: 19,

                attribution:
                    "&copy; OpenStreetMap contributors"
            }
        ).addTo(map);


        L.marker([
            latitude,
            longitude
        ])
            .addTo(map)
            .bindPopup(
                reference
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