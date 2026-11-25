<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Privacy Policy & Terms and Conditions - {{ env('APP_NAME') }}</title>
    <meta content="Privacy Policy and Terms and Conditions for {{ env('APP_NAME') }}." name="description">

    <link href="{{ asset('ui/css/assets/img/fav.png') }}" rel="icon" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('ui/css/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/css/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #F7931A;
            --primary-dark: #E8841A;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-dark);
            background: var(--bg-light);
            line-height: 1.7;
        }

        .navbar {
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-dark);
        }

        .logo img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .page-hero {
            background: linear-gradient(135deg, #F7931A 0%, #FFA64D 100%);
            color: white;
            padding: 3rem 0 2.5rem;
            text-align: center;
        }

        .page-hero h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .page-hero p {
            opacity: 0.95;
            font-size: 1rem;
        }

        .legal-section {
            max-width: 800px;
            margin: -1.5rem auto 3rem;
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .legal-card {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .legal-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
        }

        .legal-card h2:first-of-type {
            margin-top: 0;
        }

        .legal-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 1.5rem 0 0.75rem;
            color: var(--text-dark);
        }

        .legal-card p,
        .legal-card li {
            color: var(--text-gray);
            margin-bottom: 0.75rem;
        }

        .legal-card ul {
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }

        .legal-card li {
            margin-bottom: 0.4rem;
        }

        .contact-box {
            background: var(--bg-light);
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-top: 1rem;
        }

        .contact-box a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .contact-box a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }

        .back-link:hover {
            color: var(--primary-dark);
        }

        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
        }

        .footer p {
            opacity: 0.8;
            margin-top: 1rem;
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .page-hero h1 {
                font-size: 1.5rem;
            }

            .legal-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('ui/css/assets/img/logo.png') }}" alt="{{ env('APP_NAME') }} Logo">
                <span>{{ env('APP_NAME') }}</span>
            </a>
        </div>
    </nav>

    <section class="page-hero">
        <div class="container">
            <h1>Privacy Policy &amp; Terms and Conditions</h1>
            <p>Last updated: December 30, 2025</p>
        </div>
    </section>

    <section class="legal-section">
        <div class="legal-card">
            <a href="{{ url('/') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>

            <h2>Privacy Policy</h2>

            <h3>1. Introduction</h3>
            <p>This Privacy Policy explains how our application ("App") collects, uses, and protects your information. We are committed to ensuring your privacy and security while using our App.</p>

            <h3>2. Information We Collect</h3>
            <p>Our App may collect the following types of information:</p>
            <ul>
                <li><strong>Camera Access:</strong> We request permission to access your device's camera to enable photo and video capture features within the App.</li>
                <li><strong>Media Files:</strong> Photos and videos captured through the App are stored on your device.</li>
                <li><strong>Device Information:</strong> We may collect basic device information such as device model, operating system version, and app version for troubleshooting and improvement purposes.</li>
            </ul>

            <h3>3. How We Use Your Information</h3>
            <p>The information collected is used for:</p>
            <ul>
                <li>Providing core functionality of the App (camera features)</li>
                <li>Improving user experience and app performance</li>
                <li>Troubleshooting technical issues</li>
                <li>Developing new features and updates</li>
            </ul>

            <h3>4. Camera Permission</h3>
            <p><strong>Why we need it:</strong> The camera permission is essential for the App's core functionality, allowing you to capture photos and videos.</p>
            <p><strong>How we use it:</strong> Camera access is only activated when you explicitly use camera features within the App. We do not access your camera in the background or without your knowledge.</p>
            <p><strong>Your control:</strong> You can revoke camera permission at any time through your device settings.</p>

            <h3>5. Data Storage and Security</h3>
            <ul>
                <li>All photos and videos are stored locally on your device</li>
                <li>We do not automatically upload your media to external servers</li>
                <li>We implement appropriate security measures to protect your data</li>
                <li>You have full control over your media files and can delete them at any time</li>
            </ul>

            <h3>6. Data Sharing</h3>
            <p>We do not sell, trade, or share your personal information or media files with third parties, except:</p>
            <ul>
                <li>When required by law or legal process</li>
                <li>To protect our rights and safety</li>
                <li>With your explicit consent</li>
            </ul>

            <h3>7. Children's Privacy</h3>
            <p>Our App does not knowingly collect personal information from children under 13 years of age. If you are a parent or guardian and believe your child has provided us with personal information, please contact us.</p>

            <h3>8. Your Rights</h3>
            <p>You have the right to:</p>
            <ul>
                <li>Access your data</li>
                <li>Delete your data</li>
                <li>Revoke permissions at any time</li>
                <li>Request information about data collection practices</li>
            </ul>

            <h3>9. Changes to Privacy Policy</h3>
            <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date.</p>

            <h2>Terms and Conditions</h2>

            <h3>1. Acceptance of Terms</h3>
            <p>By downloading, installing, or using this App, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use the App.</p>

            <h3>2. License to Use</h3>
            <p>We grant you a limited, non-exclusive, non-transferable, revocable license to use the App for personal, non-commercial purposes in accordance with these Terms.</p>

            <h3>3. User Responsibilities</h3>
            <p>You agree to:</p>
            <ul>
                <li>Use the App in compliance with all applicable laws and regulations</li>
                <li>Not use the App for any illegal or unauthorized purpose</li>
                <li>Not attempt to reverse engineer, decompile, or disassemble the App</li>
                <li>Not use the App to capture content that violates others' rights or privacy</li>
                <li>Respect intellectual property rights and privacy of others</li>
            </ul>

            <h3>4. Camera Usage Guidelines</h3>
            <p>When using camera features, you must:</p>
            <ul>
                <li>Comply with all local laws regarding photography and video recording</li>
                <li>Obtain consent before recording individuals in private settings</li>
                <li>Respect "no photography" signs and restricted areas</li>
                <li>Not use the App for surveillance or harassment</li>
            </ul>

            <h3>5. Intellectual Property</h3>
            <p>The App and its original content, features, and functionality are owned by us and are protected by international copyright, trademark, and other intellectual property laws.</p>

            <h3>6. Disclaimer of Warranties</h3>
            <p>The App is provided "as is" and "as available" without warranties of any kind, either express or implied, including but not limited to:</p>
            <ul>
                <li>Fitness for a particular purpose</li>
                <li>Non-infringement</li>
                <li>Uninterrupted or error-free operation</li>
            </ul>

            <h3>7. Limitation of Liability</h3>
            <p>We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from:</p>
            <ul>
                <li>Your use or inability to use the App</li>
                <li>Unauthorized access to your data</li>
                <li>Any content created using the App</li>
                <li>Any other matter relating to the App</li>
            </ul>

            <h3>8. User-Generated Content</h3>
            <p>You retain all rights to photos and videos you create using the App. You are solely responsible for the content you create and its consequences.</p>

            <h3>9. Termination</h3>
            <p>We reserve the right to terminate or suspend your access to the App at any time, without notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties.</p>

            <h3>10. Updates and Modifications</h3>
            <p>We reserve the right to modify or discontinue the App at any time. We may also update these Terms from time to time. Continued use of the App after changes constitutes acceptance of the updated Terms.</p>

            <h3>11. Governing Law</h3>
            <p>These Terms shall be governed by and construed in accordance with the laws of your jurisdiction, without regard to its conflict of law provisions.</p>

            <h3>12. Severability</h3>
            <p>If any provision of these Terms is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary so that these Terms shall otherwise remain in full force and effect.</p>

            <h2>Contact Us</h2>
            <p>If you have any questions about this Privacy Policy or Terms and Conditions, please contact us at:</p>
            <div class="contact-box">
                <p style="margin-bottom: 0.35rem;"><strong>Email:</strong> <a href="mailto:info@macroit.org">info@macroit.org</a></p>
                <p style="margin-bottom: 0;"><strong>Developer:</strong> MACROIT INFORMATION TECHNOLOGY</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="logo" style="justify-content: center; color: white;">
                <img src="{{ asset('ui/css/assets/img/logo.png') }}" alt="{{ env('APP_NAME') }} Logo">
                <span>{{ env('APP_NAME') }}</span>
            </div>
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All Rights Reserved</p>
            <p style="margin-top: 0.5rem;">
                <a href="{{ route('terms') }}">Privacy Policy &amp; Terms</a>
            </p>
            <p style="margin-top: 0.5rem;">Olympia, 14 Zambezi road, Lusaka, LSK 10101 | info@bit2kwacha.info</p>
        </div>
    </footer>
</body>

</html>
