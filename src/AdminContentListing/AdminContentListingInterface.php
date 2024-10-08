<?php

/**
 * File that holds class for admin content listing.
 *
 * @package EightshiftUtils\AdminContentListing
 */

declare(strict_types=1);

namespace EightshiftUtils\AdminContentListing;

/**
 * Interface for admin content listing
 */
interface AdminContentListingInterface
{
	/**
	 * Get all post type items and structured content.
	 *
	 * @param array<string, string> $searchQuery Additional search query.
	 * @param string[] $filterPostTypes Filter post types.
	 * @param bool $showDetails Show all content details.
	 *
	 * @return array<string, array<mixed>>
	 */
	public function getItems(array $searchQuery = [], array $filterPostTypes = [], bool $showDetails = false): array;

	/**
	 * List all post types to show on output.
	 *
	 * @return string[]
	 */
	public function getPostTypes(): array;

	/**
	 * List all post statuses to show on output.
	 *
	 * @return string[]
	 */
	public function getPostStatuses(): array;

	/**
	 * List all post authors to show on output.
	 *
	 * @return array<string>
	 */
	public function getPostAuthors(): array;

	/**
	 * List all post types icons to show on output.
	 *
	 * @return array<string, string>
	 */
	public function getPostTypesIcons(): array;

	/**
	 * Get stats
	 *
	 * @return array<string, object>
	 */
	public function getStats(): array;

	/**
	 * Get taxonomies
	 *
	 * @return array<string, string[]|mixed>
	 */
	public function getTaxonomies(): array;
}
