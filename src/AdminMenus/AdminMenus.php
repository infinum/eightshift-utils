<?php

/**
 * The file that is an AdminMenus class.
 *
 * @package EightshiftUtils\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftUtils\AdminMenus;

use EightshiftUtilsVendor\EightshiftLibs\Services\ServiceInterface;

/**
 * AdminMenus class.
 */
class AdminMenus implements ServiceInterface
{
	/**
	 * Register all the hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('admin_menu', [$this, 'removeAdminMenus'], 999);
	}

	/**
	 * Remove admin bar nodes.
	 *
	 * @return void
	 */
	public function removeAdminMenus(): void
	{
		global $submenu;

		// Removes comments.
		\remove_menu_page('edit-comments.php');

		// Removes customize.
		\remove_submenu_page('themes.php', 'customize.php');
		unset($submenu['themes.php'][6]);

		// if (!\current_user_can(Permissions::ADMIN_ROLE)) {
		// 	// Remove CPT side menus because of content menu.
		// 	\remove_menu_page('edit.php');
		// 	\remove_menu_page('edit.php?post_type=page');
		// 	\remove_menu_page('edit.php?post_type=' . EventsPostType::POST_TYPE_SLUG);
		// 	\remove_menu_page('edit.php?post_type=' . JobsPostType::POST_TYPE_SLUG);
		// 	\remove_menu_page('edit.php?post_type=' . NewsPostType::POST_TYPE_SLUG);
		// 	\remove_menu_page('edit.php?post_type=' . CasesPostType::POST_TYPE_SLUG);

		// 	// Removes themes from less then admin.
		// 	\remove_submenu_page('themes.php', 'themes.php');

		// 	// Remove ACF menu.
		// 	\remove_menu_page('edit.php?post_type=acf-field-group');
		// }

		// if (!\current_user_can(Permissions::EDITOR_ROLE)) {
		// 	// Remove Yoast if user can't access any of the settings pages and not above editor.
		// 	\remove_menu_page('wpseo_workouts');
		// 	\remove_submenu_page('wpseo_workouts', 'wpseo_workouts');
		// 	\remove_submenu_page('wpseo_workouts', 'wpseo_redirects');
		// }
	}
}
