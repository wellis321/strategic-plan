<?php
// About Us page - Publicly accessible
$title = 'About Us - ' . APP_NAME;
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white">
            <h1 class="text-4xl font-bold mb-4">About Us</h1>
            <p class="text-xl text-blue-100">
                Learn more about Simple Strategic Plans and our mission to help organisations create effective strategic plans.
            </p>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Our Mission</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Simple Strategic Plans is dedicated to making strategic planning accessible, straightforward, and effective for organisations of all sizes. We believe that every organisation, regardless of its resources, should have access to tools that help them plan for the future and track their progress.
        </p>
        <p class="text-gray-700 leading-relaxed">
            Our platform provides a user-friendly interface that guides organisations through the process of creating comprehensive strategic plans, setting goals, managing projects, and monitoring progress—all in one place.
        </p>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">What We Offer</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Easy-to-Use Platform</h3>
                <p class="text-gray-700">
                    Our intuitive interface makes it simple for organisations to create, manage, and share their strategic plans without technical expertise.
                </p>
            </div>
            <div class="bg-indigo-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Flexible Structure</h3>
                <p class="text-gray-700">
                    Customise your strategic plan to match your organisation's unique needs, with customisable fields, sections, and goals.
                </p>
            </div>
            <div class="bg-purple-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Progress Tracking</h3>
                <p class="text-gray-700">
                    Automatically track project progress through milestones, giving you real-time visibility into how your strategic plan is progressing.
                </p>
            </div>
            <div class="bg-pink-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Public Sharing</h3>
                <p class="text-gray-700">
                    Share your strategic plan with stakeholders, funders, and the public through clean, professional URLs that reflect your organisation's brand.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Who We Serve</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Simple Strategic Plans is designed for organisations across all sectors, including:
        </p>
        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
            <li>Charities and non-profit organisations</li>
            <li>Community groups and social enterprises</li>
            <li>Healthcare organisations</li>
            <li>Educational institutions</li>
            <li>Local authorities and public sector bodies</li>
            <li>Any organisation looking to create and manage a strategic plan</li>
        </ul>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Get in Touch</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            If you have questions about Simple Strategic Plans or would like to learn more about how we can help your organisation, please don't hesitate to contact us.
        </p>
        <p class="text-gray-700">
            <a href="mailto:<?= h(CONTACT_EMAIL) ?>" class="text-blue-600 hover:text-blue-800 underline font-semibold">Message Us</a>
        </p>
    </div>

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg p-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-blue-100 mb-6">
            Create your strategic plan today and start tracking your organisation's progress.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <?php if (!isLoggedIn()): ?>
                <a href="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                    Register Now
                </a>
                <a href="/how-to-create" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    Learn How to Create a Plan
                </a>
            <?php else: ?>
                <a href="/strategic-plan" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                    View Your Strategic Plan
                </a>
                <?php if (isOrganizationAdmin()): ?>
                    <a href="/organization/settings" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                        Start Building Your Plan
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
