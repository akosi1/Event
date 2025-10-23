<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ===== GLOBAL RESET ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }

        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 26, 92, 0.8), rgba(107, 44, 145, 0.8), rgba(61, 26, 120, 0.8));
            z-index: 1;
        }

        /* ===== TERMS PAGE WRAPPER ===== */
        .terms-page-wrapper {
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .terms-page-container {
            width: 100%;
            max-width: 900px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== HEADER ===== */
        .terms-header {
            background: linear-gradient(135deg, #6b2c91, #4a1a5c);
            color: white;
            padding: 30px 40px;
            border-bottom: 4px solid #8e44ad;
            text-align: center;
        }

        .terms-header h1 {
            font-size: 32px;
            font-weight: 700;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .terms-header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }

        /* ===== CONTENT BODY ===== */
        .terms-body {
            padding: 40px;
            overflow-y: auto;
            max-height: calc(100vh - 300px);
            background: #f8f9fa;
        }

        .terms-body::-webkit-scrollbar {
            width: 10px;
        }

        .terms-body::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 10px;
        }

        .terms-body::-webkit-scrollbar-thumb {
            background: #6b2c91;
            border-radius: 10px;
        }

        .terms-body::-webkit-scrollbar-thumb:hover {
            background: #8e44ad;
        }

        /* ===== SECTIONS ===== */
        .terms-section {
            background: white;
            padding: 28px;
            margin-bottom: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .terms-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(107, 44, 145, 0.15);
        }

        .terms-section:last-child {
            margin-bottom: 0;
        }

        .terms-section h3 {
            color: #6b2c91;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 18px 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .terms-section h3 i {
            font-size: 20px;
        }

        .terms-section p {
            color: #333;
            font-size: 15px;
            line-height: 1.8;
            margin: 0 0 14px 0;
            font-family: 'Poppins', sans-serif;
            text-align: justify;
        }

        .terms-section p:last-child {
            margin-bottom: 0;
        }

        .terms-section ul {
            margin: 14px 0;
            padding-left: 28px;
        }

        .terms-section ul li {
            color: #333;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 10px;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        .terms-section ul li::marker {
            color: #6b2c91;
            font-weight: 600;
        }

        .terms-section ul li:last-child {
            margin-bottom: 0;
        }

        /* ===== SPECIAL STYLES ===== */
        .contact-info {
            background: linear-gradient(135deg, #f0f0f0, #e8e8e8);
            padding: 20px;
            border-radius: 8px;
            font-size: 15px !important;
            border-left: 4px solid #6b2c91;
        }

        .contact-info strong {
            color: #6b2c91;
            font-weight: 600;
        }

        .terms-footer-section {
            background: linear-gradient(135deg, rgba(107, 44, 145, 0.08), rgba(74, 26, 92, 0.08));
            border-left: 4px solid #6b2c91;
        }

        .terms-footer-section p {
            margin-bottom: 10px;
        }

        .terms-footer-section p strong {
            color: #6b2c91;
        }

        /* ===== FOOTER ACTIONS ===== */
        .terms-footer {
            background: white;
            padding: 24px 40px;
            border-top: 2px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: transparent;
            color: #6b2c91;
            border: 2px solid #6b2c91;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-back:hover {
            background: #6b2c91;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 44, 145, 0.3);
        }

        .btn-accept {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: linear-gradient(135deg, #6b2c91, #4a1a5c);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(107, 44, 145, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-accept:hover {
            background: linear-gradient(135deg, #8e44ad, #6b2c91);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(107, 44, 145, 0.4);
        }

        .btn-accept:active {
            transform: translateY(0);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .terms-page-wrapper {
                padding: 20px 15px;
            }

            .terms-header {
                padding: 24px 24px;
            }

            .terms-header h1 {
                font-size: 26px;
                letter-spacing: 1px;
            }

            .terms-header p {
                font-size: 13px;
            }

            .terms-body {
                padding: 24px;
                max-height: calc(100vh - 280px);
            }

            .terms-section {
                padding: 20px;
                margin-bottom: 20px;
            }

            .terms-section h3 {
                font-size: 19px;
            }

            .terms-section p,
            .terms-section ul li {
                font-size: 14px;
                line-height: 1.7;
            }

            .terms-footer {
                padding: 20px 24px;
                flex-direction: column-reverse;
            }

            .btn-back,
            .btn-accept {
                width: 100%;
                justify-content: center;
                padding: 12px 24px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .terms-page-wrapper {
                padding: 15px 10px;
            }

            .terms-header {
                padding: 20px 20px;
            }

            .terms-header h1 {
                font-size: 22px;
                letter-spacing: 0.5px;
            }

            .terms-header p {
                font-size: 12px;
            }

            .terms-body {
                padding: 20px;
                max-height: calc(100vh - 260px);
            }

            .terms-section {
                padding: 16px;
                margin-bottom: 16px;
            }

            .terms-section h3 {
                font-size: 17px;
            }

            .terms-section h3 i {
                font-size: 16px;
            }

            .terms-section p,
            .terms-section ul li {
                font-size: 13px;
                line-height: 1.6;
            }

            .terms-section ul {
                padding-left: 22px;
            }

            .contact-info {
                padding: 16px;
                font-size: 13px !important;
            }

            .terms-footer {
                padding: 16px 20px;
            }

            .btn-back,
            .btn-accept {
                padding: 11px 20px;
                font-size: 14px;
            }
        }
    </style>

    <div class="terms-page-wrapper">
        <div class="terms-page-container">
            <!-- Header -->
            <div class="terms-header">
                <h1>User Terms and Conditions</h1>
                <p>MCC Event and Portfolio Organizer System</p>
            </div>

            <!-- Body -->
            <div class="terms-body">
                <!-- Section 1 -->
                <div class="terms-section">
                    <h3><i class="fas fa-file-signature"></i> 1. Acceptance of Terms</h3>
                    <p>By creating an account or accessing the MCC Event and Portfolio Organizer System, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, you must not use this system.</p>
                </div>

                <!-- Section 2 -->
                <div class="terms-section">
                    <h3><i class="fas fa-user-circle"></i> 2. User Accounts</h3>
                    <p>To access the full features of the system, you must register for an account by providing accurate, complete, and current information. You are solely responsible for maintaining the confidentiality of your account credentials, including your password. Any activity that occurs under your account is your responsibility. If you suspect unauthorized access to your account, you must notify the system administrator immediately.</p>
                </div>

                <!-- Section 3 -->
                <div class="terms-section">
                    <h3><i class="fas fa-check-circle"></i> 3. Proper Use of the System</h3>
                    <p>This platform is intended exclusively for event management and portfolio organization purposes. Users agree to use the system in a lawful and respectful manner. Prohibited behaviors include, but are not limited to:</p>
                    <ul>
                        <li>Posting spam, false information, or misleading content</li>
                        <li>Uploading offensive, defamatory, or inappropriate materials</li>
                        <li>Impersonating other individuals or organizations</li>
                        <li>Disrupting the normal operation of the system</li>
                    </ul>
                </div>

                <!-- Section 4 -->
                <div class="terms-section">
                    <h3><i class="fas fa-copyright"></i> 4. Content Ownership</h3>
                    <p>You retain full ownership of all portfolios, documents, images, and other materials you upload to the system. By uploading content, you grant the MCC Event and Portfolio Organizer System a non-exclusive, royalty-free license to display, store, and distribute your content solely for the purpose of providing the service. You represent and warrant that you have the necessary rights to upload and share all content you submit.</p>
                </div>

                <!-- Section 5 -->
                <div class="terms-section">
                    <h3><i class="fas fa-shield-alt"></i> 5. Privacy and Data Use</h3>
                    <p>The system collects limited personal information necessary for account creation and management, including your name, email address, department affiliation, and uploaded files. This information is used exclusively for:</p>
                    <ul>
                        <li>Account authentication and management</li>
                        <li>Event coordination and portfolio display</li>
                        <li>Communication regarding system updates or events</li>
                    </ul>
                    <p>We are committed to protecting your privacy and will not share your personal information with third parties without your consent, except as required by law.</p>
                </div>

                <!-- Section 6 -->
                <div class="terms-section">
                    <h3><i class="fas fa-ban"></i> 6. Prohibited Activities</h3>
                    <p>Users must not engage in any activity that compromises the security, integrity, or availability of the system. This includes:</p>
                    <ul>
                        <li>Attempting to gain unauthorized access to system resources or other user accounts</li>
                        <li>Uploading malicious software, viruses, or harmful code</li>
                        <li>Reverse engineering, decompiling, or attempting to extract source code</li>
                        <li>Using automated tools or bots to access the system</li>
                        <li>Overloading system resources or attempting denial-of-service attacks</li>
                    </ul>
                </div>

                <!-- Section 7 -->
                <div class="terms-section">
                    <h3><i class="fas fa-user-times"></i> 7. Account Termination</h3>
                    <p>The system administrator reserves the right to suspend or terminate user accounts at any time, with or without notice, for violations of these Terms and Conditions. Reasons for account termination may include, but are not limited to:</p>
                    <ul>
                        <li>Violation of proper use guidelines</li>
                        <li>Posting inappropriate or offensive content</li>
                        <li>Engaging in prohibited activities</li>
                        <li>Providing false or misleading information during registration</li>
                    </ul>
                    <p>Users whose accounts have been terminated may not re-register without explicit permission from the administrator.</p>
                </div>

                <!-- Section 8 -->
                <div class="terms-section">
                    <h3><i class="fas fa-tools"></i> 8. System Updates and Maintenance</h3>
                    <p>The MCC Event and Portfolio Organizer System may undergo periodic maintenance, updates, and improvements. During these periods, the system may be temporarily unavailable. We will make reasonable efforts to notify users in advance of scheduled maintenance, but we do not guarantee uninterrupted access to the system.</p>
                </div>

                <!-- Section 9 -->
                <div class="terms-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> 9. Limitation of Liability</h3>
                    <p>The MCC Event and Portfolio Organizer System is provided "as is" without warranties of any kind, either express or implied. The developers and administrators are not responsible for:</p>
                    <ul>
                        <li>Loss of data due to technical failures or user error</li>
                        <li>Misuse of information by other users</li>
                        <li>Damages resulting from unauthorized access to user accounts</li>
                        <li>Interruptions in service or system availability</li>
                    </ul>
                    <p>Users are advised to maintain backups of important documents and information stored on the system.</p>
                </div>

                <!-- Section 10 -->
                <div class="terms-section">
                    <h3><i class="fas fa-envelope"></i> 10. Contact Information</h3>
                    <p>For questions, concerns, or support regarding these Terms and Conditions or the use of the MCC Event and Portfolio Organizer System, please contact us at:</p>
                    <p class="contact-info">
                        <strong>Email:</strong> events@gmail.com<br>
                        <strong>Subject Line:</strong> User Support - Terms and Conditions Inquiry
                    </p>
                </div>

                <!-- Footer Section -->
                <div class="terms-section terms-footer-section">
                    <p><strong>Last Updated:</strong> October 23, 2025</p>
                    <p>By continuing to use this system, you acknowledge that you have read and accepted these Terms and Conditions.</p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="terms-footer">
                <a href="{{ route('login') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
                <button class="btn-accept" onclick="window.location.href='{{ route('login') }}'">
                    <i class="fas fa-check"></i> I Understand
                </button>
            </div>
        </div>
    </div>
</x-guest-layout>