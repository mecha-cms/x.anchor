<?php namespace x\anchor;

function page__content($content) {
    if (!$content || (false === \strpos($content, '</h') && false === \strpos(\strtr($content, [
        "'" => "",
        '"' => ""
    ]), 'role=heading'))) {
        return $content;
    }
    $apart = \apart($content, ['caption', 'div', 'dt', 'figcaption', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'summary']);
    $content = "";
    $r = [];
    foreach ($apart as $v) {
        $k = 0;
        // Skip token(s) that are not complete
        if (1 !== $v[1]) {
            $content .= $v[0];
            continue;
        }
        // This gets the element name part
        $n = \substr(\strtok($v[0], " \n\r\t>"), 1);
        // This gets the element attribute(s) part
        $test = \substr(\substr($v[0], 0, $v[2]), \strlen($n) + 1, -1);
        if (false !== \strpos(',h1,h2,h3,h4,h5,h6,', ',' . $n . ',')) {
            // Skip header element(s) with a `role` attribute whose value is not `heading`
            if (false !== \strpos($test, 'role=') && 'heading' !== (\pair($test)['role'] ?? 0)) {
                $content .= $v[0];
                continue;
            }
            if (false !== \strpos($test, 'id=') && ($k = \pair($test)['id'] ?? 0)) {} else {
                // Auto-generate an `id` attribute from the current element’s content
                $k = 'to:' . \To::kebab(\w(\substr($v[0], $v[2], $v[3])) ?: \substr(\uniqid(), 6));
            }
        // Include other non-header element(s) with a `role` attribute whose value is `heading`
        } else if (false !== \strpos($test, 'role=') && 'heading' === (($q = \pair($test))['role'] ?? 0) && !empty($q['aria-level'])) {
            if (false !== \strpos($test, 'id=') && ($k = $q['id'] ?? 0)) {} else {
                // Auto-generate an `id` attribute from the current element’s content
                $k = 'to:' . \To::kebab(\w(\substr($v[0], $v[2], $v[3])) ?: \substr(\uniqid(), 6));
            }
        }
        if (0 !== $k) {
            // Count for the same `id` value(s) to avoid duplicate(s) and suffix the other(s)
            $r[$k] = ($r[$k] ?? -1) + 1;
            $e = new \HTML($v[0]);
            $e[1] = '<a aria-hidden="true" href="#' . ($e['id'] = $k . ($r[$k] > 0 ? '.' . $r[$k] : "")) . '"></a>' . $e[1];
            $content .= $e;
            continue;
        }
        $content .= $v[0];
    }
    return $content;
}

function route__page() {
    \extract(\lot());
    \class_exists("\\Asset") && $state->is('page') && \Asset::set(__DIR__ . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'css', 10);
}

\Hook::set('page.content', __NAMESPACE__ . "\\page__content", 2.1); // Run this hook before `x\t_o_c\page__content` hook
\Hook::set('route.page', __NAMESPACE__ . "\\route__page", -1);