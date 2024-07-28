<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2024 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Db
{
	const OPTION_VERSION = 'tp_eg_grids_version';
	
	const TABLE_GRID = 'eg_grids';
	const TABLE_ITEM_SKIN = 'eg_item_skins';
	const TABLE_ITEM_ELEMENTS = 'eg_item_elements';
	const TABLE_NAVIGATION_SKINS = 'eg_navigation_skins';

	/**
	 * @var array
	 */
	protected static array $grids_cache = [];

	/**
	 * get table with wp prefix
	 *
	 * @param string $table
	 * @param bool $withBackticks
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function get_table(string $table, bool $withBackticks = false): string {
		global $wpdb;
		
		$t = '';
		$backticks = $withBackticks ? '`': '';
		
		switch ($table) {
			case 'grids':
				$t = self::TABLE_GRID;
				break;
			case 'skins':
				$t = self::TABLE_ITEM_SKIN;
				break;
			case 'elements':
				$t = self::TABLE_ITEM_ELEMENTS;
				break;
			case 'nav_skins':
				$t = self::TABLE_NAVIGATION_SKINS;
				break;
			default:
				Essential_Grid_Base::throw_error(esc_attr__('Unknown table name!', ESG_TEXTDOMAIN));
		}
		
		return $backticks . $wpdb->prefix . $t . $backticks;
	}

	/**
	 * @param string $table
	 *
	 * @return bool
	 */
	public static function is_table_exists( string $table): bool {
		global $wpdb;
		return strtolower($wpdb->get_var("SHOW TABLES LIKE '" . $table . "'")) === strtolower($table);
	}
	
	/**
	 * get db version
	 * @return string
	 */
	public static function get_version(): string {
		return get_option(self::OPTION_VERSION, '0.99');
	}
	
	/**
	 * update db version
	 *
	 * @param string $new_version
	 *
	 * @return void
	 */
	public static function update_version(string $new_version)
	{
		update_option(self::OPTION_VERSION, $new_version);
	}

	/**
	 * Check if the tables could be properly created, by checking if TABLE_GRID exists AND table version is latest!
	 * 
	 * @return bool
	 */
	public static function check_table_exist_and_version(): bool {
		if (version_compare(ESG_REVISION, self::get_version(), '>')) return false;
		return self::is_table_exists(self::get_table('grids'));
	}

	/**
	 * Create/Update Database Tables
	 * @return bool
	 */
	public static function create_tables($networkwide = false): bool {
		global $wpdb;

		$created = false;

		if (function_exists('is_multisite') && is_multisite() && $networkwide) { //do for each existing site
			// Get all blog ids and create tables
			$blogids = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->blogs);

			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				$created = self::_create_tables();
				if ($created === false) {
					return false;
				}
				// 2.2.5
				restore_current_blog();
			}
		} else { //no multisite, do normal installation
			$created = self::_create_tables();
		}

		return $created;
	}

	/**
	 * Create Tables
	 * @return bool
	 */
	public static function _create_tables(): bool {
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$charset_collate = $wpdb->get_charset_collate();
		//Create/Update Grids Database
		$force = isset($_GET['esg_recreate_database']) && wp_verify_nonce($_GET['esg_recreate_database'], 'Essential_Grid_recreate_db');
		$grid_ver = ($force === true) ? '0.99' : self::get_version();
		
		if (version_compare($grid_ver, '1', '<')) {
			$table_name = self::get_table('grids');
			if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					postparams TEXT NOT NULL,
					params TEXT NOT NULL,
					layers TEXT NOT NULL,
					UNIQUE KEY id (id),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			$table_name = self::get_table('skins');
			if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					params TEXT NOT NULL,
					layers TEXT NOT NULL,
					settings TEXT,
					UNIQUE KEY id (id),
					UNIQUE (name),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			$table_name = self::get_table('elements');
			if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					settings TEXT NOT NULL,
					UNIQUE KEY id (id),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			$table_name = self::get_table('nav_skins');
			if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					css TEXT NOT NULL,
					UNIQUE KEY id (id),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			//check if a table was created, if not return false and return an error
			$table_name = self::get_table('grids');
			if (!self::is_table_exists($table_name)) {
				return false;
			}

			if($force === false) self::update_version('1');
			$grid_ver = '1';
		}

		//Change database on certain release? No Problem, use the following:
		//change layers to MEDIUMTEXT from TEXT so that more layers can be added (fix for limited entries on custom grids)
		if (version_compare($grid_ver, '1.02', '<')) {
			$table_name = self::get_table('grids');
			$sql = "CREATE TABLE $table_name (
				layers MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($table_name)) {
				return false;
			}

			if($force === false) self::update_version('1.02');
			$grid_ver = '1.02';
		}

		//change more entries to MEDIUMTEXT so that can be stored to prevent loss of data/errors
		if (version_compare($grid_ver, '1.03', '<')) {
			$table_name = self::get_table('skins');
			$sql = "CREATE TABLE $table_name (
				layers MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			$table_name = self::get_table('nav_skins');
			$sql = "CREATE TABLE $table_name (
				css MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			$table_name = self::get_table('elements');
			$sql = "CREATE TABLE $table_name (
				settings MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			$table_name = self::get_table('skins');
			if (!self::is_table_exists($table_name)) {
				return false;
			}

			if($force === false) self::update_version('1.03');
			$grid_ver = '1.03';
		}

		//Add new column settings, as for 2.0 you can add favorite grids
		if (version_compare($grid_ver, '2.1', '<')) {
			$table_name = self::get_table('grids');
			$sql = "CREATE TABLE $table_name (
				settings TEXT NULL
				last_modified DATETIME
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($table_name)) {
				return false;
			}

			if($force === false) self::update_version('2.1');
			$grid_ver = '2.1';
		}

		if (version_compare($grid_ver, '2.2', '<')) {
			$table_name = self::get_table('nav_skins');
			$sql = "CREATE TABLE $table_name (
				navversion VARCHAR(191)
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($table_name)) {
				return false;
			}

			if($force === false) self::update_version('2.2');
			$grid_ver = '2.2';
		}

		do_action('essgrid__create_tables', $grid_ver);

		return true;
	}

	/**
	 * @param array|object $grid
	 *
	 * @return array|object
	 */
	protected static function _decode_params($grid) {
		$isObj = is_object($grid);
		if ($isObj) {
			$grid = (array)$grid;
		}
		
		$keys = ['postparams', 'params', 'layers', 'settings'];
		foreach ($keys as $k) {
			$v = empty($grid[$k]) ? '' : json_decode($grid[$k], true);
			$grid[$k] = (json_last_error() === JSON_ERROR_NONE && !empty($v)) ? $v : [];
		}
		
		return $isObj ? (object)$grid : $grid;
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 * @param string $output
	 * 
	 * @return null|array
	 */
	protected static function _get_grid(string $field, $value, string $output = ARRAY_A) {
		global $wpdb;
		
		$allowed = [
			'id' => '`id` = %d', 
			'handle' => '`handle` = %s',
		];
		if (!isset($allowed[$field])) return null;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM ".self::get_table('grids', true)." WHERE ".$allowed[$field],
				$value
			),
			$output
		);
	}
	
	/**
	 * Get Essential Grid ID by alias
	 * 
	 * @since: 1.2.0
	 * @param string $alias
	 * @return int
	 */
	public static function get_id_by_alias(string $alias): int {
		$grid = self::_get_grid('handle', $alias);
		return !empty($grid['id']) ? $grid['id'] : 0;
	}

	/**
	 * Get Essential Grid alias by ID
	 * 
	 * @param int $id
	 *
	 * @return string
	 *@since: 2.0
	 */
	public static function get_alias_by_id( int $id): string {
		$grid = self::_get_grid('id', $id);
		return !empty($grid['handle']) ? $grid['handle'] : '';
	}

	/**
	 * Get Grid by ID from Database
	 * 
	 * @param int $id
	 * @param bool $raw
	 * @return bool|array
	 */
	public static function get_essential_grid_by_id(int $id, bool $raw = false) {
		$grid = self::_get_grid('id', $id);
		if (empty($grid)) return false;
		
		if (!$raw) $grid = self::_decode_params($grid);

		return $grid;
	}

	/**
	 * Get Grid by Handle from Database
	 * 
	 * @param string $handle
	 * @param bool $raw
	 *
	 * @return bool|array
	 */
	public static function get_essential_grid_by_handle( string $handle, bool $raw = false) {
		$grid = self::_get_grid('handle', $handle);
		if (empty($grid)) return false;

		if (!$raw) $grid = self::_decode_params($grid);

		return $grid;
	}

	/**
	 * clear cached Grids
	 */
	public static function clear_essential_grids_cache()
	{
		self::$grids_cache = [];
	}

	/**
	 * Get all Grids in Database
	 *
	 * @param bool|array $order
	 * @param bool $raw
	 * 
	 * @return array
	 */
	public static function get_essential_grids($order = false, bool $raw = true): array {
		global $wpdb;

		$ordertype = '';
		$orderby = '';
		$order_fav = false;
		$additional = '';
		if (!empty($order)) {
			$ordertype = key($order);
			$orderby = reset($order);
			if ($ordertype != 'favorite') {
				$additional .= ' ORDER BY ' . $ordertype . ' ' . $orderby;
			} else {
				$order_fav = true;
			}
		}

		$cache_key = md5($additional);
		if (empty(self::$grids_cache[$cache_key])) {
			$grids = $wpdb->get_results("SELECT * FROM " . self::get_table('grids', true) . $additional);
			if (!is_array($grids)) $grids = [];
			self::$grids_cache[$cache_key] = $grids;
		} else {
			$grids = self::$grids_cache[$cache_key];
		}

		//check if we order by favorites here
		if ($order_fav === true) {
			$temp = [];
			$temp_not = [];
			foreach ($grids as $grid) {
				$settings = json_decode($grid->settings, true);
				if (!isset($settings['favorite']) || $settings['favorite'] == 'false') {
					$temp_not[] = $grid;
				} else {
					$temp[] = $grid;
				}
			}

			$g = ($orderby == 'ASC') ? $temp : $temp_not;
			$g2 = ($orderby == 'ASC') ? $temp_not : $temp;
			$grids = $g;
			if (!empty($g2)) {
				foreach ($g2 as $t) {
					$grids[] = $t;
				}
			}
		}

		if ($raw === false && !empty($grids)) {
			foreach ($grids as $k => $grid) {
				if (is_array($grid->params)) continue;
				$grids[$k] = self::_decode_params($grid);
			}
		}

		return apply_filters('essgrid_get_essential_grids', $grids);
	}

	/**
	 * get array of $index -> $value from list of essential grids
	 *
	 * @param string $index
	 * @param string $value
	 * @param int|null $exceptID
	 *
	 * @return array
	 */
	protected static function _get_grids_col( string $index = 'id', string $value = 'name', int $exceptID = null): array {
		$arrShort = [];
		$arrGrids = self::get_essential_grids();
		
		foreach ($arrGrids as $grid) {
			$id = $grid->$index;
			$val = $grid->$value;
			
			//filter by except
			if (!empty($exceptID) && $exceptID == $id) continue;
			
			$arrShort[$id] = $val;
		}

		return $arrShort;
	}

	/**
	 * get array of id -> title
	 * 
	 * @param int|null $exceptID
	 * 
	 * @return array
	 */
	public static function get_grids_short( int $exceptID = null): array {
		return self::_get_grids_col('id', 'name', $exceptID);
	}

	/**
	 * get array of id -> handle
	 *
	 * @param int|null $exceptID
	 *
	 * @return array
	 */
	public static function get_grids_short_widgets( int $exceptID = null): array {
		return self::_get_grids_col('id', 'handle', $exceptID);
	}

	/**
	 * get array of name -> handle
	 *
	 * @param int|null $exceptID
	 *
	 * @return array
	 */
	public static function get_grids_short_vc( int $exceptID = null): array {
		return self::_get_grids_col('name', 'handle', $exceptID);
	}
}
