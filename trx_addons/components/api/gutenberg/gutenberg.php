<?php
/**
 * Plugin support: Gutenberg
 *
 * @package ThemeREX Addons
 */
// Return true if Gutenberg exists and current mode is preview
if ( !function_exists( 'trx_addons_gutenberg_is_preview' ) ) {
    function trx_addons_gutenberg_is_preview() {
        return trx_addons_exists_gutenberg()
                && (
                    trx_addons_gutenberg_is_block_render_action()
                    ||
                    trx_addons_is_post_edit()
                    ||
                    trx_addons_gutenberg_is_widgets_block_editor()
                    ||
                    trx_addons_gutenberg_is_site_editor()
                    );
    }
}

// Return true if current mode is "Block render"
if ( !function_exists( 'trx_addons_gutenberg_is_block_render_action' ) ) {
    function trx_addons_gutenberg_is_block_render_action() {
        return trx_addons_exists_gutenberg()
                && trx_addons_check_url('block-renderer') && !empty($_GET['context']) && $_GET['context']=='edit';
    }
}

// Return true if current mode is "Edit post"
if ( ! function_exists( 'trx_addons_is_post_edit' ) ) {
    function trx_addons_is_post_edit() {
        return ( trx_addons_check_url( 'post.php' ) && ! empty( $_GET['action'] ) && $_GET['action'] == 'edit' )
                ||
                trx_addons_check_url( 'post-new.php' )
                ||
                ( trx_addons_check_url( '/block-renderer/trx-addons/' ) && ! empty( $_GET['context'] ) && $_GET['context'] == 'edit' )
                ||
                ( trx_addons_check_url( 'admin.php' ) && ! empty( $_GET['page'] ) && $_GET['page'] == 'gutenberg-edit-site' )
                ||
                ( trx_addons_check_url( 'site-editor.php' ) && ( empty( $_GET['postType'] ) || $_GET['postType'] == 'wp_template' ) )
                ||
                trx_addons_check_url( 'widgets.php' );
    }
}

// Return true if current mode is "Widgets Block Editor" (a new widgets panel with Gutenberg support)
if ( ! function_exists( 'trx_addons_gutenberg_is_widgets_block_editor' ) ) {
    function trx_addons_gutenberg_is_widgets_block_editor() {
        return is_admin()
                && trx_addons_exists_gutenberg()
                && version_compare( get_bloginfo( 'version' ), '5.8', '>=' )
                && trx_addons_check_url( 'widgets.php' )
                && function_exists( 'wp_use_widgets_block_editor' )
                && wp_use_widgets_block_editor();
    }
}

// Return true if current mode is "Full Site Editor"
if ( ! function_exists( 'trx_addons_gutenberg_is_site_editor' ) ) {
    function trx_addons_gutenberg_is_site_editor() {
        return is_admin()
                && trx_addons_exists_gutenberg()
                && version_compare( get_bloginfo( 'version' ), '5.9', '>=' )
                && trx_addons_check_url( 'site-editor.php' )
                && function_exists( 'wp_is_block_theme' )
                && wp_is_block_theme();
    }
}