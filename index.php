<?php namespace x\anchor;

function page__content($content) {
    if (!$content || (false === \stripos($content, '</h') && false === \stripos(\strtr($content, [
        "'" => "",
        '"' => ""
    ]), 'role=heading'))) {
        return $content;
    }
    $count = [];
    return \preg_replace_callback('/<(caption|div|dt|figcaption|h[1-6]|p|summary)(\s(?:"[^"]*"|\'[^\']*\'|[^>])*)?>([\s\S]*?)<\/\1>/i', static function ($m) use (&$count) {
        if ('h' === \strtolower($m[1][0]) && \is_numeric(\substr($m[1], 1))) {
            if (false !== \stripos($m[2], 'role=') && !\preg_match('/\brole=([\'"]?)heading\1/i', $m[2])) {
                return $m[0]; // Skip!
            }
            if (false !== \stripos($m[2], 'id=') && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2], $mm)) {
                $id = $mm[1];
                if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                    $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                } else {
                    $id = \htmlspecialchars_decode($id);
                }
            } else {
                $id = \To::kebab(\w($m[3] ?: \substr(\uniqid(), 6)));
            }
            $count[$id] = ($count[$id] ?? -1) + 1;
            $out = new \HTML($m[0]);
            $out[1] = '<a aria-hidden="true" href="#' . ($out['id'] = $id . ($count[$id] > 0 ? '.' . $count[$id] : "")) . '"></a>' . $m[3];
            return (string) $out;
        }
        if (false !== \stripos($m[2], 'role=') && \preg_match('/\brole=([\'"]?)heading\1/i', $m[2]) && \preg_match('/\baria-level=("\d+"|\'\d+\'|\d+)/i', $m[2], $mm)) {
            if (false !== \stripos($m[2], 'id=') && \preg_match('/\bid=("[^"]+"|\'[^\']+\'|[^>\s]+)/i', $m[2], $mm)) {
                $id = $mm[1];
                if (('"' === $id[0] && '"' === \substr($id[0], -1)) || ("'" === $id[0] && "'" === \substr($id[0], -1))) {
                    $id = \htmlspecialchars_decode(\substr($id, 1, -1));
                } else {
                    $id = \htmlspecialchars_decode($id);
                }
            } else {
                $id = 'to:' . \To::kebab(\w($m[3] ?: \substr(\uniqid(), 6)));
            }
            $count[$id] = ($count[$id] ?? -1) + 1;
            $out = new \HTML($m[0]);
            $out[1] = '<a aria-hidden="true" href="#' . ($out['id'] = $id . ($count[$id] > 0 ? '.' . $count[$id] : "")) . '"></a>' . $m[3];
            return (string) $out;
        }
        return $m[0];
    }, $content);
}

function get() {
    \extract($GLOBALS, \EXTR_SKIP);
    \class_exists("\\Asset") && $state->is('page') && \Asset::set(__DIR__ . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'css', 10);
}

\Hook::set('get', __NAMESPACE__ . "\\get", -1);
\Hook::set('page.content', __NAMESPACE__ . "\\page__content", 2.1); // Run this hook before `x\t_o_c\page__content` hook

if (\defined("\\TEST") && 'x.anchor' === \TEST && \is_file($test = __DIR__ . \D . 'test.php')) {
    require $test;
}