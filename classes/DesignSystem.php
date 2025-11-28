<?php
class DesignSystem {
    private static $currentSystem;

    public static function init() {
        self::$currentSystem = DESIGN_SYSTEM;
    }

    public static function getCurrentSystem() {
        return self::$currentSystem ?? DESIGN_SYSTEM;
    }

    public static function getCSSFiles() {
        $system = self::getCurrentSystem();

        switch ($system) {
            case 'tailwind':
                return [
                    '/static/css/custom.css'
                ];
            case 'custom':
                return ['/static/css/custom.css'];
            default:
                return [
                    '/static/css/custom.css'
                ];
        }
    }

    public static function getJSFiles() {
        $system = self::getCurrentSystem();

        switch ($system) {
            case 'tailwind':
                return [
                    'https://cdn.tailwindcss.com'
                ];
            default:
                return [];
        }
    }

    // Component helper methods
    public static function button($text, $href = null, $type = 'primary', $attributes = []) {
        return self::tailwindButton($text, $href, $type, $attributes);
    }

    public static function card($content, $title = null, $attributes = []) {
        return self::tailwindCard($content, $title, $attributes);
    }

    public static function alert($message, $type = 'info', $attributes = []) {
        return self::tailwindAlert($message, $type, $attributes);
    }

    // Tailwind Components
    private static function tailwindButton($text, $href, $type, $attributes) {
        $baseClass = 'px-4 py-2 rounded font-medium focus:outline-none focus:ring-2 focus:ring-offset-2';

        switch ($type) {
            case 'primary':
                $class = $baseClass . ' bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500';
                break;
            case 'secondary':
                $class = $baseClass . ' bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500';
                break;
            case 'cancel':
                $class = $baseClass . ' bg-red-600 text-white hover:bg-red-700 focus:ring-red-500';
                break;
            default:
                $class = $baseClass . ' bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500';
        }

        $attrs = self::buildAttributes(array_merge(['class' => $class], $attributes));

        if ($href) {
            return "<a href=\"{$href}\" {$attrs}>{$text}</a>";
        } else {
            return "<button {$attrs}>{$text}</button>";
        }
    }

    private static function tailwindCard($content, $title, $attributes) {
        $class = 'bg-white shadow rounded-lg overflow-hidden';
        $attrs = self::buildAttributes(array_merge(['class' => $class], $attributes));

        $html = "<div {$attrs}>";

        if ($title) {
            $html .= "<div class=\"px-6 py-4 border-b border-gray-200\">";
            $html .= "<h2 class=\"text-lg font-medium text-gray-900\">{$title}</h2>";
            $html .= "</div>";
        }

        $html .= "<div class=\"px-6 py-4\">{$content}</div>";
        $html .= "</div>";

        return $html;
    }

    private static function tailwindAlert($message, $type, $attributes) {
        $baseClass = 'p-4 rounded-md';

        switch ($type) {
            case 'error':
                $class = $baseClass . ' bg-red-50 text-red-800 border border-red-200';
                break;
            case 'success':
                $class = $baseClass . ' bg-green-50 text-green-800 border border-green-200';
                break;
            case 'warning':
                $class = $baseClass . ' bg-yellow-50 text-yellow-800 border border-yellow-200';
                break;
            default:
                $class = $baseClass . ' bg-blue-50 text-blue-800 border border-blue-200';
        }

        $attrs = self::buildAttributes(array_merge(['class' => $class], $attributes));

        return "<div {$attrs}>{$message}</div>";
    }

    // Utility methods
    private static function buildAttributes($attributes) {
        $attrs = [];
        foreach ($attributes as $key => $value) {
            if ($value !== null && $value !== false) {
                $attrs[] = $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        return implode(' ', $attrs);
    }

    public static function getContainerClass() {
        return 'container mx-auto px-4';
    }

    public static function getPageClass() {
        return 'min-h-screen bg-gray-50';
    }
}

// Initialize design system
DesignSystem::init();
?>
