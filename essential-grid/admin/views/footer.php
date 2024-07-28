<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2024 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

$tooltips = get_option('tp_eg_tooltips', 'true');
?>
</div>

<script type="text/javascript">
	window.ESG ??={};
	ESG.F ??= {};
	ESG.E ??= {};
	ESG.E.slug = '<?php echo str_replace(["\n", "\r"], '', ESG_PLUGIN_SLUG); ?>';
	ESG.E.slug_path = '<?php echo str_replace(["\n", "\r"], '', ESG_PLUGIN_SLUG_PATH); ?>';
	ESG.E.activated = <?php echo Essential_Grid_Base::getValid(); ?>;
	ESG.E.revision = '<?php echo ESG_REVISION; ?>';
	ESG.E.latest_version = '<?php echo get_option('tp_eg_latest-version', ESG_REVISION); ?>';
	ESG.E.tips_dont_show = <?php $tips = get_option('tp_eg_tips_dont_show', []); echo (!empty($tips) ? json_encode($tips) : '[]'); ?>;
	ESG.E.plugin_url = '<?php echo str_replace(["\n", "\r"], '', ESG_PLUGIN_URL); ?>';
	ESG.E.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	ESG.E.nonce = '<?php echo wp_create_nonce("Essential_Grid_actions"); ?>';

	// Check for Resources ( jQuery / tptools / tpgsap )
	let p = new Promise((resolve) => {
		const t = setInterval(() => { if (ESG.F.waitTpt) { clearInterval(t); resolve();	} }, 19);
	});
	p.then(() => { ESG.F.waitTpt(); });
	
	var token = '<?php echo wp_create_nonce("Essential_Grid_actions"); ?>';
	var es_do_tooltipser = <?php echo esc_js($tooltips); ?>;
	<?php
	if($tooltips == 'true'){
	?>
	var initToolTipser_once = false;
	if (document.readyState === "loading") 
		document.addEventListener('readystatechange',function(){
			if ((document.readyState === "interactive" || document.readyState === "complete") && !initToolTipser_once) {
				initToolTipser_once = true;
				AdminEssentials.initToolTipser();
			}
		});
	else {
		initToolTipser_once = true;
		AdminEssentials.initToolTipser();
	}
	
	<?php
	}
	?>
	
</script>

<div id="waitaminute">
	<div class="waitaminute-message"><i class="eg-icon-coffee"></i><br><?php esc_html_e("Please Wait...", ESG_TEXTDOMAIN); ?></div>
</div>

<div id="eg-error-box"></div>

<?php include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/dialogs.php'); ?>
