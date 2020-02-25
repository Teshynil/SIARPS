<?php

namespace App\Helpers;

class WordToHtmlHelper {

    public static function replaceImages(array $images, ?string $str) {
        if ($str !== null) {
            if (count($images) > 0) {
                preg_match_all('~(<\s{0,1}img.*?src=(?:"|\'))([\w,\s-]+\.[A-Za-z]{1,4})((?:"|\')/>)~is', $str, $matches, PREG_SET_ORDER, 0);
                foreach ($matches as $match) {
                    if (isset($images[$match[2]])) {
                        $fi = $match[0];
                        $re = str_replace($match[2], $images[$match[2]], $fi);
                        $str = str_replace($fi, $re, $str);
                    }
                }
            }
        }
        return $str;
    }

    public static function convertExternalFromWord(?string $string) {
        $str = $string;
        if ($str !== null) {
            $str = preg_replace('~.*?(<body.*?>.*?</body>).*~si', '$1', $str);
            $str = preg_replace('~</{0,1}body.*?>~si', '', $str);
            preg_match_all('~(?<={{)([^<>]*?)(<span[^<]*?>)([^<>]*?.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~(?<={{)([^<>]*?)(<span[^<]*?>)([^<>]*?.*?)(</span>)~si', '$1$3', $str);
                preg_match_all('~(?<={{)([^<>]*?)(<span[^<]*?>)([^<>]*?.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            preg_match_all('~(<span[^<]*?>)([^<>]*?{{.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~(<span[^<]*?>)([^<>]*?{{.*?)(</span>)~si', '$2', $str);
                preg_match_all('~(<span[^<]*?>)([^<>]*?{{.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            preg_match_all('~(<span[^<]*?>)([^<>]*?}}.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~(<span[^<]*?>)([^<>]*?}}.*?)(</span>)~si', '$2', $str);
                preg_match_all('~(<span[^<]*?>)([^<>]*?}}.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            preg_match_all('~({{[^}]*?)(<span[^<>]*?>)([^<>].*?)(</span>)(.*?}})~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~({{[^}]*?)(<span[^<>]*?>)([^<>].*?)(</span>)(.*?}})~si', '$1$3$5', $str);
                preg_match_all('~({{[^}]*?)(<span[^<>]*?>)([^<>].*?)(</span>)(.*?}})~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            $str = preg_replace('~.*?(<div style=\'mso-element:header\'.*?>.*?</div>.*?<div style=\'mso-element:footer\'.*?>.*?</div>).*?~si', '$1', $str);
            $str = preg_replace('~<div style=\'mso-element:header\'.*?>~si', '<div class="header">', $str);
            $str = preg_replace('~<div style=\'mso-element:footer\'.*?>~si', '<div class="footer">', $str);
            $str = preg_replace('~</{0,1}o:p>~si', '', $str);
            $str = preg_replace('~<!--.*?<v:shape .*?(style=\'.*?\').*?(src="(.*?)").*?-->~si', '<img $1 $2/>', $str);
            $str = preg_replace('~<\!\[.*?]>.*?<\!\[.*?]>~si', '', $str);
        }
        return $str;
    }

    public static function convertBodyFromWord(?string $string) {
        $str = $string;
        if ($str !== null) {
            $str = preg_replace('~.*?(<body.*?>.*?</body>).*~si', '$1', $str);
            $str = preg_replace('~</{0,1}body.*?>~si', '', $str);
            preg_match_all('~(?<={{)([^<>]*?)(<span[^<]*?>)([^<>]*?.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~(?<={{)([^<>]*?)(<span[^<]*?>)([^<>]*?.*?)(</span>)~si', '$1$3', $str);
                preg_match_all('~(?<={{)([^<>]*?)(<span[^<]*?>)([^<>]*?.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            preg_match_all('~(<span[^<]*?>)([^<>]*?{{.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~(<span[^<]*?>)([^<>]*?{{.*?)(</span>)~si', '$2', $str);
                preg_match_all('~(<span[^<]*?>)([^<>]*?{{.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            preg_match_all('~(<span[^<]*?>)([^<>]*?}}.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~(<span[^<]*?>)([^<>]*?}}.*?)(</span>)~si', '$2', $str);
                preg_match_all('~(<span[^<]*?>)([^<>]*?}}.*?)(</span>)~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            preg_match_all('~({{[^}]*?)(<span[^<>]*?>)([^<>].*?)(</span>)(.*?}})~si', $str, $matches, PREG_SET_ORDER, 0);
            while (count($matches) > 0) {
                $str = preg_replace('~({{[^}]*?)(<span[^<>]*?>)([^<>].*?)(</span>)(.*?}})~si', '$1$3$5', $str);
                preg_match_all('~({{[^}]*?)(<span[^<>]*?>)([^<>].*?)(</span>)(.*?}})~si', $str, $matches, PREG_SET_ORDER, 0);
            }
            $str = preg_replace('~<div class=WordSection1>~si', '<div class="page">', $str);
            $str = preg_replace('~(<span [^<]*?|)<br[^>]*?style=[^>]*?page-break-before:always[^>]*?>([^<]*?</span>|)~si', '</div><div class="page">', $str);
            $str = preg_replace('~(?(?=class="page")|(class=((\'|"|)[\w ]*?(\'|")|[\w]+?))(?= |>))~si', '', $str);
            $str = preg_replace('~</{0,1}o:p>~si', '', $str);
            $str = preg_replace('~<p>&nbsp;</p>~si', '', $str);
            $str = preg_replace('~<\!\[.*?]>.*?<\!\[.*?]>~si', '', $str);
        }
        return $str;
    }

}
