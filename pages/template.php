<?php
// Strategic Plan Template/Example Page
// Publicly accessible - no login required

$title = 'Strategic Plan Template - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Strategic Plan Template & Field Guide</h1>
        <p class="mt-2 text-gray-600">
            This page shows an example strategic plan with all fields labelled. Use this as a reference to understand what each field means, then customise the field names to match your organisation's terminology.
        </p>
    </header>

    <!-- Organisation-Level Foundation -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="border-l-4 border-purple-500 pl-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Organisation-Level Foundation</h2>
            <p class="text-sm text-gray-600">These fields appear at the top of your strategic plan and provide context for all your goals and projects.</p>
        </div>

        <div class="space-y-4">
            <!-- About Us -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            About Us
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            An introduction to your organisation. This is where you describe who you are, what you do, your history, your approach, and what makes your organisation unique. This appears first in your strategic plan.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <p class="text-gray-700 whitespace-pre-line">We are [Your Organisation Name]

A pioneering charity providing dedicated services for [your service area].

We support people on their journey to [your mission], by working with each person to find their own way forward. The power of people's lived experience enables us to provide pioneering services which transform lives.

[Your organisation's story, approach, and what makes you unique...]</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Tip:</strong> This is your chance to tell your organisation's story. Include what you do, who you serve, your approach, and what makes you special. This helps readers understand the context for all your strategic goals.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Vision Statement -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Vision Statement
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            A clear, inspiring statement of what your organisation aims to achieve in the long term. This is your "north star" - where you want to be in the future.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <p class="text-gray-700 italic">"To be the leading provider of person-centered mental health support, empowering individuals to live fulfilling and independent lives in their communities."</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Tip:</strong> Your vision should be aspirational and forward-looking. It's what you're working towards, not what you're doing right now.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mission Statement -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Mission Statement
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            A statement of your organisation's fundamental purpose and what it does. This describes why your organisation exists and what it does day-to-day.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <p class="text-gray-700">We provide high-quality, person-centered mental health support services that enable individuals to achieve their goals, build resilience, and participate fully in their communities.</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Tip:</strong> Your mission is about the present - what you do and why you do it. It's more concrete than your vision.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Values -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Values
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            The core principles and beliefs that guide your organisation's actions and decisions. These are the fundamental values that everyone in your organisation should embody.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li><strong>Respect:</strong> We treat everyone with dignity and value their unique perspectives</li>
                                <li><strong>Empowerment:</strong> We support people to make their own choices and take control of their lives</li>
                                <li><strong>Inclusion:</strong> We welcome and celebrate diversity in all its forms</li>
                                <li><strong>Excellence:</strong> We strive for the highest quality in everything we do</li>
                                <li><strong>Collaboration:</strong> We work together with service users, families, and partners</li>
                            </ul>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Tip:</strong> Values should be actionable and meaningful. They guide how you work, not just what you do. You can have as many or as few values as make sense for your organisation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic Plan Sections -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="border-l-4 border-indigo-500 pl-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Strategic Plan Sections</h2>
            <p class="text-sm text-gray-600">Custom sections or chapters that you can add to provide additional context, background information, or relate to specific goals.</p>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Section Title
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            The heading for your custom section. Examples: "Context and Background", "Strategic Priorities", "Implementation Approach", "Governance", "Our Approach to Service Delivery".
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <span class="text-gray-700">Context and Background</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Section Content
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            The main content of the section. You can include multiple paragraphs, lists, or any other information that helps explain your strategic plan. This can be background information, context, or content that relates to specific goals.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <p class="text-gray-700 whitespace-pre-line">This section provides important context for understanding our strategic plan.

It may include:
- Background information about our organisation
- The environment in which we operate
- Key challenges and opportunities
- Our approach to strategic planning

Sections can be linked to specific goals or stand alone as general information.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded mr-2">OPTIONAL</span>
                            Link to Goal
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            Optionally link this section to a specific strategic goal. If linked, the section will be associated with that goal. If not linked, it will appear as a general section in your strategic plan.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <span class="text-gray-700">Goal 1: To support people to live their best lives</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Tip:</strong> Use sections to provide context, background information, or detailed explanations that don't fit into goals or projects. They help readers understand the "why" and "how" behind your strategic plan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Strategic Goal -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="border-l-4 border-blue-500 pl-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Example: Strategic Goal</h2>
            <p class="text-sm text-gray-600">A strategic goal is a high-level objective that your organization wants to achieve.</p>
        </div>

        <div class="space-y-4">
            <!-- Goal Number -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Goal Number
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            A unique identifier for this goal (e.g., "1", "2.1", "A", "Goal-1"). This helps you reference and organise your goals.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <span class="text-gray-700 font-mono">1</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Goal Title -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Goal Title
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            A brief, clear statement of what you want to achieve. This should be specific and measurable.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <span class="text-gray-700">To support people to live their best lives</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Description
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            A more detailed explanation of the goal. This provides context and helps everyone understand what the goal means in practice.
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <span class="text-gray-700">The People We Support must have the opportunity to be adventurous in their lives. We will ensure all services are person-centered and empowering.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsible Senior manager -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Responsible Senior manager
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Note:</strong> Your organisation might not have "senior managers". You can customise this field name to match your structure (e.g., "Responsible Manager", "Lead Person", "Accountable Officer", "Owner", or remove it entirely).
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <span class="text-gray-700">Senior manager of Care Services</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Customisation idea:</strong> If you don't have directors, you might rename this to "Responsible Person", "Lead", "Owner", or make it optional.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Goal Statements -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-900 mb-1">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                            Goal Statements
                        </label>
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Note:</strong> These are specific statements that support the goal. Your organisation might call these something else (e.g., "Objectives", "Outcomes", "Key Points", "Principles", "Values", or you might not use them at all).
                        </p>
                        <div class="bg-white p-3 rounded border border-gray-300">
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li>The People We Support must have the opportunity to be adventurous in their lives</li>
                                <li>Support should be person-centered and empowering</li>
                                <li>All services will promote independence and choice</li>
                            </ul>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">
                            💡 <strong>Customisation idea:</strong> You might rename this to "Objectives", "Key Outcomes", "Supporting Points", "Principles", or remove this field if it doesn't fit your structure.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Project -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="border-l-4 border-green-500 pl-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Example: Project</h2>
            <p class="text-sm text-gray-600">Projects are specific initiatives or workstreams that help achieve your strategic goals.</p>
        </div>

        <div class="space-y-4">
            <!-- Project Title -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Project Title
                </label>
                <p class="text-sm text-gray-600 mb-2">The name of the project or initiative.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700">Explore how technology, digital resources and AI can be used to achieve people's goals</span>
                </div>
            </div>

            <!-- Project Number -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Project Number
                </label>
                <p class="text-sm text-gray-600 mb-2">A reference number for the project (e.g., "1.4", "P-001", "2024-01").</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700 font-mono">1.4</span>
                </div>
            </div>

            <!-- Strategic Goal (Link) -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Strategic Goal
                </label>
                <p class="text-sm text-gray-600 mb-2">Which strategic goal does this project support? Projects are linked to goals.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700">Goal 1: To support people to live their best lives</span>
                </div>
            </div>

            <!-- Project Group -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Project Group
                </label>
                <p class="text-sm text-gray-600 mb-2">Optional: The team, department, or group responsible for this project.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700">Digital Development Group</span>
                </div>
            </div>

            <!-- Start Date / End Date -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Start Date / End Date
                </label>
                <p class="text-sm text-gray-600 mb-2">The planned timeline for the project.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700">1 Apr 2025 - 31 Mar 2027</span>
                </div>
            </div>

            <!-- Project Leads -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Project Leads
                </label>
                <p class="text-sm text-gray-600 mb-2">The person or people leading this project.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700">William Ellis, Sarah Johnson</span>
                </div>
            </div>

            <!-- Working Group Members -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Working Group Members
                </label>
                <p class="text-sm text-gray-600 mb-2">Optional: Other team members involved in the project.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <span class="text-gray-700">John Smith, Mary Brown, David Wilson</span>
                </div>
            </div>

            <!-- Project Purposes -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Project Purposes
                </label>
                <p class="text-sm text-gray-600 mb-2">Why is this project needed? What are its main purposes or objectives?</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li>Improve service delivery through technology</li>
                        <li>Enhance person-centered support</li>
                        <li>Increase efficiency and reduce administrative burden</li>
                    </ul>
                </div>
            </div>

            <!-- Milestones -->
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-semibold text-gray-900 mb-1">
                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">FIELD NAME</span>
                    Milestones
                </label>
                <p class="text-sm text-gray-600 mb-2">Key checkpoints or deliverables in the project. Each milestone has a title, target date, and status.</p>
                <div class="bg-white p-3 rounded border border-gray-300">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">• Technology assessment completed</span>
                            <span class="text-gray-500">Target: 30 Jun 2025</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">• Pilot program launched</span>
                            <span class="text-gray-500">Target: 31 Dec 2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It All Fits Together -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">How It All Fits Together</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p class="mb-2">Your strategic plan follows this structure:</p>
                    <ol class="list-decimal list-inside space-y-1 ml-2">
                        <li><strong>About Us</strong> - Your organisation's introduction and story</li>
                        <li><strong>Vision, Mission, Values</strong> - Your organisation's foundation (set at organisation level)</li>
                        <li><strong>Strategic Goals</strong> - High-level objectives that support your vision</li>
                        <li><strong>Projects</strong> - Specific initiatives that help achieve your goals</li>
                        <li><strong>Milestones</strong> - Checkpoints to track progress on projects</li>
                    </ol>
                    <p class="mt-2">When you view your strategic plan, About Us appears first, followed by Vision, Mission, and Values, then your goals and projects. This gives everyone context for who you are and what you're working towards.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customisation Note -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Customisation Coming Soon</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>We're working on features that will allow you to:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Rename fields to match your organisation's terminology</li>
                        <li>Hide fields you don't need</li>
                        <li>Add custom fields specific to your organisation</li>
                        <li>Set which fields are required or optional</li>
                    </ul>
                    <p class="mt-2">For now, you can use the fields as they are, or contact your administrator if you need help adapting the system to your needs.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3">
        <?= DesignSystem::button('Back to Dashboard', '/', 'secondary') ?>
        <?php if (isOrganizationAdmin()): ?>
            <?= DesignSystem::button('Create Your First Goal', '/goals/new', 'primary') ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
