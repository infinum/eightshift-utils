<?php

/**
 * File that holds class for admin content listing.
 *
 * @package EightshiftUtils\AdminContentListing
 */

declare(strict_types=1);

namespace EightshiftUtils\AdminContentListing;

use EightshiftUtilsVendor\EightshiftLibs\Helpers\ObjectHelperTrait;
use EightshiftUtilsVendor\EightshiftLibs\Services\ServiceInterface;
use WP_Query;
// To make it more precise. EightshiftUtils\AdminContentListing\WP_Post not available.
use WP_Post; // phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse,PSR12.Files.FileHeader.SpacingAfterBlock

/**
 * AdminContentListing class.
 */
class AdminContentListing implements ServiceInterface, AdminContentListingInterface
{
	use ObjectHelperTrait;

	/**
	 * All post types to show const.
	 *
	 * @var array<string>
	 */
	public const POST_TYPES = [
		'page',
		'post',
	];

	/**
	 * Search hook name.
	 *
	 * @var string
	 */
	public const SEARCH_HOOK = 'es_utils_admin_content_listing_search';

	/**
	 * Sanitize array hook name.
	 *
	 * @var string
	 */
	public const SANITIZE_ARRAY_HOOK = 'es_utils_admin_content_listing_sanitize_array';

	/**
	 * Register all the hooks.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_filter(self::SEARCH_HOOK, [$this, 'getItems'], 10, 3);
		\add_filter(self::SANITIZE_ARRAY_HOOK, [$this, 'sanitizeArrayHelper'], 10, 2);
	}

	/**
	 * Get stats.
	 *
	 * @return array<string, object>
	 */
	public function getStats(): array
	{
		$output = [];
		foreach ($this->getPostTypes() as $item) {
			$output[$item] = \wp_count_posts($item);
		}

		return $output;
	}

	/**
	 * Get taxonomies.
	 *
	 * @return array<string, string[]|mixed>
	 */
	public function getTaxonomies(): array
	{
		$output = [];
		foreach ($this->getPostTypes() as $item) {
			$output[$item] = \get_object_taxonomies($item);
		}

		return $output;
	}

	/**
	 * List all post statuses to show on output.
	 *
	 * @return array<string>
	 */
	public function getPostStatuses(): array
	{
		return [
			'publish',
			'pending',
			'draft',
			'auto-draft',
			'future',
			'private',
		];
	}

	/**
	 * List all post authors to show on output.
	 *
	 * @return array<string>
	 */
	public function getPostAuthors(): array
	{
		return [
			'mine',
		];
	}

	/**
	 * List all roles that will see only own items.
	 *
	 * @return array<string, array<int, string>>
	 */
	public function getLimitedViewRoles(): array
	{
		return [
			// Permissions::AUTHOR_ROLE => [
			// 	'post',
			// ],
			// Permissions::HR_ROLE => [
			// 	'post',
			// ],
		];
	}

	/**
	 * List all post types to show on output.
	 *
	 * @return array<string>
	 */
	public function getPostTypes(): array
	{
		$output = [];

		// Loop all post types.
		foreach (self::POST_TYPES as $item) {
			if (\current_user_can("read_{$item}s") && \post_type_exists($item)) {
				$output[] = $item;
			}
		}

		return $output;
	}

	/**
	 * List all real arcives.
	 *
	 * @return array<string>
	 */
	public function getRealArchives(): array
	{
		return [
			// NewsPostType::POST_TYPE_SLUG,
			// EventsPostType::POST_TYPE_SLUG, // Custom Event page remove.
		];
	}

