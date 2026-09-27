<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers.php';

function staffHeader(string $title, string $active, array $styles = []): void
{
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . h($title) . ' | Water Infrastructure Tracker</title>';
    echo '<link rel="stylesheet" href="assets/style.css">';
    foreach ($styles as $style) {
        echo '<link rel="stylesheet" href="assets/' . h($style) . '">';
    }
    echo '</head><body>';
    staffNav($active);
}

function staffNav(string $active): void
{
    $signedIn = function_exists('isLoggedIn') && isLoggedIn();
    if ($signedIn) {
        $links = ['dashboard.php' => 'Dashboard'];
        if (userHasRole(['ADMIN', 'COORDINATOR'])) {
            $links['water-points.php'] = 'Water Points';
            $links['faults.php'] = 'Faults';
            $links['reports.php'] = 'Reports';
            $links['users.php'] = 'Users';
        }
        if (userHasRole('TECHNICIAN')) {
            $links['technician.php'] = 'My Repairs';
        }
        $links['logout.php'] = 'Sign Out';
    } else {
        $links = [
            'index.php' => 'Home',
            'water-points.php' => 'Water Points',
            'report-fault.php' => 'Report',
            'track-report.php' => 'Track',
            'login.php' => 'Staff Login',
        ];
    }
    $currentLink = match ($active) {
        'fault-details.php' => 'faults.php',
        'water-point.php' => 'water-points.php',
        'register-water-point.php' => 'water-points.php',
        default => $active,
    };
    $navLabel = $signedIn ? 'Staff navigation' : 'Public navigation';
    echo '<header class="site-header app-header"><div class="app-header-inner">';
    echo '<a class="app-brand" href="index.php">Water Point Tracker</a>';
    echo '<nav class="site-nav desktop-nav" aria-label="' . $navLabel . '">';
    renderHeaderLinks($links, $currentLink);
    echo '</nav>';
    echo '<details class="mobile-menu"><summary>Menu</summary>';
    echo '<nav aria-label="' . $navLabel . '">';
    renderHeaderLinks($links, $currentLink);
    echo '</nav></details></div></header>';
}

function renderHeaderLinks(array $links, string $currentLink): void
{
    foreach ($links as $path => $label) {
        echo '<a href="' . h($path) . '"' . ($currentLink === $path ? ' aria-current="page"' : '') . '>' . h($label) . '</a>';
    }
}

function staffFooter(): void
{
    echo '</body></html>';
}
