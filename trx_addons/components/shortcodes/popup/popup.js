/**
 * Shortcode Anchor
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */

// Init handlers
jQuery(document).on('action.init_shortcodes', function(e, container) {
	"use strict";
	jQuery('.sc_popup:not(.inited)').each(function() {
		var id = jQuery(this).attr('id');
		if (!id) return;
		jQuery('a[href="#'+id+'"]').addClass('trx_addons_popup_link');
		jQuery(this).addClass('inited');
	});
});
