<?php
$title = 'Privacy Policy - ' . APP_NAME;
ob_start();
?>

<div class="max-w-4xl mx-auto mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>

    <div class="bg-white shadow rounded-lg p-8 prose max-w-none">
        <p class="text-gray-600 mb-4">Last updated: <?= date('d F Y') ?></p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">1. Information We Collect</h2>
        <p class="text-gray-700 mb-4">
            We collect information that you provide directly to us, including:
        </p>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
            <li>Name and contact information (email address)</li>
            <li>Organisation details</li>
            <li>Strategic plan content and data you upload</li>
            <li>Account credentials and preferences</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">2. How We Use Your Information</h2>
        <p class="text-gray-700 mb-4">
            We use the information we collect to:
        </p>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
            <li>Provide, maintain, and improve our services</li>
            <li>Process your transactions and send related information</li>
            <li>Send you technical notices and support messages</li>
            <li>Respond to your comments and questions</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">3. Data Security</h2>
        <p class="text-gray-700 mb-4">
            We implement appropriate technical and organisational measures to protect your personal information against unauthorised access, alteration, disclosure, or destruction. Your data is stored securely and is isolated per organisation.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">4. Data Sharing</h2>
        <p class="text-gray-700 mb-4">
            We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
        </p>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
            <li>With your consent</li>
            <li>To comply with legal obligations</li>
            <li>To protect our rights and safety</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">5. Your Rights</h2>
        <p class="text-gray-700 mb-4">
            You have the right to:
        </p>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
            <li>Access your personal information</li>
            <li>Correct inaccurate data</li>
            <li>Request deletion of your data</li>
            <li>Object to processing of your data</li>
            <li>Data portability</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">6. Cookies</h2>
        <p class="text-gray-700 mb-4">
            We use cookies to enhance your experience. You can control cookies through your browser settings. For more information, see our <a href="/cookies" class="text-blue-600 hover:text-blue-800">Cookie Policy</a>.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">7. Contact</h2>
        <p class="text-gray-700 mb-4">
            If you have any questions about this Privacy Policy, please contact us at:
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
