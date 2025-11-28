<?php
$title = 'Cookie Policy - ' . APP_NAME;
ob_start();
?>

<div class="max-w-4xl mx-auto mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Cookie Policy</h1>

    <div class="bg-white shadow rounded-lg p-8 prose max-w-none">
        <p class="text-gray-600 mb-4">Last updated: <?= date('d F Y') ?></p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">What Are Cookies?</h2>
        <p class="text-gray-700 mb-4">
            Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently and provide information to the website owners.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">How We Use Cookies</h2>
        <p class="text-gray-700 mb-4">
            Simple Strategic Plans uses cookies for the following purposes:
        </p>
        <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
            <li><strong>Session Management:</strong> To keep you logged in and maintain your session</li>
            <li><strong>Preferences:</strong> To remember your preferences and settings</li>
            <li><strong>Cookie Consent:</strong> To remember your cookie consent preferences</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">Types of Cookies We Use</h2>

        <h3 class="text-xl font-semibold text-gray-900 mt-4 mb-2">Essential Cookies</h3>
        <p class="text-gray-700 mb-4">
            These cookies are necessary for the website to function properly. They enable core functionality such as security, network management, and accessibility.
        </p>

        <h3 class="text-xl font-semibold text-gray-900 mt-4 mb-2">Functional Cookies</h3>
        <p class="text-gray-700 mb-4">
            These cookies allow the website to remember choices you make (such as your username, language, or region) and provide enhanced, more personal features.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">Managing Cookies</h2>
        <p class="text-gray-700 mb-4">
            You can control and manage cookies in various ways. Please keep in mind that removing or blocking cookies can impact your user experience and parts of our website may no longer be fully accessible.
        </p>
        <p class="text-gray-700 mb-4">
            Most browsers automatically accept cookies, but you can modify your browser settings to decline cookies if you prefer. You can also delete cookies that have already been set.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">Third-Party Cookies</h2>
        <p class="text-gray-700 mb-4">
            Currently, Simple Strategic Plans does not use third-party cookies. If this changes in the future, we will update this policy accordingly.
        </p>

        <h2 class="text-2xl font-semibold text-gray-900 mt-6 mb-4">Contact</h2>
        <p class="text-gray-700 mb-4">
            If you have any questions about our use of cookies, please contact us at:
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
