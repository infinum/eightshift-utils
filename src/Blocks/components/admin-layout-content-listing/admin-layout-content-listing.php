<?php

/**
 * Layout component - Admin layout content listing.
 *
 * @package EightshiftUtils
 */

use EightshiftUtils\AdminContentListing\AdminContentListing;
use EightshiftUtilsVendor\EightshiftLibs\Helpers\Helpers;

$manifest = Helpers::getManifestByDir(__DIR__);


// echo Helpers::outputCssVariablesGlobal(); // phpcs:ignore Eightshift.Security.HelpersEscape.OutputNotEscaped

$componentClass = $manifest['componentClass'] ?? '';
$additionalClass = $attributes['additionalClass'] ?? '';
$blockClass = $attributes['blockClass'] ?? '';
$selectorClass = $attributes['selectorClass'] ?? $componentClass;

$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$postType = isset($_GET['post_type']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['post_type']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postStatus = isset($_GET['post_status']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['post_status']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postAuthor = isset($_GET['post_author']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['post_author']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$showDetails = isset($_GET['show_details']) ? sanitize_text_field(wp_unslash($_GET['show_details'])) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$request = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postJobLocation = isset($_GET['job-location']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['job-location']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postJobCompany = isset($_GET['job-company']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['job-company']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postJobDepartment = isset($_GET['job-department']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['job-department']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postIndustries = isset($_GET['industries']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['industries']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postServices = isset($_GET['services']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['services']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postCategory = isset($_GET['category']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['category']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$postEventType = isset($_GET['event-type']) ? apply_filters(AdminContentListing::SANITIZE_ARRAY_HOOK, wp_unslash($_GET['event-type']), 'sanitize_text_field') : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

$adminLayoutContentListingItems = apply_filters(
	AdminContentListing::SEARCH_HOOK,
	[
		's' => $search,
		'post_status' => $postStatus,
		'post_author' => $postAuthor,
		'job-location' => $postJobLocation,
		'job-company' => $postJobCompany,
		'job-department' => $postJobDepartment,
		'industries' => $postIndustries,
		'services' => implode(',', $postServices),
		'category_name' => implode(',', $postCategory),
		// Custom Event page add from here.
		// 'event-type' => $postEventType,
		// Custom Event page add to here.
	],
	$postType,
	(bool) $showDetails
);

$adminLayoutContentListingPostTypes = Helpers::checkAttr('adminLayoutContentListingPostTypes', $attributes, $manifest);
$adminLayoutContentListingPostTypesIcons = Helpers::checkAttr('adminLayoutContentListingPostTypesIcons', $attributes, $manifest);
$adminLayoutContentListingPostStatuses = Helpers::checkAttr('adminLayoutContentListingPostStatuses', $attributes, $manifest);
$adminLayoutContentListingPostAuthors = Helpers::checkAttr('adminLayoutContentListingPostAuthors', $attributes, $manifest);
$adminLayoutContentListingPostStats = Helpers::checkAttr('adminLayoutContentListingPostStats', $attributes, $manifest);
$adminLayoutContentListingPost = Helpers::checkAttr('adminLayoutContentListingPostStatuses', $attributes, $manifest);
$adminLayoutContentListingUrl = Helpers::checkAttr('adminLayoutContentListingUrl', $attributes, $manifest);
$adminLayoutContentListingTaxonomies = Helpers::checkAttr('adminLayoutContentListingTaxonomies', $attributes, $manifest);
$adminLayoutContentListingPostTaxonomies = Helpers::checkAttr('adminLayoutContentListingPostTaxonomies', $attributes, $manifest);

$filtredTaxonomies = [];
foreach ($adminLayoutContentListingTaxonomies as $taxonomies) {
	if (empty($taxonomies)) {
		continue;
	}
	$filtredTaxonomies = array_merge($filtredTaxonomies, $taxonomies);
}

// Remove duplicated taxonomies.
$filtredTaxonomies = array_unique($filtredTaxonomies);

foreach ($filtredTaxonomies as $tax) {
	$tempTerms = get_terms(
		[
			'taxonomy' => $tax,
			'hide_empty' => true,
		]
	);
	$terms = [];
	foreach ((array)$tempTerms as $tempTerm) {
			$term = (array) $tempTerm;
			$terms[$term['slug']] = $term['name'];
	}
	if (empty($terms)) {
		continue;
	}

	$adminLayoutContentListingPostTaxonomies[$tax] = $terms;
}

$layoutClass = Helpers::classnames([
	Helpers::selector($componentClass, $componentClass),
	Helpers::selector($blockClass, $blockClass, $selectorClass),
	Helpers::selector($additionalClass, $additionalClass),
]);

$isSearched = false;
if ($search || $postType || $postStatus || $postAuthor) {
	$isSearched = true;
}

$checkboxFilters = [
	[
		'items' => $adminLayoutContentListingPostTypes,
		'item' => $postType,
		'id' => 'post_type',
		'name' => esc_html__('Post Type', 'eightshift-utils'),
	],
	[
		'items' => $adminLayoutContentListingPostStatuses,
		'item' => $postStatus,
		'id' => 'post_status',
		'name' => esc_html__('Post Status', 'eightshift-utils'),
	],
	[
		'items' => $adminLayoutContentListingPostAuthors,
		'item' => $postAuthor,
		'id' => 'post_author',
		'name' => esc_html__('Post Author', 'eightshift-utils'),
	],
];

if (!$showDetails) {
	$detailsUrl = add_query_arg(['show_details' => 'true'], $request);
} else {
	$detailsUrl = remove_query_arg('show_details', $request);
}

$isTrash = $postStatus && isset(array_flip($postStatus)['trash']);
$trashUrl = add_query_arg(['post_status' => ['trash']], $adminLayoutContentListingUrl);

?>

<div class="<?php echo esc_attr($layoutClass); ?>">

	<div class="<?php echo esc_attr("{$componentClass}__main-content"); ?>">
		<?php
		foreach ($adminLayoutContentListingItems as $type => $items) {
			$postTypeObject = get_post_type_object($type);
			$postTypeLabel = isset($postTypeObject->label) ? $postTypeObject->label : '';
			?>
			<div class="<?php echo esc_attr("{$componentClass}__section"); ?>">
				<div class="<?php echo esc_attr("{$componentClass}__section-heading"); ?>">
					<div class="<?php echo esc_attr("{$componentClass}__section-heading-intro-wrap"); ?>">
						<div class="<?php echo esc_attr("{$componentClass}__section-heading-label"); ?>">
							<?php if ($isTrash) { ?>
								<?php echo esc_html__('Trash', 'eightshift-utils'); ?>
							<?php } ?>

							<?php echo esc_html($postTypeLabel); ?>
						</div>
						<?php if (!$isTrash) { ?>
							<div class="<?php echo esc_attr("{$componentClass}__section-heading-actions {$componentClass}__actions"); ?>">
								<a href="<?php echo esc_url($trashUrl); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
									<span class="dashicons dashicons-trash"></span>
									<?php echo esc_html__('View Trash', 'eightshift-utils'); ?>
								</a>

								<a href="<?php echo esc_url("/wp-admin/post-new.php?post_type={$type}"); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
									<span class="dashicons dashicons-plus-alt"></span>
									<?php echo esc_html__('Add New', 'eightshift-utils'); ?>
								</a>
							</div>
						<?php } ?>
					</div>

					<?php if ($adminLayoutContentListingTaxonomies[$type] && !$isTrash) { ?>
						<div class="<?php echo esc_attr("{$componentClass}__section-heading-intro-links"); ?>">
							<?php foreach ($adminLayoutContentListingTaxonomies[$type] as $taxSlug) { ?>
								<a href="<?php echo esc_url("/wp-admin/edit-tags.php?taxonomy={$taxSlug}&post_type={$type}"); ?>" class="<?php echo esc_attr("{$componentClass}__section-heading-intro-link"); ?>">
									<?php
										$taxonomy = get_taxonomy($taxSlug);
										$taxLabel = isset($taxonomy->label) ? $taxonomy->label : ucwords($taxSlug);
										echo esc_html($taxLabel);
									?>
								</a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<div class="<?php echo esc_attr("{$componentClass}__section-content"); ?> <?php echo (!$items) ? esc_attr("{$componentClass}__section-content--empty") : ''; ?>">
					<?php if ($items) { ?>
						<ul class="<?php echo esc_attr("{$componentClass}__list"); ?>">
							<?php foreach ($items as $item) { ?>
								<?php
								$id = $item['id'] ?? '';
								$title = $item['title'] ?? '';
								$status = $item['status'] ?? '';
								$editLink = $item['editLink'] ?? '';
								$trashLink = $item['trashLink'] ?? '';
								$trashPermanentlyLink = $item['trashPermanentlyLink'] ?? '';
								$trashRestoreLink = $item['trashRestoreLink'] ?? '';
								$level = $item['level'] ?? '';
								$isHome = $item['isHome'] ?? false;
								$isListing = $item['isListing'] ?? false;
								$dateCreated = $item['dateCreated'] ?? '';
								$dateUpdated = $item['dateUpdated'] ?? '';
								$excerpt = $item['excerpt'] ?? '';
								$author = $item['author'] ?? '';
								$isError = $item['isError'] ?? false;
								$isMine = $item['isMine'] ?? false;
								$slug = $item['slug'] ?? '';
								$image = $item['image'] ?? '';

								$titleLink = $editLink;
								if (!$editLink) {
									$titleLink = $slug;
								}

								$titleClass = '';
								if ($isTrash) {
									$titleClass = 'is-trash';
									$titleLink = '';
								}
								?>
								<li class="<?php echo esc_attr("{$componentClass}__list-item"); ?>" data-level="<?php echo esc_attr($level); ?>">
									<div class="<?php echo esc_attr("{$componentClass}__list-item-main"); ?>">
										<div class="<?php echo esc_attr("{$componentClass}__list-item-intro"); ?>">
											<a href="<?php echo esc_url($titleLink); ?>" class="<?php echo esc_attr("{$componentClass}__list-item-title {$titleClass}"); ?>">
												<?php if ($level > 0) { ?>
													<?php for ($i = 0; $i < $level; $i++) { ?>
														--
													<?php } ?>
												<?php } ?>
												<span class="dashicons <?php echo esc_attr($adminLayoutContentListingPostTypesIcons[$type] ?? ''); ?>"></span>
												<?php echo esc_html($title); ?>
											</a>

											<div class="<?php echo esc_attr("{$componentClass}__list-item-notices {$componentClass}__notices"); ?>">
												<?php if ($status && $status !== 'publish') { ?>
													<div class="<?php echo esc_attr("{$componentClass}__notice {$componentClass}__notice--status"); ?>">
														<?php echo esc_html(ucfirst($status)); ?>
													</div>
												<?php } ?>

												<?php if ($isHome) { ?>
													<div class="<?php echo esc_attr("{$componentClass}__notice {$componentClass}__notice--home"); ?>">
														<?php echo esc_html__('Home Page', 'eightshift-utils'); ?>
													</div>
												<?php } ?>

												<?php if ($isListing) { ?>
													<div class="<?php echo esc_attr("{$componentClass}__notice {$componentClass}__notice--archive"); ?>">
														<?php echo esc_html__('Archive list', 'eightshift-utils'); ?>
													</div>
												<?php } ?>

												<?php if ($isError) { ?>
													<div class="<?php echo esc_attr("{$componentClass}__notice {$componentClass}__notice--error"); ?>">
														<?php echo esc_html__('404', 'eightshift-utils'); ?>
													</div>
												<?php } ?>

												<?php if ($isMine) { ?>
													<div class="<?php echo esc_attr("{$componentClass}__notice {$componentClass}__notice--mine"); ?>">
														<?php echo esc_html__('Mine', 'eightshift-utils'); ?>
													</div>
												<?php } ?>
											</div>
										</div>

										<div class="<?php echo esc_attr("{$componentClass}__list-item-actions {$componentClass}__actions"); ?>">
											<?php if (!$isTrash) { ?>
												<?php if ($trashLink) { ?>
													<a href="<?php echo esc_url($trashLink); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
														<span class="<?php echo esc_attr("dashicons dashicons-trash"); ?> "></span>
														<?php echo esc_html__('Delete', 'eightshift-utils'); ?>
													</a>
												<?php } ?>

												<?php if ($editLink) { ?>
													<a href="<?php echo esc_url($editLink); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
														<span class="dashicons dashicons-edit"></span>
														<?php echo esc_html__('Edit', 'eightshift-utils'); ?>
													</a>
												<?php } ?>

												<?php if ($slug) { ?>
													<a href="<?php echo esc_url($slug); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
														<span class="dashicons dashicons-welcome-view-site"></span>
														<?php echo esc_html__('View', 'eightshift-utils'); ?>
													</a>
												<?php } ?>
											<?php } else { ?>
												<?php if ($trashPermanentlyLink) { ?>
													<a href="<?php echo esc_url($trashPermanentlyLink); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
														<span class="<?php echo esc_attr("dashicons dashicons-trash"); ?> "></span>
														<?php echo esc_html__('Delete permanently', 'eightshift-utils'); ?>
													</a>
												<?php } ?>

												<?php if ($trashRestoreLink) { ?>
													<a href="<?php echo esc_url($trashRestoreLink); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
														<span class="<?php echo esc_attr("dashicons dashicons-image-rotate"); ?> "></span>
														<?php echo esc_html__('Restore', 'eightshift-utils'); ?>
													</a>
												<?php } ?>
											<?php } ?>
										</div>
									</div>

									<?php if ($showDetails) { ?>
										<div class="<?php echo esc_attr("{$componentClass}__list-item-details"); ?>">
											<div class="<?php echo esc_attr("{$componentClass}__list-item-details-col"); ?>">
												<div class="<?php echo esc_attr("{$componentClass}__list-item-details-col-wrap"); ?>">
													<?php if ($author) { ?>
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
															<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
																<?php esc_html_e('Author', 'eightshift-utils') ?>:
															</div>
															<?php echo esc_html($author); ?>
														</div>
													<?php } ?>

													<?php if ($id) { ?>
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
															<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
																<?php esc_html_e('ID', 'eightshift-utils') ?>:
															</div>
															<?php echo esc_html($id); ?>
														</div>
													<?php } ?>

													<?php if ($slug) { ?>
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
															<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
																<?php esc_html_e('Url', 'eightshift-utils') ?>:
															</div>
															<a href="<?php echo esc_url($slug); ?>"><?php echo esc_html($slug); ?></a>
														</div>
													<?php } ?>

													<?php if ($dateCreated) { ?>
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
															<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
																<?php esc_html_e('Date Created', 'eightshift-utils') ?>:
															</div>
															<?php echo esc_html($dateCreated); ?>
														</div>
													<?php } ?>

													<?php if ($dateUpdated) { ?>
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
															<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
																<?php esc_html_e('Date Modified', 'eightshift-utils') ?>:
															</div>
															<?php echo esc_html($dateUpdated); ?>
														</div>
													<?php } ?>
												</div>
											</div>

											<div class="<?php echo esc_attr("{$componentClass}__list-item-details-col"); ?>">
												<?php if ($image) { ?>
													<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
															<?php esc_html_e('Featured Image', 'eightshift-utils') ?>:
														</div>
														<img class="<?php echo esc_attr("{$componentClass}__list-item-details-item-img"); ?>" src="<?php echo esc_url($image); ?>" />
													</div>
												<?php } ?>

												<?php if ($excerpt) { ?>
													<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item"); ?>">
														<div class="<?php echo esc_attr("{$componentClass}__list-item-details-item-title"); ?>">
															<?php esc_html_e('Excerpt', 'eightshift-utils') ?>:
														</div>
														<?php echo apply_filters('the_content', $excerpt); // phpcs:ignore Eightshift.Security.HelpersEscape.OutputNotEscaped ?>
													</div>
												<?php } ?>
											</div>

										</div>
									<?php } ?>

								</li>
							<?php } ?>
						</ul>
					<?php } ?>

					<?php
					if ($isSearched && !$items) {
						if ($isTrash) {
							/* Translators: %s will be replaces with the post type name. */
							echo sprintf(esc_html__('Sorry there are not items in the trash %s section.', 'eightshift-utils'), esc_html($type));
						} else {
							/* Translators: %s will be replaces with the post type name. */
							echo sprintf(esc_html__('Sorry there are not items that satisfies your search query in the %s section.', 'eightshift-utils'), esc_html($type));
						}
					}
					?>

					<?php
					if (!$isSearched && !$items) {
						if ($isTrash) {
							/* Translators: %s will be replaces with the post type name. */
							echo sprintf(esc_html__('Sorry there are not items in the trash %s section.', 'eightshift-utils'), esc_html($type));
						} else {
							/* Translators: %s will be replaces with the post type name. */
							echo sprintf(esc_html__('Sorry there are not items in the %s section.', 'eightshift-utils'), esc_html($type));
						}
					}
					?>
				</div>
			</div>
		<?php } ?>
	</div>

	<?php if (!$isTrash) { ?>
		<div class="<?php echo esc_attr("{$componentClass}__sidebar"); ?>">
			<div class="<?php echo esc_attr("{$componentClass}__sidebar-item"); ?>">
				<div class="<?php echo esc_attr("{$componentClass}__sidebar-item-title"); ?>">
					<div class="<?php echo esc_attr("{$componentClass}__sidebar-item-label"); ?>">
						<?php echo esc_html__('Details', 'eightshift-utils'); ?>
					</div>
				</div>

				<div class="<?php echo esc_attr("{$componentClass}__sidebar-info"); ?>">
					<?php echo esc_html__('Show/hide details will show you all items details that we can we find. Just a reminder this can be a very heavy page load so use it with caution!', 'eightshift-utils'); ?>
				</div>

				<a class="<?php echo esc_attr("{$componentClass}__sidebar-details-link button-secondary"); ?>" href="<?php echo esc_url($detailsUrl); ?>">
					<?php if ($showDetails) { ?>
						<?php echo esc_html__('Hide details', 'eightshift-utils'); ?>
					<?php } else { ?>
						<?php echo esc_html__('Show details', 'eightshift-utils'); ?>
					<?php } ?>
				</a>
			</div>

			<div class="<?php echo esc_attr("{$componentClass}__sidebar-item"); ?>">
				<div class="<?php echo esc_attr("{$componentClass}__sidebar-item-title"); ?>">
					<div class="<?php echo esc_attr("{$componentClass}__sidebar-item-label"); ?>">
						<?php echo esc_html__('Filter', 'eightshift-utils'); ?>
					</div>

					<a href="<?php echo esc_url($adminLayoutContentListingUrl); ?>" class="<?php echo esc_attr("{$componentClass}__action"); ?>">
						<span class="dashicons dashicons-remove"></span>
						<?php echo esc_html__('Clear', 'eightshift-utils'); ?>
					</a>
				</div>
				<form method="GET" action="" class="<?php echo esc_attr("{$componentClass}__form"); ?>">

					<div class="<?php echo esc_attr("{$componentClass}__input-wrap"); ?>">
						<input
							type="text"
							value="<?php echo esc_attr($search); ?>"
							name="s"
							placeholder="<?php echo esc_html__('Enter your search...', 'eightshift-utils'); ?>"
							class="<?php echo esc_attr("{$componentClass}__input"); ?>"
						/>
					</div>

					<?php foreach ($checkboxFilters as $filters) { ?>
						<?php
							$items = $filters['items'];
							$item = $filters['item'];
							$id = $filters['id'];
							$name = $filters['name'];
						?>

						<div class="<?php echo esc_attr("{$componentClass}__filter-type"); ?>">
							<div class="<?php echo esc_attr("{$componentClass}__filter-type-label"); ?>">
							<?php echo esc_html($name); ?>:
							</div>
							<?php foreach ($items as $type) { ?>
								<?php
								$checkboxId = "{$id}_{$type}";
								$checkPostTypes = array_flip($item);
								?>
								<label for="<?php echo esc_attr($checkboxId); ?>" class="<?php echo esc_attr("{$componentClass}__checkbox-label"); ?>">
									<input
										type="checkbox"
										value="<?php echo esc_attr($type); ?>"
										name="<?php echo esc_attr("{$id}[]"); ?>"
										id="<?php echo esc_attr($checkboxId); ?>"
										<?php checked(isset($checkPostTypes[$type])); ?>
										class="<?php echo esc_attr("{$componentClass}__checkbox"); ?>"
									/>
									<?php echo esc_html(ucwords($type)); ?>
								</label>
							<?php } ?>
						</div>
					<?php } ?>

					<!-- Post terms. -->
					<div class="<?php echo esc_attr("{$componentClass}__filter-type");?>">
						<div class="<?php echo esc_attr("{$componentClass}__filter-type-label"); ?>">
							<?php echo esc_html__('Post Terms', 'eightshift-utils'); ?>:
						</div>
						<?php
						foreach ($adminLayoutContentListingPostTaxonomies as $type => $terms) { ?>
							<?php
							$id = 'post_term';
							// Need this itemName for dynamic $item name.
							$itemName = ucwords(Helpers::kebabToCamelCase($type, "-"));
							$item = ${'post' . $itemName};
							$checkPostTypes = is_array($item) ? array_flip($item) : [];
							?>
							<a class="<?php echo esc_attr("{$componentClass}__checkbox-link"); ?>">
								<?php echo esc_html(ucwords(str_replace("-", " ", $type))); ?>

								<ul class="<?php echo esc_attr("{$componentClass}__checkbox-term-list"); ?>">
									<?php foreach ($terms as $key => $term) {
										$checkboxId = "{$id}_{$key}";
										?>
										<li class="<?php echo esc_attr("{$componentClass}__checkbox-term"); ?>">
											<label for="<?php echo esc_attr($checkboxId); ?>" class="<?php echo esc_attr("{$componentClass}__checkbox-label"); ?>">
												<input
													type="checkbox"
													value="<?php echo esc_attr($key); ?>"
													name="<?php echo esc_attr("{$type}[]"); ?>"
													id="<?php echo esc_attr($checkboxId); ?>" <?php checked(isset($checkPostTypes[$key])); ?>
													class="<?php echo esc_attr("{$componentClass}__checkbox"); ?>"
												/>
												<?php echo esc_attr($term); ?>
											</label>
										</li>
									<?php } ?>
								</ul>
							</a>
						<?php } ?>
					</div>

					<input type="hidden" value="<?php echo esc_attr($page); ?>" name="page" />
					<input type="hidden" value="<?php echo esc_attr((string) $showDetails); ?>" name="show_details" />

					<button class="<?php echo esc_attr("{$componentClass}__submit button-primary"); ?>">
						<span class="dashicons dashicons-search"></span>
						<?php echo esc_html__('Search', 'eightshift-utils'); ?>
					</button>
				</form>
			</div>

			<div class="<?php echo esc_attr("{$componentClass}__sidebar-item"); ?>">
				<div class="<?php echo esc_attr("{$componentClass}__sidebar-item-title"); ?>">
					<div class="<?php echo esc_attr("{$componentClass}__sidebar-item-label"); ?>">
						<?php echo esc_html__('Stats', 'eightshift-utils'); ?>
					</div>
				</div>
				<div class="<?php echo esc_attr("{$componentClass}__stats"); ?>">
					<?php foreach ($adminLayoutContentListingPostTypes as $type) { ?>
						<div class="<?php echo esc_attr("{$componentClass}__stat"); ?>">
							<div class="<?php echo esc_attr("{$componentClass}__stat-label"); ?>">
								<?php echo esc_html(ucwords($type)); ?>
							</div>
							<ul class="<?php echo esc_attr("{$componentClass}__stat-list"); ?>">
								<?php foreach ($adminLayoutContentListingPostStatuses as $status) { ?>
									<li class="<?php echo esc_attr("{$componentClass}__stat-list-item"); ?>">
										<?php echo esc_html(ucwords($status)); ?>
										<span class="<?php echo esc_attr("{$componentClass}__stat-list-item-count"); ?>">
											<?php echo esc_html($adminLayoutContentListingPostStats[$type]->$status); ?>
										</span>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>

			</div>
		</div>
	<?php } ?>
</div>
