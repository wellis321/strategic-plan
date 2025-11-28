<?php
// Example/In-Progress Strategic Plan page - Shows a plan that's being worked on
// Publicly accessible - no login required

$title = 'In-Progress Strategic Plan Example - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <!-- Hero Section with Image -->
    <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg overflow-hidden mb-8 shadow-xl">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative p-12 text-white">
            <div class="flex items-center mb-4">
                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h1 class="text-4xl font-bold">In-Progress Strategic Plan Example</h1>
            </div>
            <p class="text-xl text-blue-100 max-w-3xl">
                This example shows a strategic plan that's actively being worked on, with projects at various stages of completion. This is what your plan might look like as you build it.
            </p>
            <div class="mt-4">
                <a href="/example-plan" class="text-blue-100 hover:text-white underline text-sm">
                    View a completed plan example →
                </a>
            </div>
        </div>
        <!-- Image Placeholder -->
        <div class="relative h-64 bg-gray-300 flex items-center justify-center">
            <div class="text-center text-gray-500">
                <svg class="h-16 w-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm">Hero Image Placeholder</p>
                <p class="text-xs">Replace with your organisation's hero image</p>
            </div>
        </div>
    </div>

    <!-- About Us Section with Image -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="md:flex">
            <div class="md:w-1/3 bg-gray-200 flex items-center justify-center min-h-[200px]">
                <div class="text-center text-gray-500 p-4">
                    <svg class="h-12 w-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-xs">About Us Image</p>
                </div>
            </div>
            <div class="md:w-2/3 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About Us</h2>
                <div class="text-gray-700 leading-relaxed space-y-4">
                    <p class="font-semibold text-lg">We are Example Mental Health Organisation</p>
                    <p>A pioneering charity providing dedicated services for people with mild to serious and enduring mental ill health.</p>
                    <p>We support people on their journey to better mental health, by working with each person to find their own way forward. The power of people's lived experience enables us to provide pioneering services which transform lives.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vision, Mission, Values Section -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Vision Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-600 rounded-full p-3 mr-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Our Vision</h3>
            </div>
            <p class="text-gray-700 leading-relaxed">
                To be the leading provider of person-centered mental health support, empowering individuals to live fulfilling and independent lives in their communities.
            </p>
        </div>

        <!-- Mission Card -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 shadow-lg rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-600 rounded-full p-3 mr-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Our Mission</h3>
            </div>
            <p class="text-gray-700 leading-relaxed">
                We provide high-quality, person-centered mental health support services that enable individuals to achieve their goals, build resilience, and participate fully in their communities.
            </p>
        </div>

        <!-- Values Card -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 shadow-lg rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-600 rounded-full p-3 mr-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Our Values</h3>
            </div>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span><strong>Respect:</strong> We treat everyone with dignity</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span><strong>Empowerment:</strong> We support people to make their own choices</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span><strong>Inclusion:</strong> We welcome and celebrate diversity</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span><strong>Excellence:</strong> We strive for the highest quality</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span><strong>Collaboration:</strong> We work together</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Strategic Plan Sections -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Strategic Plan Sections</h2>

        <!-- Section 1: Context and Background -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="md:flex">
                <div class="md:w-1/3 bg-gray-200 flex items-center justify-center min-h-[250px]">
                    <div class="text-center text-gray-500 p-4">
                        <svg class="h-12 w-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-xs">Context Image</p>
                    </div>
                </div>
                <div class="md:w-2/3 p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Context and Background</h3>
                    <div class="text-gray-700 leading-relaxed space-y-4">
                        <p>Our organisation operates in a rapidly changing environment. Mental health services are facing increasing demand, changing funding models, and evolving expectations from service users and their families.</p>
                        <p>This strategic plan sets out how we will respond to these challenges and opportunities over the next three years.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic Goals -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Strategic Goals</h2>

        <!-- Goal 1 -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
                <h3 class="text-2xl font-bold">
                    Goal 1: To support people to live their best lives
                </h3>
            </div>
            <div class="p-8">
                <p class="text-gray-700 text-lg mb-6">
                    The People We Support must have the opportunity to be adventurous in their lives. We will ensure all services are person-centered and empowering.
                </p>

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Goal Statements:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">The People We Support must have the opportunity to be adventurous in their lives</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Support should be person-centered and empowering</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2"><strong>Responsible Senior manager:</strong></p>
                    <p class="text-gray-900 font-semibold">Senior manager of Care Services</p>
                        <p class="text-sm text-gray-600 mt-4 mb-2"><strong>Projects:</strong></p>
                        <p class="text-gray-900 font-semibold">2 projects</p>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Associated Projects:</h4>
                    <div class="space-y-3">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded font-semibold">1.4</span>
                                    <strong class="text-gray-900">Explore how technology, digital resources and AI can be used to achieve people's goals</strong>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-600">45%</div>
                                    <div class="text-xs text-gray-500">Complete</div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded font-semibold">1.5</span>
                                    <strong class="text-gray-900">Develop peer support networks across all services</strong>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-yellow-600">30%</div>
                                    <div class="text-xs text-gray-500">Complete</div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 30%"></div>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 italic">Progress percentages are automatically calculated from completed milestones</p>
                </div>
            </div>
        </div>

        <!-- Goal 2 -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 p-6 text-white">
                <h3 class="text-2xl font-bold">
                    Goal 2: Increase sustainability
                </h3>
            </div>
            <div class="p-8">
                <p class="text-gray-700 text-lg mb-6">
                    Focus on environmental and operational sustainability across all services. We will reduce our environmental impact while ensuring financial sustainability.
                </p>

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Goal Statements:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Implement sustainable practices across all operations</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Reduce carbon footprint by 30% by 2027</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2"><strong>Responsible Senior manager:</strong></p>
                    <p class="text-gray-900 font-semibold">Senior manager of Operations</p>
                        <p class="text-sm text-gray-600 mt-4 mb-2"><strong>Projects:</strong></p>
                        <p class="text-gray-900 font-semibold">1 project</p>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Associated Projects:</h4>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <span class="bg-green-600 text-white px-3 py-1 rounded font-semibold">2.1</span>
                                <strong class="text-gray-900">Achieve carbon neutrality by 2030</strong>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-yellow-600">20%</div>
                                <div class="text-xs text-gray-500">Complete</div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goal 3 -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-pink-700 p-6 text-white">
                <h3 class="text-2xl font-bold">
                    Goal 3: Strengthen partnerships and collaboration
                </h3>
            </div>
            <div class="p-8">
                <p class="text-gray-700 text-lg mb-6">
                    Build stronger partnerships with health services, local authorities, community groups, and service users to deliver integrated, effective support.
                </p>

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Goal Statements:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <span class="text-purple-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Develop strategic partnerships with key stakeholders</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-purple-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Improve integration with health and social care systems</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2"><strong>Responsible Senior manager:</strong></p>
                    <p class="text-gray-900 font-semibold">Senior manager of Partnerships</p>
                        <p class="text-sm text-gray-600 mt-4 mb-2"><strong>Projects:</strong></p>
                        <p class="text-gray-900 font-semibold">1 project</p>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Associated Projects:</h4>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <span class="bg-purple-600 text-white px-3 py-1 rounded font-semibold">3.1</span>
                                <strong class="text-gray-900">Establish partnership framework with local health services</strong>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">65%</div>
                                <div class="text-xs text-gray-500">Complete</div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Note -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 mb-6 rounded-lg shadow border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">This is an In-Progress Example</h3>
                <div class="text-sm text-blue-800 space-y-2">
                    <p>This page shows what a strategic plan looks like while it's being actively worked on. Projects are at various stages of completion, and milestones are being completed over time.</p>
                    <p><strong>Progress Calculation:</strong> Project progress percentages are automatically calculated based on completed milestones. As you mark milestones as "completed", the progress percentage updates automatically.</p>
                    <p><a href="/example-plan" class="underline font-semibold">View a completed plan example</a> to see what it looks like when everything is finished.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="/how-to-create" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
            Learn How to Create Your Plan
        </a>
        <a href="/example-plan" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors text-center">
            View Completed Plan Example
        </a>
        <?php if (isLoggedIn()): ?>
            <a href="/strategic-plan" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-center">
                View Your Strategic Plan
            </a>
        <?php else: ?>
            <a href="/register" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-center">
                Get Started
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
