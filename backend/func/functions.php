<?php

function bbcodeToHtml($text) {
    // Définitions des correspondances BBCode -> HTML
    $bbcode_patterns = [
        '/\[b\](.*?)\[\/b\]/i',            // [b]Bold[/b]
        '/\[i\](.*?)\[\/i\]/i',            // [i]Italic[/i]
        '/\[u\](.*?)\[\/u\]/i',            // [u]Underline[/u]
        '/\[url\=(.*?)\](.*?)\[\/url\]/i', // [url=http://example.com]Link[/url]
        '/\[url\](.*?)\[\/url\]/i',        // [url]http://example.com[/url]
        '/\[img\](.*?)\[\/img\]/i',        // [img]Image URL[/img]
        '/\[quote\](.*?)\[\/quote\]/i',    // [quote]Quote text[/quote]
    ];

    $html_replacements = [
        '<strong>$1</strong>',             // Remplacement pour [b]
        '<em>$1</em>',                     // Remplacement pour [i]
        '<u>$1</u>',                       // Remplacement pour [u]
        '<a href="$1">$2</a>',             // Remplacement pour [url=...]
        '<a href="$1">$1</a>',             // Remplacement pour [url]
        '<img src="$1" alt="Image">',      // Remplacement pour [img]
        '<blockquote>$1</blockquote>',    // Remplacement pour [quote]
    ];

    // Remplacement des BBCode par HTML
    return preg_replace($bbcode_patterns, $html_replacements, $text);
}