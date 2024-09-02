<?php
/**
 * Initial functions.
 *
 * @package Mayo Clinic Press
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class to manipulate menus.
 *
 * @since 1.0
 */
class Mayo_Nav_Walker {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		// Add custom fields to menu
		add_filter('wp_setup_nav_menu_item', array($this, 'add_custom_fields_meta'));
		add_action('wp_nav_menu_item_custom_fields', array($this, 'add_custom_fields'), 10, 4);

		// Save menu custom fields
		add_action('wp_update_nav_menu_item', array($this, 'update_custom_nav_fields'), 10, 3);
	}

	/**
	 * Add custom menu style fields data to the menu.
	 *
	 * @access public
	 * @param object $menu_item A single menu item.
	 * @return object The menu item.
	 */
	public function add_custom_fields_meta($menu_item) {
		$menu_item->nolink 					= get_post_meta($menu_item->ID, '_menu_item_nolink', true);
		$menu_item->megamenu_widgetarea 	= get_post_meta($menu_item->ID, '_menu_item_megamenu_widgetarea', true);

		return $menu_item;
	}

	/**
	 * Add custom megamenu fields data to the menu.
	 *
	 * @access public
	 * @param object $menu_item A single menu item.
	 * @return object The menu item.
	 */
	public function add_custom_fields($id, $item, $depth, $args) { ?>
	    <p class="field-nolink description description-wide">
	    	<label for="edit-menu-item-nolink-<?php echo esc_attr($item->ID); ?>">
	    	<input type="checkbox" id="edit-menu-item-nolink-<?php echo esc_attr($item->ID); ?>" class="code edit-menu-item-nolink" value="nolink" name="menu-item-nolink[<?php echo esc_attr($item->ID); ?>]"<?php checked($item->nolink, 'nolink'); ?> />
	    		<?php esc_html_e('Disable link', 'mayo'); ?>
	    	</label>
		</p>
	    <p class="field-megamenu-widgetarea description description-wide">
			<label for="edit-menu-item-megamenu_widgetarea-<?php echo esc_attr($item->ID); ?>">
				<?php esc_html_e('Mega Menu Widget Area', 'mayo'); ?>
				<select id="edit-menu-item-megamenu_widgetarea-<?php echo esc_attr($item->ID); ?>" class="widefat code edit-menu-item-custom" name="menu-item-megamenu_widgetarea[<?php echo esc_attr($item->ID); ?>]">
					<option value="0"><?php esc_html_e('Select Widget Area', 'mayo'); ?></option>
					<?php global $wp_registered_sidebars;
					if (!empty($wp_registered_sidebars) && is_array($wp_registered_sidebars)) :
						foreach ($wp_registered_sidebars as $sidebar) : ?>
							<option value="<?php echo esc_attr($sidebar['id']); ?>" <?php selected($item->megamenu_widgetarea, $sidebar['id']); ?>><?php echo esc_html($sidebar['name']); ?>
							</option>
					<?php endforeach; endif; ?>
				</select>
			</label>
		</p>
	<?php
	}

	/**
	 * Add the custom menu style fields menu item data to fields in database.
	 *
	 * @access public
	 * @param string|int $menu_id         The menu ID.
	 * @param string|int $menu_item_db_id The menu ID from the db.
	 * @param array      $args            The arguments array.
	 * @return void
	 */
	public function update_custom_nav_fields($menu_id, $menu_item_db_id, $args) {

		$check = array('nolink', 'megamenu_widgetarea');

		foreach ($check as $key) {
			if(!isset($_POST['menu-item-'.$key][$menu_item_db_id])) {
				$_POST['menu-item-'.$key][$menu_item_db_id] = '';
			}

			$value = sanitize_text_field(wp_unslash($_POST['menu-item-'.$key][$menu_item_db_id]));
			update_post_meta($menu_item_db_id, '_menu_item_'.$key, $value);
		}

	}

	/**
	 * Function to replace normal edit nav walker.
	 *
	 * @return string Class name of new navwalker
	 */
	public function edit_walker() {
		require_once 'class-walker-edit-custom.php';
		return 'Walker_Nav_Menu_Edit_Custom';
	}

}

new Mayo_Nav_Walker();
