<?php
$title = 'Terms of Service - ' . APP_NAME;
ob_start();
?>

<div class="max-w-4xl mx-auto mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Terms of Service</h1>

    <div class="bg-white shadow rounded-lg p-8 prose max-w-none">
        <p class="text-gray-600 mb-4">Last updated: <?= date('d F Y') ?></p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">1. Acceptance of Terms</h2>
        <p class="text-gray-700 mb-4">
            By accessing and using Simple Strategic Plans, you accept and agree to be bound by the terms and provision of this agreement.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">2. Use License</h2>
        <p class="text-gray-700 mb-4">
            Permission is granted to temporarily use Simple Strategic Plans for your organisation's strategic planning purposes. This is the grant of a license, not a transfer of title, and under this license you may not:
        </p>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
            <li>Modify or copy the materials</li>
            <li>Use the materials for any commercial purpose without explicit permission</li>
            <li>Attempt to reverse engineer any software contained in Simple Strategic Plans</li>
            <li>Remove any copyright or other proprietary notations from the materials</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">3. User Accounts</h2>
        <p class="text-gray-700 mb-4">
            You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">4. Data and Content</h2>
        <p class="text-gray-700 mb-4">
            You retain all rights to the content you upload to Simple Strategic Plans. You are responsible for ensuring you have the right to use and share any content you upload.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">5. Disclaimer</h2>
        <p class="text-gray-700 mb-4">
            The materials on Simple Strategic Plans are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">6. Limitations</h2>
        <p class="text-gray-700 mb-4">
            In no event shall Simple Strategic Plans or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use Simple Strategic Plans.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">7. Contact</h2>
        <p class="text-gray-700 mb-4">
            If you have any questions about these Terms of Service, please contact us at:
        </p>
        <p class="text-gray-700 mb-4">
            <a href="mailto:<?= h(CONTACT_EMAIL) ?>" class="text-blue-600 hover:text-blue-800 underline font-semibold">Message Us</a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
