<?php
// Example/Completed Strategic Plan page - Enhanced with images
// Publicly accessible - no login required

$title = 'Example Strategic Plan - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <!-- Hero Section with Image -->
    <div class="relative bg-gradient-to-r from-green-600 to-emerald-700 rounded-lg overflow-hidden mb-8 shadow-xl">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative p-12 text-white">
            <div class="flex items-center mb-4">
                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h1 class="text-4xl font-bold">Completed Strategic Plan Example</h1>
            </div>
            <p class="text-xl text-green-100 max-w-3xl">
                This example shows a fully completed strategic plan with all projects at 100% completion. See how a finished plan looks and what you can achieve.
            </p>
            <div class="mt-4">
                <a href="/example-plan-in-progress" class="text-green-100 hover:text-white underline text-sm">
                    View an in-progress plan example →
                </a>
            </div>
        </div>
        <div class="relative h-64">
            <img src="/static/images/examples/completed/hero.jpeg" alt="Team celebrating successful strategic plan" class="w-full h-64 object-cover">
        </div>
    </div>

    <!-- About Us Section with Image -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="md:flex">
            <div class="md:w-1/3">
                <img src="/static/images/examples/completed/About-Us.jpeg" alt="Staff supporting community members" class="w-full h-full object-cover">
            </div>
            <div class="md:w-2/3 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About Us</h2>
                <div class="text-gray-700 leading-relaxed space-y-4">
                    <p class="font-semibold text-lg">We are Example Mental Health Organisation</p>
                    <p>A pioneering charity providing dedicated services for people with mild to serious and enduring mental ill health.</p>
                    <p>We support people on their journey to better mental health, by working with each person to find their own way forward. The power of people's lived experience enables us to provide pioneering services which transform lives.</p>
                    <p>From being there for people in crisis to suicide prevention, supported living to self-harm management and peer support. We are with those we support every step of their journey to a better place. People's experiences are at the centre of everything that we do. We champion peer workers; they know that recovery is possible, because they've been there too.</p>
                    <p>Of course, everyone's journey is different, so we work with people to identify, believe in, and reach their goals, whatever they may be. Often, it's about hope, but we know that's not always easy for people to hold on to. And so, when times are tough, we hold it for them, keeping it safe - just until the time is right.</p>
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

    <!-- Strategic Plan Sections with Images -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Strategic Plan Sections</h2>

        <!-- Section 1: Context and Background -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="md:flex">
                <div class="md:w-1/3">
                    <img src="/static/images/examples/completed/strategic-plan-section.jpeg" alt="Team workshop reviewing strategic context" class="w-full h-full object-cover">
                </div>
                <div class="md:w-2/3 p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Context and Background</h3>
                    <div class="text-gray-700 leading-relaxed space-y-4">
                        <p>Our organisation operates in a rapidly changing environment. Mental health services are facing increasing demand, changing funding models, and evolving expectations from service users and their families.</p>
                        <div>
                            <p class="font-semibold mb-2">Key challenges include:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>Rising demand for mental health support</li>
                                <li>Limited resources and funding constraints</li>
                                <li>Need for more person-centered approaches</li>
                                <li>Integration with health and social care systems</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-semibold mb-2">Despite these challenges, we see significant opportunities:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>Growing recognition of the importance of mental health</li>
                                <li>Advances in peer support and lived experience approaches</li>
                                <li>Potential for digital innovation in service delivery</li>
                                <li>Strong partnerships with local communities</li>
                            </ul>
                        </div>
                        <p>This strategic plan sets out how we will respond to these challenges and opportunities over the next three years.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Strategic Priorities -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="md:flex md:flex-row-reverse">
                <div class="md:w-1/3">
                    <img src="/static/images/examples/completed/strategic-priorities.jpeg" alt="Leadership team defining strategic priorities" class="w-full h-full object-cover">
                </div>
                <div class="md:w-2/3 p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Strategic Priorities</h3>
                    <div class="text-gray-700 leading-relaxed space-y-4">
                        <p>Our strategic priorities for 2025-2027 focus on three key areas:</p>
                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="font-bold text-gray-900 mb-2">1. Person-Centered Care</p>
                                <p>We will ensure that every person we support receives care that is tailored to their individual needs, preferences, and goals.</p>
                            </div>
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <p class="font-bold text-gray-900 mb-2">2. Innovation and Quality</p>
                                <p>We will continuously improve our services through innovation, evidence-based practice, and learning from experience.</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="font-bold text-gray-900 mb-2">3. Sustainability and Growth</p>
                                <p>We will build a sustainable organisation that can grow and adapt to meet future needs while maintaining our core values.</p>
                            </div>
                        </div>
                        <p>These priorities guide all our strategic goals and projects.</p>
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
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">All services will promote independence and choice</span>
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
                                    <div class="text-2xl font-bold text-green-600">100%</div>
                                    <div class="text-xs text-gray-500">Complete</div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded font-semibold">1.5</span>
                                    <strong class="text-gray-900">Develop peer support networks across all services</strong>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600">100%</div>
                                    <div class="text-xs text-gray-500">Complete</div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
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
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Develop sustainable funding models</span>
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
                                <div class="text-2xl font-bold text-green-600">100%</div>
                                <div class="text-xs text-gray-500">Complete</div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
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
                            <li class="flex items-start">
                                <span class="text-purple-600 mr-2 mt-1">✓</span>
                                <span class="text-gray-700">Strengthen service user involvement in planning and delivery</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2"><strong>Responsible Senior manager:</strong></p>
                    <p class="text-gray-900 font-semibold">Senior manager of Partnerships</p>
                        <p class="text-sm text-gray-600 mt-4 mb-2"><strong>Projects:</strong></p>
                        <p class="text-gray-900 font-semibold">0 projects</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-gray-500 italic text-center">No projects associated with this goal yet.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Note -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 mb-6 rounded-lg shadow border border-green-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-green-900 mb-2">This is a Completed Plan Example</h3>
                <div class="text-sm text-green-800 space-y-2">
                    <p>This page shows a fully completed strategic plan with all projects at 100% completion. All milestones have been completed, demonstrating what your plan will look like when finished.</p>
                    <p><strong>Progress Calculation:</strong> Project progress percentages are automatically calculated based on completed milestones. When all milestones are completed, projects show 100% progress.</p>
                    <p><a href="/example-plan-in-progress" class="underline font-semibold">View an in-progress plan example</a> to see what it looks like while you're building it.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="/example-plan-in-progress" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
            View In-Progress Plan Example
        </a>
        <a href="/how-to-create" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors text-center">
            Learn How to Create Your Plan
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
