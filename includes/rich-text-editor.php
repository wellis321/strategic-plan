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
 * @return string Sanitized HTML, or empty string if content is empty
 */
function sanitizeRichText($html) {
    if (empty($html)) {
        return '';
    }

    // Allow basic formatting tags
    $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li>';
    $sanitized = strip_tags($html, $allowedTags);

    // Check if the sanitized content is empty (only whitespace/breaks)
    // Use the same logic as displayRichText to detect empty content
    $textContent = strip_tags($sanitized);
    $textContent = html_entity_decode($textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $textContent = preg_replace('/[\s\n\r\t]+/', '', $textContent);
    $textContent = str_replace("\xC2\xA0", '', $textContent);

    // If there's no actual text content, return empty string
    if (empty($textContent)) {
        return '';
    }

    return $sanitized;
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

    // Quick check: if the content is exactly or only contains empty paragraph tags, return empty immediately
    // Check exact matches first (most common case)
    if ($html === '<p><br></p>' || $html === '<p><br/></p>' || $html === '<p></p>' || $html === '<p> </p>') {
        return '';
    }

    // Normalize and check patterns
    $normalized = preg_replace('/\s+/', ' ', $html);
    $normalized = trim($normalized);
    if (preg_match('/^<p[^>]*>(<br\s*\/?>|\s|&nbsp;)*<\/p>$/i', $normalized) ||
        preg_match('/^<p[^>]*>\s*<\/p>$/i', $normalized) ||
        $normalized === '<p><br></p>' ||
        $normalized === '<p><br/></p>' ||
        $normalized === '<p></p>') {
        return '';
    }

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

    // First, check if there's actual text content by stripping tags
    // This is the most reliable way to detect empty content
    $textContent = strip_tags($html);
    // Decode HTML entities in text content (like &nbsp;)
    $textContent = html_entity_decode($textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Remove all whitespace characters (including non-breaking spaces)
    $textContent = preg_replace('/[\s\n\r\t]+/', '', $textContent);
    // Also remove non-breaking space character (0xC2 0xA0 in UTF-8)
    $textContent = str_replace("\xC2\xA0", '', $textContent);

    // If there's no actual text content, return empty immediately
    if (empty($textContent)) {
        return '';
    }

    // If we have content, clean up the HTML by removing empty tags
    // Normalize whitespace first
    $html = str_replace(["\n", "\r", "\t"], ' ', $html);
    $html = preg_replace('/\s+/', ' ', $html); // Replace multiple spaces with single space

    // Remove empty paragraph tags with breaks: <p><br></p>, <p><br/></p>, <p> <br> </p>, etc.
    $html = preg_replace('/<p[^>]*>\s*(<br\s*\/?>\s*)*<\/p>/i', '', $html);
    // Remove empty paragraph tags: <p></p>, <p> </p>
    $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html);
    // Remove paragraph tags with only non-breaking spaces or other whitespace entities
    $html = preg_replace('/<p[^>]*>\s*(&nbsp;|\s)*\s*<\/p>/i', '', $html);

    // Also remove other empty tags that might contain only breaks
    $html = preg_replace('/<div[^>]*>\s*(<br\s*\/?>\s*)*<\/div>/i', '', $html);
    $html = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $html);

    // Trim again after removing empty tags
    $html = trim($html);

    // Final check: if after cleaning, the content is empty, return empty string
    $finalTextContent = strip_tags($html);
    $finalTextContent = html_entity_decode($finalTextContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $finalTextContent = preg_replace('/[\s\n\r\t]+/', '', $finalTextContent);
    $finalTextContent = str_replace("\xC2\xA0", '', $finalTextContent);
    if (empty($finalTextContent)) {
        return '';
    }

    // The HTML is now safe to output directly - it will be rendered by the browser
    return $html;
}
?>
