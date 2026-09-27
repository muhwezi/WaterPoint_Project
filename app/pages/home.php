<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Report community water point faults and track repair progress.">
    <title>Water Infrastructure Tracker | Report and Track</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/public-home.css">
</head>
<body class="public-home">
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <?php require_once dirname(__DIR__) . '/layouts/staff.php'; staffNav('index.php'); ?>

    <main id="main-content">
        <section class="home-hero" aria-labelledby="home-title">
            <div class="home-shell hero-layout">
                <div class="hero-copy">
                    <p class="home-eyebrow">COMMUNITY WATER POINTS</p>
                    <h1 id="home-title">Report a water problem. Follow the repair.</h1>
                    <p class="hero-intro">If a water point is broken or water is not reaching your area, send a report. You’ll get a reference number to check what happens next.</p>
                    <div class="hero-actions">
                        <a class="home-button primary" href="report-fault.php">Report a Fault <span aria-hidden="true">→</span></a>
                        <a class="home-button secondary" href="track-report.php">Track a Report</a>
                    </div>
                    <p class="hero-help">Reporting is open to everyone. Your name and phone number are required so the team can follow up.</p>
                </div>
                <div class="hero-panel" aria-label="How reports move through the system">
                    <div class="hero-panel-top"><span class="panel-mark" aria-hidden="true">●</span> FROM REPORT TO REPAIR</div>
                    <ol class="progress-list">
                        <li><span class="step-number">01</span><div><strong>Report</strong><span>Describe the problem and its location.</span></div></li>
                        <li><span class="step-number">02</span><div><strong>Respond</strong><span>A coordinator reviews and assigns the fault.</span></div></li>
                        <li><span class="step-number">03</span><div><strong>Repair</strong><span>A technician records the work carried out.</span></div></li>
                        <li><span class="step-number">04</span><div><strong>Verify</strong><span>The coordinator checks the outcome.</span></div></li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="home-shell home-services" aria-labelledby="services-title">
            <div class="section-intro">
                <p class="home-eyebrow">GET STARTED</p>
                <h2 id="services-title">What would you like to do?</h2>
            </div>
            <div class="service-grid">
                <article class="service-card">
                    <span class="service-icon report-icon" aria-hidden="true">!</span>
                    <h3>Report a problem</h3>
                    <p>Use a water point code if you know it, or describe another water infrastructure fault and where it is.</p>
                    <a href="report-fault.php">Open reporting form <span aria-hidden="true">→</span></a>
                </article>
                <article class="service-card">
                    <span class="service-icon track-icon" aria-hidden="true">↗</span>
                    <h3>Track your report</h3>
                    <p>Enter the reference you received after reporting to see the current status and public updates.</p>
                    <a href="track-report.php">Track by reference <span aria-hidden="true">→</span></a>
                </article>
                <article class="service-card">
                    <span class="service-icon points-icon" aria-hidden="true">⌖</span>
                    <h3>Find a water point</h3>
                    <p>Browse registered water points, search by code or place, and see their recorded operating status.</p>
                    <a href="water-points.php">Browse water points <span aria-hidden="true">→</span></a>
                </article>
            </div>
        </section>

        <section class="home-shell home-note" aria-label="About the tracker">
            <div><span class="note-dot" aria-hidden="true"></span><strong>Community reporting supports faster follow-up.</strong></div>
            <p>Operating status describes the reported condition of infrastructure. It does not certify water quality.</p>
        </section>
    </main>

    <footer class="home-footer">
        <div class="home-shell footer-inner">
            <span>Water Infrastructure Tracker · SDG 6: Clean Water and Sanitation</span>
            <a href="login.php">Staff access</a>
        </div>
    </footer>
</body>
</html>
