<?php
/**
 * Walker Nav Menu.
 */
class rehab_nav_menu extends Walker_Nav_Menu {
    /**
    * Starts the list before the elements are added.
    *
    * @see Walker::start_lvl()
    *
    * @since 3.0.0
    *
    * @param string $output Passed by reference. Used to append additional content.
    * @param int    $depth  Depth of menu item. Used for padding.
    * @param array  $args   An array of arguments. @see wp_nav_menu()
    */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        if($depth == 0){
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        }
    }
    /**
    * Ends the list of after the elements are added.
    *
    * @see Walker::end_lvl()
    *
    * @since 3.0.0
    *
    * @param string $output Passed by reference. Used to append additional content.
    * @param int    $depth  Depth of menu item. Used for padding.
    * @param array  $args   An array of arguments. @see wp_nav_menu()
    */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        if($depth == 0){
            $output .= "$indent</ul>\n";
        }
        if($depth > 0){
            $output .= "\n<div class='clear'></div>\n";
        }
    }
    /**
    * Start the element output.
    *
    * @see Walker::start_el()
    *
    * @since 3.0.0
    *
    * @param string $output Passed by reference. Used to append additional content.
    * @param object $item   Menu item data object.
    * @param int    $depth  Depth of menu item. Used for padding.
    * @param array  $args   An array of arguments. @see wp_nav_menu()
    * @param int    $id     Current item ID.
    */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        /**
        * Filter the CSS class(es) applied to a menu item's list item element.
        *
        * @since 3.0.0
        * @since 4.1.0 The `$depth` parameter was added.
        *
        * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
        * @param object $item    The current menu item.
        * @param array  $args    An array of {@see wp_nav_menu()} arguments.
        * @param int    $depth   Depth of menu item. Used for padding.
        */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        if($depth == 1){
            $class_names .= ' tier2';
        }
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        /**
        * Filter the ID applied to a menu item's list item element.
        *
        * @since 3.0.1
        * @since 4.1.0 The `$depth` parameter was added.
        *
        * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
        * @param object $item    The current menu item.
        * @param array  $args    An array of {@see wp_nav_menu()} arguments.
        * @param int    $depth   Depth of menu item. Used for padding.
        */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
        $output .= $indent . '<li' . $id . $class_names .'>';
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
        /**
        * Filter the HTML attributes applied to a menu item's anchor element.
        *
        * @since 3.6.0
        * @since 4.1.0 The `$depth` parameter was added.
        *
        * @param array $atts {
        *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
        *
        *     @type string $title  Title attribute.
        *     @type string $target Target attribute.
        *     @type string $rel    The rel attribute.
        *     @type string $href   The href attribute.
        * }
        * @param object $item  The current menu item.
        * @param array  $args  An array of {@see wp_nav_menu()} arguments.
        * @param int    $depth Depth of menu item. Used for padding.
        */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        /** This filter is documented in wp-includes/post-template.php */
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        if($depth == 0 && stripos(strtolower($class_names), 'menu-item-has-children') !== false){
            $item_output .= ' <span class="down-arrow"></span>';
        }
        $item_output .= '</a>';
        if($depth == 0 && stripos(strtolower($class_names), 'menu-item-has-children') !== false){
            $item_output .= ' <span class="mobile-down-arrow"></span>';
            $item_output .= ' <span class="mobile-up-arrow"></span>';
        }
        $item_output .= $args->after;
        //ends the tier 2 li before nesting tier 3s
        if($depth == 1){
            $item_output .= '</li>';
        }
        /**
        * Filter a menu item's starting output.
        *
        * The menu item's starting output only includes `$args->before`, the opening `<a>`,
        * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
        * no filter for modifying the opening and closing `<li>` for a menu item.
        *
        * @since 3.0.0
        *
        * @param string $item_output The menu item's starting HTML output.
        * @param object $item        Menu item data object.
        * @param int    $depth       Depth of menu item. Used for padding.
        * @param array  $args        An array of {@see wp_nav_menu()} arguments.
        */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    /**
    * Ends the element output, if needed.
    *
    * @see Walker::end_el()
    *
    * @since 3.0.0
    *
    * @param string $output Passed by reference. Used to append additional content.
    * @param object $item   Page data object. Not used.
    * @param int    $depth  Depth of page. Not Used.
    * @param array  $args   An array of arguments. @see wp_nav_menu()
    */
    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        //if it's a tier 2, don't end the li because we've already done it
        if($depth == 1){
            return;
        }else{
            $output .= "</li>\n";
        }
    }
} // Walker_Nav_Menu
