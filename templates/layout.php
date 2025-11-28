<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? APP_NAME) ?></title>

    <?php foreach (DesignSystem::getCSSFiles() as $cssFile): ?>
        <link rel="stylesheet" href="<?= $cssFile ?>">
    <?php endforeach; ?>

    <?php foreach (DesignSystem::getJSFiles() as $jsFile): ?>
        <script src="<?= $jsFile ?>"></script>
    <?php endforeach; ?>

    <link rel="icon" type="image/png" href="/static/favicon.png">
</head>
<body>
    <div class="<?= DesignSystem::getPageClass() ?>">
        <header class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
            <div class="<?= DesignSystem::getContainerClass() ?>">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <a class="text-xl font-bold text-white hover:text-blue-100 transition-colors" href="/">
                            Simple Strategic Plans
                        </a>
                    </div>

                    <nav class="hidden md:flex space-x-6">
                        <ul class="flex space-x-6 items-center">
                            <!-- Examples Dropdown - Visible to Everyone -->
                            <li class="relative group">
                                <button class="text-white hover:text-blue-100 flex items-center <?= (isCurrentPage('/example-plan') || isCurrentPage('/example-plan-in-progress')) ? 'font-semibold text-blue-100' : '' ?>">
                                    Examples
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200">
                                    <a href="/example-plan" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/example-plan') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Completed Plan
                                        </div>
                                    </a>
                                    <a href="/example-plan-in-progress" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/example-plan-in-progress') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            In-Progress Plan
                                        </div>
                                    </a>
                                </div>
                            </li>

                            <!-- How-to Link - Visible to Everyone -->
                            <li>
                                <a href="/how-to-create" class="text-white hover:text-blue-100 <?= isCurrentPage('/how-to-create') ? 'font-semibold text-blue-100' : '' ?>">How-to</a>
                            </li>

                            <!-- About Us Link - Visible to Everyone -->
                            <li>
                                <a href="/about" class="text-white hover:text-blue-100 <?= isCurrentPage('/about') ? 'font-semibold text-blue-100' : '' ?>">About Us</a>
                            </li>

                            <?php if (isLoggedIn()): ?>
                                <!-- Content Dropdown -->
                                <li class="relative group">
                                    <button class="text-white hover:text-blue-100 flex items-center <?= (isCurrentPage('/projects') || isCurrentPage('/goals') || isCurrentPage('/reports')) ? 'font-semibold text-blue-100' : '' ?>">
                                        Content
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200">
                                        <a href="/projects" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/projects') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">Projects</a>
                                        <a href="/goals" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/goals') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">Goals</a>
                                        <a href="/reports" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/reports') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">Reports</a>
                                    </div>
                                </li>

                                <!-- Strategic Plan Dropdown -->
                                <li class="relative group">
                                    <button class="text-white hover:text-blue-100 flex items-center <?= (isCurrentPage('/strategic-plan') || isCurrentPage('/plans') || isCurrentPage('/sections') || isCurrentPage('/example-plan') || isCurrentPage('/template')) ? 'font-semibold text-blue-100' : '' ?>">
                                        Strategic Plan
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200">
                                        <a href="/strategic-plan" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/strategic-plan') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">View Plan</a>
                                        <?php if (isOrganizationAdmin()): ?>
                                            <a href="/plans" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/plans') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">Manage Plans</a>
                                            <a href="/sections" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/sections') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">Sections</a>
                                        <?php endif; ?>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="/template" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 <?= isCurrentPage('/template') ? 'bg-blue-50 font-semibold text-blue-700' : '' ?>">Template Guide</a>
                                    </div>
                                </li>

                                <?php if (isSuperAdmin()): ?>
                                    <li>
                                        <a href="/admin" class="text-white hover:text-blue-100 <?= isCurrentPage('/admin') ? 'font-semibold text-blue-100' : '' ?>">Admin</a>
                                    </li>
                                <?php endif; ?>

                                <!-- User Menu -->
                                <li class="ml-auto relative group">
                                    <button class="flex items-center text-sm text-white hover:text-blue-100">
                                        <?php
                                        $user = getCurrentUser();
                                        echo h($user['first_name'] ?? $user['email']);
                                        ?>
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200">
                                        <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                                            <?= h($user['email']) ?>
                                        </div>
                                        <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Logout</a>
                                    </div>
                                </li>
                            <?php else: ?>
                                <li class="ml-auto">
                                    <a href="/login" class="text-white hover:text-blue-100 font-medium">Login</a>
                                </li>
                                <li>
                                    <a href="/register" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition-colors">Register</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <div class="flex-1">
            <div class="<?= DesignSystem::getContainerClass() ?>">
                <main class="py-8">
                    <?php
                    // Display flash messages
                    $flashMessages = getFlashMessage();
                    if ($flashMessages):
                        foreach ($flashMessages as $type => $message):
                            echo DesignSystem::alert($message, $type, ['class' => 'mb-4']);
                        endforeach;
                    endif;
                    ?>

                    <?= $content ?? '' ?>
                </main>
            </div>
        </div>

        <footer class="mt-auto bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 text-white border-t border-slate-800 shadow-inner">
            <div class="<?= DesignSystem::getContainerClass() ?>">
                <div class="py-10">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                        <div>
                            <h3 class="font-semibold text-white mb-3 text-lg">Simple Strategic Plans</h3>
                            <p class="text-sm text-slate-200">
                                The comprehensive platform for organisations to create, manage, and track their strategic plans.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-3 text-lg">Legal</h3>
                            <ul class="space-y-2 text-sm text-slate-200">
                                <li><a href="/terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                                <li><a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a></li>
                                <li><a href="/cookies" class="hover:text-white transition-colors">Cookie Policy</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-3 text-lg">Support</h3>
                            <ul class="space-y-2 text-sm text-slate-200">
                                <li><a href="/template" class="hover:text-white transition-colors">Template Guide</a></li>
                                <li><a href="/example-plan" class="hover:text-white transition-colors">Example Plan</a></li>
                                <li><a href="/example-plan-in-progress" class="hover:text-white transition-colors">In-Progress Plan</a></li>
                                <li><a href="/how-to-create" class="hover:text-white transition-colors">How to Create a Plan</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-3 text-lg">Account</h3>
                            <ul class="space-y-2 text-sm text-slate-200">
                                <?php if (isLoggedIn()): ?>
                                    <li><a href="/dashboard" class="hover:text-white transition-colors">Dashboard</a></li>
                                    <li><a href="/logout" class="hover:text-white transition-colors">Logout</a></li>
                                <?php else: ?>
                                    <li><a href="/login" class="hover:text-white transition-colors">Login</a></li>
                                    <li><a href="/register" class="hover:text-white transition-colors">Register</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-6">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <p class="text-sm text-slate-200 mb-4 md:mb-0">
                                &copy; <?= date('Y') ?> Simple Strategic Plans. All rights reserved.
                            </p>
                            <div id="cookie-consent" class="hidden">
                                <button onclick="acceptCookies()" class="text-sm text-white hover:text-blue-200 underline">
                                    Accept Cookies
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Cookie Consent Banner -->
        <div id="cookie-banner" class="hidden fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 shadow-lg z-50">
            <div class="<?= DesignSystem::getContainerClass() ?>">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="flex-1">
                        <p class="text-sm">
                            We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.
                            <a href="/cookies" class="underline ml-1">Learn more</a>
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <button onclick="acceptCookies()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm font-semibold">
                            Accept
                        </button>
                        <button onclick="declineCookies()" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-600 text-sm font-semibold">
                            Decline
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Cookie consent management
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }

            function setCookie(name, value, days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
            }

            function acceptCookies() {
                setCookie('cookie_consent', 'accepted', 365);
                const banner = document.getElementById('cookie-banner');
                const footerButton = document.getElementById('cookie-consent');
                if (banner) banner.classList.add('hidden');
                if (footerButton) footerButton.classList.add('hidden');
            }

            function declineCookies() {
                setCookie('cookie_consent', 'declined', 365);
                document.getElementById('cookie-banner').classList.add('hidden');
            }

            // Show cookie banner if consent hasn't been given
            window.addEventListener('DOMContentLoaded', function() {
                const consent = getCookie('cookie_consent');
                const banner = document.getElementById('cookie-banner');
                const footerButton = document.getElementById('cookie-consent');

                if (!consent) {
                    // Show banner if no consent
                    if (banner) banner.classList.remove('hidden');
                    // Footer button stays hidden (only banner shows)
                } else {
                    // Hide both if consent already given
                    if (banner) banner.classList.add('hidden');
                    if (footerButton) footerButton.classList.add('hidden');
                }
            });
        </script>
    </div>
</body>
</html>
