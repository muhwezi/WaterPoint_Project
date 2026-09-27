"use strict";


// =========================================================
// PASSWORD VISIBILITY
// =========================================================

const passwordField =
    document.getElementById(
        "password"
    );

const toggleButton =
    document.getElementById(
        "toggle-password"
    );


if (
    passwordField &&
    toggleButton
) {

    toggleButton.addEventListener(
        "click",
        function () {

            const passwordVisible =
                passwordField.type ===
                "text";


            passwordField.type =
                passwordVisible
                    ? "password"
                    : "text";


            toggleButton.textContent =
                passwordVisible
                    ? "Show"
                    : "Hide";


            toggleButton.setAttribute(
                "aria-label",
                passwordVisible
                    ? "Show password"
                    : "Hide password"
            );


            passwordField.focus();
        }
    );
}