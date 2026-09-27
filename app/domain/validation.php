<?php

declare(strict_types=1);

function normalizeUgandanPhone(
    string $phone
): ?string {

    $phone =
        preg_replace(
            '/[\s\-\(\)]/',
            '',
            trim($phone)
        );

    if ($phone === null) {
        return null;
    }


    // +2567XXXXXXXX
    if (
        preg_match(
            '/^\+256(7\d{8})$/',
            $phone,
            $matches
        )
    ) {

        return
            '+256' .
            $matches[1];
    }


    // 2567XXXXXXXX
    if (
        preg_match(
            '/^256(7\d{8})$/',
            $phone,
            $matches
        )
    ) {

        return
            '+256' .
            $matches[1];
    }


    // 07XXXXXXXX
    if (
        preg_match(
            '/^0(7\d{8})$/',
            $phone,
            $matches
        )
    ) {

        return
            '+256' .
            $matches[1];
    }


    return null;
}


// =========================================================
