<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2024 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Meta
{

	/**
	 * Add a new Meta
	 */
	public function add_new_meta($new_meta)
	{
		$new_meta = apply_filters('essgrid_add_new_meta', $new_meta);
		if (!isset($new_meta['handle']) || strlen($new_meta['handle']) < 3) return esc_attr__('Wrong Meta Handle received', ESG_TEXTDOMAIN);
		if (!isset($new_meta['name']) || strlen($new_meta['name']) < 3) return esc_attr__('Wrong Meta Name received', ESG_TEXTDOMAIN);
		if (!isset($new_meta['sort-type'])) $new_meta['sort-type'] = 'alphabetic';

		$metas = $this->get_all_meta(false);
		if (!empty($metas)) {
			foreach ($metas as $meta) {
				if ($meta['handle'] == $new_meta['handle']) return esc_attr__('Meta Handle already exist, choose a different handle', ESG_TEXTDOMAIN);
			}
		} else {
			$metas = [];
		}

		$new = ['handle' => $new_meta['handle'], 'name' => $new_meta['name'], 'type' => $new_meta['type'], 'sort-type' => $new_meta['sort-type'], 'default' => @$new_meta['default']];
		if ($new_meta['type'] == 'select' || $new_meta['type'] == 'multi-select') {
			if (!isset($new_meta['sel']) || strlen($new_meta['sel']) < 3) return esc_attr__('Wrong Meta Select received', ESG_TEXTDOMAIN);
			$new['select'] = $new_meta['sel'];
		}
		$metas[] = $new;
		update_option('esg-custom-meta', apply_filters('essgrid_add_new_meta_update', $metas));

		return true;
	}

	/**
	 * change meta by handle
	 */
	public function edit_meta_by_handle($edit_meta)
	{
		$edit_meta = apply_filters('essgrid_edit_meta_by_handle', $edit_meta);
		if (!isset($edit_meta['handle']) || strlen($edit_meta['handle']) < 3) return esc_attr__('Wrong Meta Handle received', ESG_TEXTDOMAIN);
		if (!isset($edit_meta['name']) || strlen($edit_meta['name']) < 3) return esc_attr__('Wrong Meta Name received', ESG_TEXTDOMAIN);

		$metas = $this->get_all_meta(false);
		if (!empty($metas)) {
			foreach ($metas as $key => $meta) {
				if ($meta['handle'] == $edit_meta['handle']) {
					$metas[$key]['select'] = @$edit_meta['sel'];
					$metas[$key]['name'] = $edit_meta['name'];
					$metas[$key]['default'] = @$edit_meta['default'];
					update_option('esg-custom-meta', apply_filters('essgrid_edit_meta_by_handle_update', $metas, $edit_meta));
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Remove Meta
	 */
	public function remove_meta_by_handle($handle)
	{
		$handle = apply_filters('essgrid_remove_meta_by_handle', $handle);

		$metas = $this->get_all_meta(false);
		if (!empty($metas)) {
			foreach ($metas as $key => $meta) {
				if ($meta['handle'] == $handle) {
					unset($metas[$key]);
					update_option('esg-custom-meta', apply_filters('essgrid_remove_meta_by_handle_update', $metas));
					return true;
				}
			}
		}

		return esc_attr__('Meta not found! Wrong handle given.', ESG_TEXTDOMAIN);
	}

	/**
	 * get all custom metas
	 */
	public function get_all_meta($links = true)
	{
		$meta = get_option('esg-custom-meta', []);
		if (!is_array($meta)) $meta = [];

		if ($links === true) { 
			//add the meta links to the array
			if (!empty($meta)) {
				foreach ($meta as $key => $m) {
					$meta[$key]['m_type'] = 'meta';
				}
			}
			$meta_link = new Essential_Grid_Meta_Linking();
			$link_metas = $meta_link->get_all_link_meta();
			if (!empty($link_metas)) {
				foreach ($link_metas as $key => $m) {
					$link_metas[$key]['m_type'] = 'link';
				}
			}
			if (is_array($link_metas) && !empty($link_metas)) {
				$meta = @array_merge($meta, $link_metas);
			}
		}

		return apply_filters('essgrid_get_all_meta', $meta, $links);
	}

	/**
	 * get all handles of custom metas
	 * 
	 * @return array
	 */
	public function get_all_meta_handle()
	{
		$metas = [];
		$meta = get_option('esg-custom-meta', []);
		if (empty($meta)) return $metas;
		
		foreach ($meta as $m) {
			$metas[] = 'eg-' . $m['handle'];
		}

		return apply_filters('essgrid_get_all_meta_handle', $metas);
	}
	
	/**
	 * get handle => name of custom metas
	 * 
	 * @param string $prefix
	 * @return array
	 */
	public function get_all_meta_handle_name($prefix = '')
	{
		$metas = [];
		$meta = get_option('esg-custom-meta', []);
		if (empty($meta)) return $metas;
		
		foreach ($meta as $m) {
			$metas[$prefix . $m['handle']] = $m['name'];
		}

		return apply_filters('get_all_meta_handle_name', $metas);
	}

	/**
	 * get single meta data
	 *
	 * @param string $handle
	 * @return false|array
	 */
	public function get_meta_by_handle($handle)
	{
		$metas = $this->get_all_meta(false);
		if (empty($metas)) return false;

		foreach ($metas as $meta) {
			if ($meta['handle'] != $handle) continue;
			return $meta;
		}

		return false;
	}

	/**
	 * insert comma seperated string, it will return an array of it
	 */
	public function prepare_select_by_string($string)
	{
		return apply_filters('essgrid_prepare_select_by_string', explode(',', $string), $string);
	}

	/**
	 * check if post has meta
	 *
	 * @param int $post_id
	 * @param string $handle
	 * @param bool $json_decode
	 * @return string
	 */
	public function get_meta_value_by_handle($post_id, $handle, $json_decode = true)
	{
		if (trim($handle) === '' || intval($post_id) === 0) return '';

		$metas = get_post_meta($post_id, $handle, true);
		if (is_array($metas))
			$text = @$metas[$handle];
		else
			$text = $metas;

		//check if custom meta from us and if it is an image. If yes, output URL instead of ID
		$cmeta = $this->get_all_meta(false);
		if (!empty($cmeta)) {
			foreach ($cmeta as $me) {
				if ('eg-' . $me['handle'] != $handle) continue;

				$text = $this->_getImgSrc($me['type'], $text);
				
				if ($me['type'] == 'multi-select' && !empty($text) && $json_decode) {
					$text = implode(',', json_decode($text));
				}

				if ($text == '' && isset($me['default'])) {
					$text = $me['default'];
				}
				break;
			}
		}

		//check woocommerce
		if (Essential_Grid_Woocommerce::is_woo_exists()) {
			$wc_text = Essential_Grid_Woocommerce::get_value_by_meta($post_id, $handle);
			if ($wc_text !== '') $text = $wc_text;
		}

		if ($text == '') {
			//check if we have a linking
			$meta_link = new Essential_Grid_Meta_Linking();
			$text = $meta_link->get_link_meta_value_by_handle($post_id, $handle);
		}

		/* 2.1.6 allows for shortcodes inside custom meta */
		$text_sc_check = strip_shortcodes($text);
		if ($text != $text_sc_check) {
			$text = preg_replace('/"/', "'", $text);

			//3.0.8 fixed output multiselect metadata
			$text = str_replace('"rn', '"', str_replace("'rn", "'", $text));
			//3.0.13 comment out as this break meta with shortcodes like
			//[testimonial_count category="vat"] reviews
			//$text = str_replace(array('","',"','"), ', ', str_replace(array('["', '"]',"['","']"), '', $text));
		}

		return apply_filters('essgrid_get_meta_value_by_handle', $text, $post_id, $handle);
	}

	/**
	 * replace all metas with corresponding text
	 * 
	 * @param int $post_id
	 * @param string $text
	 * @param array $layer
	 * @return string
	 */
	public function replace_all_meta_in_text($post_id, $text, $layer)
	{
		if (trim($text) === '' || intval($post_id) === 0) return '';

		$base = new Essential_Grid_Base();
		$meta_link = new Essential_Grid_Meta_Linking();
		$cmeta = $this->get_all_meta();

		//process meta tags:
		$arr_matches = [];
		preg_match_all("/%[^%]*%/", $text, $arr_matches);
		if (!empty($arr_matches)) {
			$my_post = get_post($post_id, ARRAY_A);

			foreach ($arr_matches as $matches) {
				if (is_array($matches)) {
					foreach ($matches as $match) {
						$meta = trim(str_replace('%', '', $match));
						$meta_value = get_post_meta($post_id, $meta, true);
						if (!empty($cmeta)) {
							foreach ($cmeta as $me) {
								if ('eg-' . $me['handle'] == $meta) {
									$meta_value = $this->_getImgSrc($me['type'], $meta_value);
									if ($meta_value == '' && isset($me['default'])) {
										$meta_value = $me['default'];
									}
									break;
								}
							}
						}

						//check woocommerce
						if (Essential_Grid_Woocommerce::is_woo_exists()) {
							$wc_text = Essential_Grid_Woocommerce::get_value_by_meta($post_id, $meta);
							if ($wc_text !== '') $meta_value = $wc_text;
						}

						if (empty($meta_value) && !empty($my_post)) {
							//try to get from post
							switch ($meta) {
								//Post elements
								case 'post_url':
									$post_id = $base->getVar($my_post, 'ID');
									$meta_value = get_permalink($post_id);
									break;
								case 'post_id':
									$meta_value = $base->getVar($my_post, 'ID');
									break;
								case 'title':
									$meta_value = $base->getVar($my_post, 'post_title');
									break;
								case 'caption':
								case 'excerpt':
									$meta_value = trim($base->getVar($my_post, 'post_excerpt'));
									if (empty($meta_value))
										$meta_value = trim($base->getVar($my_post, 'post_content'));

									$meta_value = strip_tags($meta_value);
									break;
								case 'meta':
									$meta_value = $this->get_meta_value_by_handle($my_post['ID'], $meta);
									break;
								case 'alias':
									$meta_value = $base->getVar($my_post, 'post_name');
									break;
								case 'description':
								case 'content':
									$meta_value = $base->getVar($my_post, 'post_content');
									break;
								case 'link':
									$meta_value = get_permalink($my_post['ID']);
									break;
								case 'likespost':
									if (!empty($my_post['ID'])) {
										$count = get_post_meta($my_post['ID'], "eg_votes_count", '');
										$count[0] = isset($count[0]) ? $count[0] : 0;
										$meta_value = '<span class="eg-post-count">' . $count[0] . '</span>';
									} else {
										$meta_value = '';
									}
									break;
								case 'date':
									$postDate = $base->getVar($my_post, "post_date_gmt");
									$meta_value = $base->convert_post_date($postDate);
									break;
								case 'date_day':
									$postDate = $base->getVar($my_post, "post_date_gmt");
									$meta_value = date('d', strtotime($postDate));
									break;
								case 'date_month':
									$postDate = $base->getVar($my_post, "post_date_gmt");
									$meta_value = date('m', strtotime($postDate));
									break;
								case 'date_month_abbr':
									$postDate = $base->getVar($my_post, "post_date_gmt");
									$meta_value = date('M', strtotime($postDate));
									break;
								case 'date_year':
									$postDate = $base->getVar($my_post, "post_date_gmt");
									$meta_value = date('Y', strtotime($postDate));
									break;
								case 'date_year_abbr':
									$postDate = $base->getVar($my_post, "post_date_gmt");
									$meta_value = date('y', strtotime($postDate));
									break;
								case 'date_modified':
									$dateModified = $base->getVar($my_post, "post_modified");
									$meta_value = $base->convert_post_date($dateModified);
									break;
								case 'author_name':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_the_author_meta('display_name', $authorID);
									break;
								case 'author_posts':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_author_posts_url($authorID);
									break;
								case 'author_profile':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_the_author_meta('url', $authorID);
									break;
								case 'author_avatar_32':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_avatar($authorID, 32);
									break;
								case 'author_avatar_64':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_avatar($authorID, 64);
									break;
								case 'author_avatar_96':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_avatar($authorID, 96);
									break;
								case 'author_avatar_512':
									$authorID = $base->getVar($my_post, 'post_author');
									$meta_value = get_avatar($authorID, 512);
									break;
								case 'num_comments':
									$meta_value = $base->getVar($my_post, 'comment_count');
									break;
								case 'cat_list':
									$use_taxonomies = false;
									$postCatsIDs = $base->getVar($my_post, 'post_category');
									if (empty($postCatsIDs) && isset($my_post['post_type'])) {
										$postCatsIDs = [];
										$obj = get_object_taxonomies($my_post['post_type']);
										if (!empty($obj) && is_array($obj)) {
											foreach ($obj as $tax) {
												$use_taxonomies[] = $tax;
												$new_terms = get_the_terms($my_post['ID'], $tax);
												if (is_array($new_terms) && !empty($new_terms)) {
													foreach ($new_terms as $term) {
														$postCatsIDs[$term->term_id] = $term->term_id;
													}
												}
											}
										}
									}
									$meta_value = $base->get_categories_html_list($postCatsIDs, true, ',', $use_taxonomies);
									break;
								case 'tag_list':
									$meta_value = $base->get_tags_html_list($my_post['ID']);
									break;
								case 'taxonomy':
									$text_array = [];
									$separator = $base->getVar($layer, ['settings', 'source-separate'], ',');
									$taxonomy = $base->getVar($layer, ['settings', 'source-taxonomy']);
									$terms = get_the_terms($my_post['ID'], $taxonomy);
									if (is_array($terms)) {
										foreach ($terms as $term) {
											$text_array[] = '<a href="' . get_term_link($term->term_id) . '">' . $term->name . '</a>';
										}
									} else {
										$text_array[] = '';
									}
									$meta_value = implode($separator, $text_array);
									break;
								case 'alternate-image':
									$alt_img = get_post_meta($post_id, 'eg_sources_image', true);
									$alt_img = wp_get_attachment_image_src(esc_attr($alt_img), 'full');
									$meta_value = ($alt_img !== false && isset($alt_img['0'])) ? $alt_img['0'] : '';
									break;
								default:
									$meta_value = apply_filters('essgrid_post_meta_content', $meta_value, $meta, $my_post['ID'], $my_post);
									break;
							}

							if (empty($meta_value)) {
								//check if its linking
								$meta_value = $meta_link->get_link_meta_value_by_handle($my_post['ID'], $meta);
							}
						}

						$text = str_replace($match, $meta_value, $text);
					}
				}
			}
		}

		//remove and readd shortcodes for not killing shortcodes in here that end with "]
		preg_match_all('/' . get_shortcode_regex() . '/', $text, $matches, PREG_SET_ORDER);
		$shortcodes = [];
		$num = 0;
		if (!empty($matches)) {
			foreach ($matches as $shortcode) {
				$text = str_replace($shortcode[0], '#####' . $num . '#####', $text);
				$shortcodes[$num] = $shortcode[0];
				$num++;
			}
		}

		$text = str_replace('","', ',', str_replace(['["', '"]'], '', $text));

		if (!empty($shortcodes)) {
			foreach ($shortcodes as $num => $shortcode) {
				$text = str_replace('#####' . $num . '#####', $shortcode, $text);
			}
		}

		return apply_filters('essgrid_replace_all_meta_in_text', $text, $post_id, $arr_matches);
	}

	/**
	 * replace all metas with corresponding text
	 */
	public function replace_all_custom_element_meta_in_text($values, $text)
	{
		$cmeta = $this->get_all_meta();
		$pmeta = Essential_Grid_Item_Element::getPostElementsArray();

		//process meta tags:
		$arr_matches = [];
		preg_match_all("/%[^%]*%/", $text, $arr_matches);
		if (!empty($arr_matches)) {
			foreach ($arr_matches as $matches) {
				if (is_array($matches)) {
					foreach ($matches as $match) {
						$meta = str_replace('%', '', $match);
						$meta_value = @$values[$meta];
						if (!empty($cmeta)) {
							foreach ($cmeta as $me) {
								if ('eg-' . $me['handle'] == $meta) {
									$meta_value = $this->_getImgSrc($me['type'], $meta_value);
									break;
								}
							}
						}

						if (!empty($pmeta)) {
							if (isset($pmeta[$meta])) {
								$meta_value = Essential_Grid_Base::getVar($values, $meta);
							}
						}
						$text = str_replace($match, $meta_value, $text);
					}
				}
			}
		}

		return apply_filters('essgrid_replace_all_custom_element_meta_in_text', $text, $values, $arr_matches);
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function remove_meta_in_text($text)
	{
		return preg_replace("/%[^%]*%/", '', $text);
	}

	/**
	 * get video ratios from post
	 */
	public function get_post_video_ratios($post_id)
	{
		$ratio['vimeo'] = get_post_meta($post_id, 'eg_vimeo_ratio', true);
		$ratio['youtube'] = get_post_meta($post_id, 'eg_youtube_ratio', true);
		$ratio['wistia'] = get_post_meta($post_id, 'eg_wistia_ratio', true);
		$ratio['html5'] = get_post_meta($post_id, 'eg_html5_ratio', true);
		$ratio['soundcloud'] = get_post_meta($post_id, 'eg_soundcloud_ratio', true);

		return apply_filters('essgrid_get_post_video_ratios', $ratio, $post_id);
	}

	/**
	 * get video ratios from custom element
	 */
	public function get_custom_video_ratios($values)
	{
		if (!isset($values['custom-ratio'])) $values['custom-ratio'] = '0';
		$ratio['vimeo'] = $values['custom-ratio'];
		$ratio['youtube'] = $values['custom-ratio'];
		$ratio['wistia'] = $values['custom-ratio'];
		$ratio['html5'] = $values['custom-ratio'];
		$ratio['soundcloud'] = $values['custom-ratio'];

		return apply_filters('essgrid_get_custom_video_ratios', $ratio, $values);
	}

	/**
	 * save all metas at once
	 * @since: 3.0.0
	 */
	public function save_all_metas($metas)
	{
		if (!empty($metas)) {
			foreach ($metas as $k => $meta) {
				if (!isset($meta['handle']) || strlen($meta['handle']) < 3) return esc_attr__('Wrong Meta Handle received', ESG_TEXTDOMAIN);
				if (preg_replace('/[^a-zA-Z0-9\-_]/', '', $meta['handle']) != $meta['handle']) return esc_attr__('Meta Handle "' . $meta['handle'] . '" contain forbidden characters!', ESG_TEXTDOMAIN);
				if (!isset($meta['name']) || strlen($meta['name']) < 3) return esc_attr__('Wrong Meta Name received', ESG_TEXTDOMAIN);
				if (!isset($meta['sort-type'])) $metas[$k]['sort-type'] = 'alphabetic';

				if ($meta['type'] == 'select' || $meta['type'] == 'multi-select') {
					if (!isset($meta['select']) || strlen($meta['select']) < 3) return esc_attr__('Wrong Meta Select received', ESG_TEXTDOMAIN);
				}
			}
		}
		
		update_option('esg-custom-meta', apply_filters('essgrid_add_new_meta_update', $metas));

		return true;
	}

	/**
	 * @param $type
	 * @param $text
	 * @return mixed|string
	 */
	protected function _getImgSrc($type, $text)
	{
		if ($type == 'image') {
			if (intval($text) > 0) {
				//get URL to Image
				$img = wp_get_attachment_image_src($text, 'full');
				if ($img !== false) {
					$text = $img[0];
				} else {
					$text = '';
				}
			} else {
				$text = '';
			}
		}
		return $text;
	}
}
