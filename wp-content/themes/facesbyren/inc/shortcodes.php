<?php
/**
* Get template url for image links
*/
function template_shortcode() {
    return get_template_directory_uri('template_url').'/img';
}
add_shortcode('template_url','template_shortcode');
/**
* Sources shortcode.
* Display article sources.
* Nest [source]'s inside this shortcode.
*/
function sources($atts, $content = NULL) {
    $output = '<div class="sources">';
    $output .= '<input type="checkbox">';
    $output .= '<i></i>';
    $output .= '<h3>Sources</h3>';
    $output .= '<ul>';
    $content = do_shortcode($content);
    $output .= $content;
    $output .= '</ul>';
    $output .= '</div>';
    return $output;
}

add_shortcode('sources','sources');

/**
* Source shortcode.
* Output a single source. Nest this inside a single [sources] shortcode.
*/
function source($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'url'		=> '#'
    ), $atts) );
    $content = do_shortcode($content);
    $output = '<li class="source">' . $content . '<br/><a href="' . $url . '">' . $url . '</a></li>';
    return $output;
}
add_shortcode('source','source');

/**
* Blockquote shortcode
* Display blockquotes with and without authorship information.
*/
function blockquote($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'type'		  => 'facts',
        'attribution' => ''
    ), $atts) );
    $output = '<blockquote class="' . $type . '">';
    $output .= '<p>' . $content . '</p>';
    if($type === 'quote' && $attribution !== '') {
        $output .= '<p class="quote-name">' . $attribution . '</p>';
    }
    $output .= '</blockquote>';
    return $output;
}
add_shortcode('blockquote','blockquote');

function button($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'color' => 'blue',
        'url' => '#'
    ), $atts) );
    $output = '<a href="' . $url . '" class="button button--' . $color . '">' . $content . '</a>';
    return $output;
}
add_shortcode('button','button');
function column($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'size' => '6of12', /* size should be whole fractions, ex: 1of3 or 6of12 */
        'classes' => '' /* classes should be any additional css classes. ex: tac (text-align: center) or other utility classnames. */
    ), $atts) );
    $output =  '<div class="g-b g-b--m--' . $size . ' ' . $classes . '">';
    $content = do_shortcode($content);
    $output .= $content;
    $output .= '</div>';
    return $output;
}
add_shortcode('column','column');
function row($atts, $content = NULL) {
    $output =  '<div class="row">';
    $content = do_shortcode($content);
    $output .= $content;
    $output .= '</div>';
    return $output;
}
add_shortcode('row','row');
function cta($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'background' => 'blue'
    ), $atts) );
    $output =  '<div class="cta cta--' . $background . '">';
    $output .= '<div class="cta__bg"></div>';
    $content = do_shortcode($content);
    $output .= $content;
    $output .= '<a href="#" class="button mts fadeInDown">Get Help Now</a>';
    $output .= '</div>';
    return $output;
}
add_shortcode('cta','cta');
