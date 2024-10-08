<?php

/**
 * File that holds class for admin menu example.
 *
 * @package EightshiftUtils\AdminMenus
 */

declare(strict_types=1);

namespace EightshiftUtils\AdminMenus;

use EightshiftUtils\AdminContentListing\AdminContentListingInterface;
use EightshiftUtilsVendor\EightshiftLibs\AdminMenus\AbstractAdminMenu;

/**
 * AdminContentListingAdminMenu class.
 */
class AdminContentListingAdminMenu extends AbstractAdminMenu
{
	/**
	 * Instance variable of documents data.
	 *
	 * @var AdminContentListingInterface
	 */
	protected $adminContentListing;

	/**
	 * Create a new instance.
	 *
	 * @param AdminContentListingInterface $adminContentListing Inject documentsData which holds documents' data.
	 */
	public function __construct(AdminContentListingInterface $adminContentListing)
	{
		$this->adminContentListing = $adminContentListing;
	}

	/**
	 * Capability for this admin menu.
	 *
	 * @var string
	 */
	public const ADMIN_MENU_CAPABILITY = 'edit_posts';

	/**
	 * Menu slug for this admin menu.
	 *
	 * @var string
	 */
	public const ADMIN_MENU_SLUG = 'es-content';

	/**
	 * Admin icon.
	 */
	public const ADMIN_ICON = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M5 2C3.89543 2 3 2.89543 3 4V16C3 17.1046 3.89543 18 5 18H13C14.1046 18 15 17.1046 15 16V4C15 2.89543 14.1046 2 13 2H5ZM5 5.5C5 5.22386 5.22386 5 5.5 5H12.5C12.7761 5 13 5.22386 13 5.5V6.5C13 6.77614 12.7761 7 12.5 7H5.5C5.22386 7 5 6.77614 5 6.5V5.5ZM16 6H16.5C16.7761 6 17 6.22386 17 6.5V8C17 8.27614 16.7761 8.5 16.5 8.5H16V6ZM16 9.5H16.5C16.7761 9.5 17 9.72386 17 10V11.5C17 11.7761 16.7761 12 16.5 12H16V9.5ZM16 13H16.5C16.7761 13 17 13.2239 17 13.5V15C17 15.2761 16.7761 15.5 16.5 15.5H16V13Z" fill="black"/></svg>'; // phpcs:ignore

	/**
	 * Menu position for this admin menu.
	 *
	 * @var int
	 */
	public const ADMIN_MENU_POSITION = 4;

	/**
	 * Get the title to use for the admin page.
	 *
	 * @return string The text to be displayed in the title tags of the page when the menu is selected.
	 */
	protected function getTitle(): string
	{
		return \esc_html__('Content', 'eightshift-utils');
	}

	/**
	 * Get the menu title to use for the admin menu.
	 *
	 * @return string The text to be used for the menu.
	 */
	protected function getMenuTitle(): string
	{
		return \esc_html__('Content', 'eightshift-utils');
	}

	/**
	 * Get the capability required for this menu to be displayed.
	 *
	 * @return string The capability required for this menu to be displayed to the user.
	 */
	protected function getCapability(): string
	{
		return self::ADMIN_MENU_CAPABILITY;
	}

	/**
	 * Get the menu slug.
	 *
	 * @return string The slug name to refer to this menu by.
	 *                Should be unique for this menu page and only include lowercase alphanumeric,
	 *                dashes, and underscores characters to be compatible with sanitize_key().
	 */
	protected function getMenuSlug(): string
	{
		return self::ADMIN_MENU_SLUG;
	}

	/**
	 * Get the URL to the icon to be used for this menu
	 *
	 * @return string The URL to the icon to be used for this menu.
	 *                * Pass a base64-encoded SVG using a data URI, which will be colored to match
	 *                  the color scheme. This should begin with 'data:image/svg+xml;base64,'.
	 *                * Pass the name of a Dashicons helper class to use a font icon,
	 *                  e.g. 'dashicons-chart-pie'.
	 *                * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 */
	protected function getIcon(): string
	{
		return 'data:image/svg+xml;base64,' . base64_encode(self::ADMIN_ICON); // phpcs:ignore;
	}

	/**
	 * Get the position of the menu.
	 *
	 * @return int Number that indicates the position of the menu.
	 * 5   - below Posts
	 * 10  - below Media
	 * 15  - below Links
	 * 20  - below Pages
	 * 25  - below comments
	 * 60  - below first separator
	 * 65  - below Plugins
	 * 70  - below Users
	 * 75  - below Tools
	 * 80  - below Settings
	 * 100 - below second separator
	 */
	protected function getPosition(): int
	{
		return self::ADMIN_MENU_POSITION;
	}

	/**
	 * Get the view component that will render correct view.
	 *
	 * @return string View uri.
	 */
	protected function getViewComponent(): string
	{
		return 'admin-layout-content-listing';
	}

	/**
	 * Process the admin menu attributes.
	 *
	 * Here you can get any kind of metadata, query the database, etc..
	 * This data will be passed to the component view to be rendered out in the
	 * processAdminMenu parent method.
	 *
	 * @param array<string, mixed>|string $attr Raw admin menu attributes passed into the
	 *                           admin menu function.
	 *
	 * @return array<string, mixed> Processed admin menu attributes.
	 */
	protected function processAttributes($attr): array
	{
		return [
			'pageTitle' => \esc_html__('Content', 'eightshift-utils'),
			'adminLayoutContentListingPostTypes' => $this->adminContentListing->getPostTypes(),
			'adminLayoutContentListingPostTypesIcons' => $this->adminContentListing->getPostTypesIcons(),
			'adminLayoutContentListingPostStatuses' => $this->adminContentListing->getPostStatuses(),
			'adminLayoutContentListingPostAuthors' => $this->adminContentListing->getPostAuthors(),
			'adminLayoutContentListingPostStats' => $this->adminContentListing->getStats(),
			'adminLayoutContentListingTaxonomies' => $this->adminContentListing->getTaxonomies(),
			'adminLayoutContentListingUrl' => '/wp-admin/admin.php?page=' . self::ADMIN_MENU_SLUG,
		];
	}
}
