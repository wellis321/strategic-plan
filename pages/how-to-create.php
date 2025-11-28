<?php
$title = 'How to Create a Strategic Plan - ' . APP_NAME;
ob_start();
?>

<div class="max-w-6xl mx-auto mb-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">How to Create Your Strategic Plan</h1>
        <p class="text-xl text-blue-100">
            A step-by-step guide to building a comprehensive strategic plan for your organisation
        </p>
    </div>

    <!-- Overview -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Getting Started</h2>
        <p class="text-gray-700 mb-4">
            Creating a strategic plan with Simple Strategic Plans is straightforward. This guide will walk you through the process,
            from setting up your organisation's foundation to tracking progress on your goals and projects.
        </p>
        <p class="text-gray-700">
            <strong>Not sure what fields mean?</strong> Check out our <a href="/template" class="text-blue-600 hover:text-blue-800 underline">Template Guide</a>
            to see detailed explanations of each field, or view a <a href="/example-plan" class="text-blue-600 hover:text-blue-800 underline">completed example plan</a>
            to see how everything fits together.
        </p>
    </div>

    <!-- Step 1 -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">1</div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Set Up Your Organisation's Foundation</h2>
                <p class="text-gray-700 mb-4">
                    Start by defining your organisation's core identity. This foundation appears at the top of your strategic plan and sets the context for everything else.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Go to:</strong> Organisation Settings (available to organisation admins)
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">About Us</h3>
                        <p class="text-gray-600 text-sm mb-3">
                            Write a compelling introduction to your organisation. This is often the first thing people see, so make it engaging and informative.
                        </p>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm text-gray-600 italic">
                                "We are a pioneering charity providing dedicated services for people with mental health challenges.
                                We support people on their journey to better mental health, working with each person to find their own way forward..."
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Vision</h3>
                        <p class="text-gray-600 text-sm mb-3">
                            Your vision statement describes the ideal future you're working towards. It should be inspiring and aspirational.
                        </p>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm text-gray-600 italic">
                                "A Scotland where everyone has the opportunity to live their best life, with access to the support they need when they need it."
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Mission</h3>
                        <p class="text-gray-600 text-sm mb-3">
                            Your mission statement explains what you do and why you exist. It should be clear and actionable.
                        </p>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm text-gray-600 italic">
                                "To provide high-quality, person-centred support services that empower individuals to achieve their goals and live independently."
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Values</h3>
                        <p class="text-gray-600 text-sm mb-3">
                            List the core values that guide your organisation's work. These are the principles that inform all your decisions.
                        </p>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <ul class="text-sm text-gray-600 italic list-disc list-inside space-y-1">
                                <li>Person-centred</li>
                                <li>Empowering</li>
                                <li>Inclusive</li>
                                <li>Innovative</li>
                                <li>Collaborative</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2 -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">2</div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Create Your Strategic Plan</h2>
                <p class="text-gray-700 mb-4">
                    Create a new strategic plan for a specific time period. You can have multiple plans (e.g., one for 2025-2030, another for 2030-2035).
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Go to:</strong> Plans → Create New Plan
                    </p>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Title:</strong> Give your plan a clear name (e.g., "Strategic Plan 2025-2030")
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">URL Slug:</strong> This creates your public URL (e.g., "2025-2030plan" creates /your-org/2025-2030plan)
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Time Period:</strong> Set start and end years (optional but recommended)
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Status:</strong> Start as "Draft" while building, then "Publish" when ready
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3 -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">3</div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Add Custom Sections (Optional)</h2>
                <p class="text-gray-700 mb-4">
                    Add custom chapters or sections to provide context, background, or additional information. These appear between your foundation content and your goals.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Go to:</strong> Sections → New Section
                    </p>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Title:</strong> Name your section (e.g., "Context and Background", "Strategic Priorities")
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Content:</strong> Write rich text content with formatting (bold, italic, lists, links)
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Link to Goal (Optional):</strong> Connect the section to a specific goal if relevant
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-600 font-semibold mr-2">•</span>
                        <div>
                            <strong class="text-gray-900">Sort Order:</strong> Control the order sections appear in your plan
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded border border-gray-200 mt-4">
                    <p class="text-sm text-gray-600">
                        <strong>Example sections:</strong> "Our Journey So Far", "Current Challenges", "Strategic Priorities", "How We Work", "Our Impact"
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4 -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">4</div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Define Your Strategic Goals</h2>
                <p class="text-gray-700 mb-4">
                    Strategic goals are high-level objectives that guide your organisation's work. Each goal should be specific, measurable, and aligned with your mission.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Go to:</strong> Goals → New Goal
                    </p>
                </div>

                <div class="space-y-4 mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Goal Number</h3>
                        <p class="text-gray-600 text-sm">A unique identifier (e.g., "1", "2", "Goal A")</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Title</h3>
                        <p class="text-gray-600 text-sm">A clear, concise goal statement</p>
                        <div class="bg-gray-50 p-3 rounded border border-gray-200 mt-2">
                            <p class="text-sm text-gray-600 italic">"To support people to live their best lives"</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600 text-sm">More detail about what this goal means and why it matters</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Goal Statements (Optional)</h3>
                        <p class="text-gray-600 text-sm">Break down the goal into specific statements or sub-objectives</p>
                        <div class="bg-gray-50 p-3 rounded border border-gray-200 mt-2">
                            <ul class="text-sm text-gray-600 italic list-disc list-inside space-y-1">
                                <li>"The People We Support must have the opportunity to be adventurous in their lives"</li>
                                <li>"Support should be person-centred and empowering"</li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Responsible Senior manager</h3>
                        <p class="text-gray-600 text-sm">The person or role responsible for overseeing this goal</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 5 -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">5</div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Create Projects</h2>
                <p class="text-gray-700 mb-4">
                    Projects are specific initiatives that help achieve your goals. Each project should have clear objectives, timelines, and milestones.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Go to:</strong> Projects → New Project
                    </p>
                </div>

                <div class="space-y-4 mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Link to Goal</h3>
                        <p class="text-gray-600 text-sm">Select which strategic goal this project supports</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Project Details</h3>
                        <p class="text-gray-600 text-sm mb-2">Include:</p>
                        <ul class="text-sm text-gray-600 list-disc list-inside space-y-1 ml-4">
                            <li>Project title and number</li>
                            <li>Start and end dates</li>
                            <li>Project leads and working group members</li>
                            <li>Project purposes (what you're trying to achieve)</li>
                            <li>Meeting frequency</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Milestones</h3>
                        <p class="text-gray-600 text-sm">Add milestones to track progress. As you mark milestones as "completed", the system automatically calculates project progress percentage.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 6 -->
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">6</div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Track Progress & Share</h2>
                <p class="text-gray-700 mb-4">
                    Once your plan is published, you can track progress, generate reports, and share your strategic plan with stakeholders.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded border border-blue-200">
                        <h3 class="font-semibold text-gray-900 mb-2">View Your Plan</h3>
                        <p class="text-sm text-gray-600 mb-2">See your complete strategic plan with all sections, goals, and projects in one place.</p>
                        <p class="text-xs text-gray-500"><strong>Go to:</strong> Strategic Plan</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded border border-blue-200">
                        <h3 class="font-semibold text-gray-900 mb-2">Public URL</h3>
                        <p class="text-sm text-gray-600 mb-2">Share your published plan with a custom URL (e.g., /your-org/2025-2030plan)</p>
                        <p class="text-xs text-gray-500">No login required for published plans</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded border border-blue-200">
                        <h3 class="font-semibold text-gray-900 mb-2">Reports</h3>
                        <p class="text-sm text-gray-600 mb-2">View progress summaries, projects by status, and completion rates.</p>
                        <p class="text-xs text-gray-500"><strong>Go to:</strong> Reports</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded border border-blue-200">
                        <h3 class="font-semibold text-gray-900 mb-2">Update Milestones</h3>
                        <p class="text-sm text-gray-600 mb-2">Mark milestones as completed to automatically update project progress percentages.</p>
                        <p class="text-xs text-gray-500">Progress is calculated automatically</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-8 mb-8 border border-blue-200">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">💡 Tips for Success</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Start Simple</h3>
                <p class="text-sm text-gray-700">Begin with your foundation (About Us, Vision, Mission, Values) and 2-3 key goals. You can always add more later.</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Be Specific</h3>
                <p class="text-sm text-gray-700">Clear, specific goals and projects are easier to track and achieve than vague aspirations.</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Use Milestones</h3>
                <p class="text-sm text-gray-700">Break projects into milestones. This makes progress tracking easier and more meaningful.</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Customise to Your Needs</h3>
                <p class="text-sm text-gray-700">Rename fields, add custom sections, and structure your plan to match how your organisation works.</p>
            </div>
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-white shadow rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
        <div class="flex flex-col sm:flex-row gap-4">
            <?php if (isLoggedIn() && isOrganizationAdmin()): ?>
                <a href="/plans/new" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
                    Create Your First Plan
                </a>
                <a href="/organization/settings" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors text-center">
                    Set Up Foundation
                </a>
            <?php elseif (isLoggedIn()): ?>
                <a href="/strategic-plan" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
                    View Your Strategic Plan
                </a>
            <?php else: ?>
                <a href="/register" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
                    Register Your Organisation
                </a>
                <a href="/example-plan" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors text-center">
                    View Example Plan
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
