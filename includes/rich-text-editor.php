<?php
// Helper functions for rich text editing
// This file provides utilities for initializing Quill.js editors

/**
 * Initialize Quill.js rich text editor on a textarea
 * @param string $textareaId The ID of the textarea to convert
 * @param array $options Optional Quill configuration options
 */
function initRichTextEditor($textareaId, $options = []) {
    static $quillLoaded = false;

    if (!$quillLoaded) {
        echo '<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">';
        echo '<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>';
        $quillLoaded = true;
    }

    $defaultOptions = [
        'theme' => 'snow',
        'modules' => [
            'toolbar' => [
                ['bold', 'italic', 'underline'],
                ['link'],
                [['list' => 'ordered'], ['list' => 'bullet']],
                ['clean']
            ]
        ],
        'placeholder' => 'Start typing...'
    ];

    $finalOptions = array_merge_recursive($defaultOptions, $options);
    $optionsJson = json_encode($finalOptions);

    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('{$textareaId}');
        if (!textarea) return;

        // Create a div for Quill
        var editor = document.createElement('div');
        editor.id = '{$textareaId}_editor';
        editor.innerHTML = textarea.value || '';
        editor.style.minHeight = '150px';
        editor.classList.add('rich-text-editor');

        // Insert editor before textarea and hide textarea completely
        textarea.parentNode.insertBefore(editor, textarea);
        textarea.style.display = 'none';
        textarea.style.position = 'absolute';
        textarea.style.visibility = 'hidden';
        textarea.style.height = '0';
        textarea.style.padding = '0';
        textarea.style.margin = '0';
        textarea.style.border = 'none';

        // Initialize Quill
        var quill = new Quill('#{$textareaId}_editor', {$optionsJson});

        // Sync Quill content to textarea on change
        quill.on('text-change', function() {
            textarea.value = quill.root.innerHTML;
        });

        // Also sync on form submit
        var form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                textarea.value = quill.root.innerHTML;
            });
        }
    });
    </script>";
}

/**
 * Sanitize HTML content before saving
 * @param string $html The HTML content to sanitize
 * @return string Sanitized HTML
 */
function sanitizeRichText($html) {
    // Allow basic formatting tags
    $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li>';
    return strip_tags($html, $allowedTags);
}

/**
 * Display rich text content safely
 * @param string $html The HTML content to display
 * @return string Safe HTML
 */
function displayRichText($html) {
    if (empty($html)) {
        return '';
    }

    // Trim whitespace
    $html = trim($html);

    // Check if content is double-encoded (contains &lt; or &gt; entities)
    // This can happen with old data that was encoded with htmlspecialchars
    if (strpos($html, '&lt;') !== false || strpos($html, '&gt;') !== false) {
        // Decode HTML entities (may need multiple passes if double-encoded)
        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // If still encoded, decode again
        while ($decoded !== $html && (strpos($decoded, '&lt;') !== false || strpos($decoded, '&gt;') !== false)) {
            $html = $decoded;
            $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $html = $decoded;
    }

    // Clean and sanitize the HTML (this removes dangerous tags but keeps safe ones)
    $html = sanitizeRichText($html);

    // Ensure proper paragraph spacing
    $html = str_replace('<p></p>', '<p>&nbsp;</p>', $html);

    // The HTML is now safe to output directly - it will be rendered by the browser
    return $html;
}
?>