	/**
	 * List all post types icons to show on output.
	 *
	 * @return array<string, string>
	 */
	public function getPostTypesIcons(): array
	{
		$output = [];

		foreach ($this->getPostTypes() as $type) {
			$details = \get_post_type_object($type);

			$output[$type] = isset($details->menu_icon) ? $details->menu_icon : ''; //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		}

		return $output;
	}

	/**
	 * Get all post type items and structured content.
	 *
	 * @param array<string, string> $searchQuery Additional search query.
	 * @param string[] $filterPostTypes Filter post types.
	 * @param bool $showDetails Show all content details.
	 *
	 * @return array<string, array<mixed>>
	 */
	public function getItems(array $searchQuery = [], array $filterPostTypes = [], bool $showDetails = false): array
	{
		$items = [];

		// Get current user ID.
		$currentUser = \wp_get_current_user();
		$userId = $currentUser->ID;
		$userRoles = $currentUser->caps;

		// If post type is provided for search use it.
		$postTypes = !empty($filterPostTypes) ? $filterPostTypes : $this->getPostTypes();

		if (!$postTypes) {
			return [];
		}

		if (isset($searchQuery['post_author']) && isset($searchQuery['post_author'][0]) && $searchQuery['post_author'][0] === 'mine') {
			$searchQuery['author__in'] = [$userId];
			unset($searchQuery['post_author']);
		}

		// Get pages from core options.
		$homePageId = (string)\get_option('page_on_front');
		$blogArchiveId = (string)\get_option('page_for_posts');

		// Skip IDs from loop.
		$skipIds = [
			$homePageId,
			$blogArchiveId
		];

		// Add custom IDs for post type archives.
		$customId = [
			'page' => $homePageId,
			'post' => $blogArchiveId,
		];

		// Loop post types and fill archive IDs.
		// foreach ($postTypes as $item) {
		// 	$typeId = Meta::getThemeOption(ThemeOptions::POST_TYPE_CONTENT . "_{$item}");

		// 	if (!$typeId) {
		// 		continue;
		// 	}

		// 	$customId[$item] = $typeId;
		// 	$skipIds[] = $typeId;
		// }

		// If error page is set in options.
		// $errorPage = Meta::getThemeOption(ThemeOptions::POST_TYPE_CONTENT . "_404");

		$skipIds = \array_flip($skipIds);

		// Find out trash items.
		$isTrash = isset(\array_filter(
			(array) $searchQuery['post_status'],
			static function ($item) {
				return $item === 'trash';
			}
		)[0]);

		// Loop post types.
		foreach ($postTypes as $item) {
			$args = [
				'post_type' => $item,
				'posts_per_page' => 5000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				'post_parent' => 0,
				'post_status' => $this->getPostStatuses(),
			];

			$limitedView = false;

			foreach ($this->getLimitedViewRoles() as $limitRoleKey => $limitRoleValue) {
				if (isset($userRoles[$limitRoleKey])) {
					$limitRoleValue = \array_flip((array)$limitRoleValue);

					if (isset($limitRoleValue[$item])) {
						$limitedView = true;
					}
				}
			}

			// Output archive item.
			if (isset($customId[$item]) && !$isTrash) {
				$items[$item][] = [
					'id' => $customId[$item],
					'title' => \get_the_title((int) $customId[$item]),
					'level' => 0,
					'status' => \get_post_status((int) $customId[$item]),
					'isHome' => $item === 'page',
					'isListing' => $item !== 'page',
					'isMine' => (string) \get_post_field('post_author', (int) $customId[$item]) === (string) $userId,
					'slug' => !isset($this->getRealArchives()[$item]) ? \get_the_permalink((int) $customId[$item]) : \get_post_type_archive_link($item),
					'editLink' => $this->getEditUrl((int) $customId[$item]),
				];
			}

			// Add additional search query to the args.
			if ($searchQuery) {
				$args = \array_merge($args, $searchQuery);
			}

			$theQuery = new WP_Query($args);

			$output = [];

			if ($theQuery->have_posts()) {
				// Prepare posts with children.
				while ($theQuery->have_posts()) {
					$theQuery->the_post();
					$post = $theQuery->post;

					if ($item !== 'post') {
						$output[] = $this->getPostOffspring($post, 0);
					} else {
						$output[] = $post;
					}
				}

				// Loop items and output.
				foreach ($this->flattenArray($output) as $postItem) {
					$id = (string) $postItem->ID;

					$editLink = $this->getEditUrl((int) $id);

					if (!$editLink) {
						continue;
					}

					$isMine = (string) $postItem->post_author === (string) $userId; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

					if (!$isMine && $limitedView) {
						continue;
					}

					if (isset($skipIds[$id])) {
						continue;
					}

					$title = $postItem->post_title; //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					$name = $postItem->post_name; //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					$status = $postItem->post_status; //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

					// If missing name.
					if (!$name) {
						$name = $id;
					}

					// If missing title.
					if (!$title) {
						$title = $name;
					}

					$itemDetailsOutput = [
						'id' => $id,
						'title' => $title,
						'status' => $status,
						'editLink' => $editLink,
						'trashLink' => $this->getTrashActionUrl((string) $id, false),
						'trashPermanentlyLink' => $this->getTrashActionUrl((string) $id, true),
						'trashRestoreLink' => $this->getTrashRestoreActionUrl((string) $id),
						'level' => $postItem->level,
						'isHome' => false,
						'isListing' => false,
						// 'isError' => $errorPage === $id,
						'isMine' => $isMine,
						'slug' => \get_the_permalink((int) $id),
					];

					if ($showDetails && !$isTrash) {
						$itemDetailsOutput['dateCreated'] = $postItem->post_date; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						$itemDetailsOutput['dateUpdated'] = $postItem->post_modified; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						$itemDetailsOutput['excerpt'] = $postItem->post_excerpt; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						$itemDetailsOutput['author'] = \get_the_author_meta('display_name', $postItem->post_author); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						$itemDetailsOutput['image'] = \get_the_post_thumbnail_url((int)$id, 'thumbnail');
					}

					$items[$item][] = $itemDetailsOutput;
				}
			} else {
				if (!isset($items[$item])) {
					$items[$item] = [];
				}
			}

			\wp_reset_postdata();
		}

		// Custom Event page from here.
		// if (isset($items[EventsPostType::POST_TYPE_SLUG]) && !$isTrash) {
		// 	\array_unshift(
		// 		$items[EventsPostType::POST_TYPE_SLUG],
		// 		[
		// 			'title' => \__('Events', 'EightshiftUtils'),
		// 			'isListing' => true,
		// 			'slug' => \site_url(EventsPostType::POST_TYPE_URL_SLUG),
		// 		]
		// 	);
		// }
		// // Custom Event page to here.

		// if (isset($items[NewsPostType::POST_TYPE_SLUG]) && !$isTrash) {
		// 	\array_unshift(
		// 		$items[NewsPostType::POST_TYPE_SLUG],
		// 		[
		// 			'title' => \__('News', 'EightshiftUtils'),
		// 			'isListing' => true,
		// 			'slug' => \site_url(NewsPostType::POST_TYPE_URL_SLUG),
		// 		]
		// 	);
		// }

		return $items;
	}

	/**
	 * Sanitize all values in an array.
	 *
	 * @link https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
	 *
	 * @param array<mixed> $array Provided array.
	 * @param string $sanitizationFunction WordPress function used for sanitization purposes.
	 *
	 * @return array<mixed>
	 */
	public function sanitizeArrayHelper(array $array, string $sanitizationFunction): array
	{
		return $this->sanitizeArray($array, $sanitizationFunction);
	}

	/**
	 * Get post children recursive.
	 *
	 * @param WP_Post|null $post Post object.
	 * @param int $level          Depth level.
	 *
	 * @return array<mixed>
	 */
	private function getPostOffspring($post, int $level): array
	{
		$childrenOutput = [];

		if ($level === 0) {
			$post->level = $level; // @phpstan-ignore-line Setting the non standard property.

			$childrenOutput[] = $post;
		}

		// Get immediate children of current post and added to a new object element called children.
		$childrenItems = \get_children([
			"post_parent" => isset($post->ID) ? $post->ID : '',
			'posts_per_page' => 5000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			"post_type" => $this->getPostTypes(),
			"post_status" => $this->getPostStatuses(),
		]);

		// If post does not have any children just return the post with with children being an empty array.
		if (!\is_iterable($childrenItems)) {
			return $childrenOutput;
		}
		foreach ($childrenItems as $child) {
			$child->level = $level + 1; // @phpstan-ignore-line Setting the non standard property.

			$childrenOutput[] = $child;

			$children = \get_children([
				"post_parent" => isset($child->ID) ? $child->ID : '',
				'posts_per_page' => 5000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				"post_type" => $this->getPostTypes(),
				"post_status" => $this->getPostStatuses(),
			]);

			if (\is_object($child) && !empty($children)) {
				$childrenOutput[] = $this->getPostOffspring($child, $level + 1);
			}
		}

		return $childrenOutput;
	}

	/**
	 * Method that returns trash action url.
	 *
	 * @param string $id ID.
	 * @param bool $permanent Permanently delete.
	 *
	 * @return string
	 */
	private function getTrashActionUrl(string $id, bool $permanent = false): string
	{
		// phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
		return \get_delete_post_link((int) $id, '', $permanent) ?: '';
	}

	/**
	 * Method that returns edit page url.
	 *
	 * @param int $id  ID.
	 *
	 * @return string
	 */
	private static function getEditUrl(int $id): string
	{
		return \get_edit_post_link($id) ?? '';
	}

	/**
	 * Method that returns trash restore action url.
	 *
	 * @param string $id ID.
	 *
	 * @return string
	 */
	private static function getTrashRestoreActionUrl(string $id): string
	{
		return \wp_nonce_url("/wp-admin/post.php?post={$id}&action=untrash", 'untrash-post_' . $id);
	}
}
