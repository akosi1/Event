{{-- Terms and Conditions Modal Component --}}
<div id="termsModal" class="terms-modal" style="display: none;">
    <div class="terms-modal-overlay"></div>
    <div class="terms-modal-content">
        <!-- Header -->
        <div class="terms-modal-header">
            <h2><i class="fas fa-file-contract"></i> User Terms and Conditions</h2>
            <p>MCC Event and Portfolio Organizer System</p>
            <button type="button" class="terms-close-btn" onclick="closeTermsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="terms-modal-body">
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
        <div class="terms-modal-footer">
            <button type="button" class="btn-modal-close" onclick="closeTermsModal()">
                <i class="fas fa-times"></i> Close
            </button>
            <button type="button" class="btn-modal-accept" onclick="acceptTerms()">
                <i class="fas fa-check"></i> I Accept
            </button>
        </div>
    </div>
</div>

<style>
    /* Modal Overlay */
    .terms-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.3s ease;
    }

    .terms-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
    }

    .terms-modal-content {
        position: relative;
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        animation: slideUp 0.4s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modal Header */
    .terms-modal-header {
        background: linear-gradient(135deg, #6b2c91, #4a1a5c);
        color: white;
        padding: 24px 30px;
        border-radius: 16px 16px 0 0;
        position: relative;
    }

    .terms-modal-header h2 {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .terms-modal-header p {
        font-size: 14px;
        margin: 0;
        opacity: 0.9;
    }

    .terms-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .terms-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    /* Modal Body */
    .terms-modal-body {
        padding: 30px;
        overflow-y: auto;
        flex: 1;
        background: #f8f9fa;
    }

    .terms-modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .terms-modal-body::-webkit-scrollbar-track {
        background: #e0e0e0;
        border-radius: 10px;
    }

    .terms-modal-body::-webkit-scrollbar-thumb {
        background: #6b2c91;
        border-radius: 10px;
    }

    .terms-modal-body::-webkit-scrollbar-thumb:hover {
        background: #8e44ad;
    }

    /* Sections */
    .terms-section {
        background: white;
        padding: 20px;
        margin-bottom: 16px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .terms-section:last-child {
        margin-bottom: 0;
    }

    .terms-section h3 {
        color: #6b2c91;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .terms-section h3 i {
        font-size: 16px;
    }

    .terms-section p {
        color: #333;
        font-size: 14px;
        line-height: 1.7;
        margin: 0 0 12px 0;
        text-align: justify;
    }

    .terms-section p:last-child {
        margin-bottom: 0;
    }

    .terms-section ul {
        margin: 12px 0;
        padding-left: 24px;
    }

    .terms-section ul li {
        color: #333;
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 8px;
    }

    .terms-section ul li::marker {
        color: #6b2c91;
        font-weight: 600;
    }

    .contact-info {
        background: linear-gradient(135deg, #f0f0f0, #e8e8e8);
        padding: 16px;
        border-radius: 8px;
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

    .terms-footer-section p strong {
        color: #6b2c91;
    }

    /* Modal Footer */
    .terms-modal-footer {
        background: white;
        padding: 20px 30px;
        border-top: 2px solid #e0e0e0;
        border-radius: 0 0 16px 16px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-modal-close,
    .btn-modal-accept {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-modal-close {
        background: transparent;
        color: #6b2c91;
        border: 2px solid #6b2c91;
    }

    .btn-modal-close:hover {
        background: #6b2c91;
        color: white;
    }

    .btn-modal-accept {
        background: linear-gradient(135deg, #6b2c91, #4a1a5c);
        color: white;
        box-shadow: 0 4px 12px rgba(107, 44, 145, 0.3);
    }

    .btn-modal-accept:hover {
        background: linear-gradient(135deg, #8e44ad, #6b2c91);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(107, 44, 145, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .terms-modal {
            padding: 10px;
        }

        .terms-modal-content {
            max-height: 95vh;
        }

        .terms-modal-header {
            padding: 20px;
        }

        .terms-modal-header h2 {
            font-size: 20px;
        }

        .terms-modal-body {
            padding: 20px;
        }

        .terms-section {
            padding: 16px;
        }

        .terms-section h3 {
            font-size: 16px;
        }

        .terms-section p,
        .terms-section ul li {
            font-size: 13px;
        }

        .terms-modal-footer {
            padding: 16px 20px;
            flex-wrap: wrap;
        }

        .btn-modal-close,
        .btn-modal-accept {
            flex: 1;
            justify-content: center;
            min-width: 120px;
        }
    }
</style>

<script>
    function openTermsModal() {
        document.getElementById('termsModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeTermsModal() {
        document.getElementById('termsModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function acceptTerms() {
        const checkbox = document.getElementById('terms_accepted');
        if (checkbox) {
            checkbox.checked = true;
            
            // Update the checkbox label styling
            const checkboxWrapper = checkbox.closest('.terms-checkbox-wrapper');
            if (checkboxWrapper) {
                checkboxWrapper.classList.add('accepted');
            }
        }
        closeTermsModal();
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('termsModal');
        if (event.target === modal) {
            closeTermsModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('termsModal');
            if (modal && modal.style.display === 'flex') {
                closeTermsModal();
            }
        }
    });
</script>