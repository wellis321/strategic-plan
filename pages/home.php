<?php
// Home/Landing page - public facing page that "sells" Simple Strategic Plans
// This page is shown to non-logged-in users, or can be accessed by anyone

$title = 'Simple Strategic Plans - Plan, Track, and Achieve Your Organisation\'s Goals';
ob_start();
?>

<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img src="/static/images/strategic-black-logo.png" alt="Simple Strategic Plans" class="h-16 md:h-20 w-auto">
                </div>
                <h1 class="text-5xl font-bold text-gray-900 mb-6">
                    Simple Strategic Plans
                </h1>
                <p class="text-xl text-gray-700 mb-8 max-w-3xl mx-auto">
                    The comprehensive platform for organisations of all types to create, manage, and track their strategic plans.
                    From charities to public sector bodies, we help you turn vision into action.
                </p>
                <div class="flex justify-center space-x-4">
                    <?php if (!isLoggedIn()): ?>
                        <a href="/request-organization" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            Get Started
                        </a>
                        <a href="/login" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors">
                            Login
                        </a>
                    <?php else: ?>
                        <a href="/strategic-plan" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            View Your Strategic Plan
                        </a>
                        <a href="/dashboard" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors">
                            Go to Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Everything You Need to Manage Your Strategic Plan</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Powerful features designed to help organisations of all sizes plan effectively and track progress
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Multiple Strategic Plans</h3>
                    <p class="text-gray-600">
                        Create multiple strategic plans for different time periods. Each plan gets its own public URL that you can share with stakeholders.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Track Progress</h3>
                    <p class="text-gray-600">
                        Monitor project progress automatically through milestones. Get real-time insights into what's on track, at risk, or delayed.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Team Collaboration</h3>
                    <p class="text-gray-600">
                        Assign project leads, working groups, and responsible directors. Keep everyone aligned with clear ownership and accountability.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Customisable Structure</h3>
                    <p class="text-gray-600">
                        Tailor your strategic plan to match your organisation's structure. Add custom sections, rename fields, and organise content your way.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Rich Content</h3>
                    <p class="text-gray-600">
                        Add rich text content with formatting. Include your organisation's vision, mission, values, and custom sections with full control.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="text-blue-600 mb-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Secure & Private</h3>
                    <p class="text-gray-600">
                        Multi-tenant architecture ensures your data is completely isolated. Choose to publish plans publicly or keep them private.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Who It's For Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Perfect for All Types of Organisations</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Whether you're a charity, public sector body, social enterprise, or any organisation with strategic goals
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                    <div class="text-blue-600 mb-4 flex justify-center">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Public Sector</h3>
                    <p class="text-sm text-gray-600">Local authorities, government bodies, and public services</p>
                </div>
                <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                    <div class="text-blue-600 mb-4 flex justify-center">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Charities</h3>
                    <p class="text-sm text-gray-600">Third sector organisations and non-profits</p>
                </div>
                <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                    <div class="text-blue-600 mb-4 flex justify-center">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Social Enterprises</h3>
                    <p class="text-sm text-gray-600">Organisations with social and environmental missions</p>
                </div>
                <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                    <div class="text-blue-600 mb-4 flex justify-center">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">All Organisations</h3>
                    <p class="text-sm text-gray-600">Any organisation with strategic planning needs</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">1</div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Register Your Organisation</h3>
                            <p class="text-gray-600">Sign up with your organisation's email domain. Your organisation will be set up and ready to go.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">2</div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Create Your Strategic Plan</h3>
                            <p class="text-gray-600">Add your vision, mission, values, and strategic goals. Customise the structure to match your organisation's needs.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">3</div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Add Projects & Track Progress</h3>
                            <p class="text-gray-600">Link projects to goals, set milestones, and track progress. The system automatically calculates completion percentages.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">4</div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Share & Report</h3>
                            <p class="text-gray-600">Publish your strategic plan with a custom URL. Generate reports and share progress with stakeholders.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-xl text-blue-100 mb-8">
                Join organisations across the UK who are using Simple Strategic Plans to achieve their goals
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="/request-organization" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors inline-block">
                    Register Your Organisation
                </a>
            <?php else: ?>
                <a href="/strategic-plan" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors inline-block">
                    View Your Strategic Plan
                </a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
