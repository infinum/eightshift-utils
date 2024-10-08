<?php

/**
 * The file that defines actions on plugin activation.
 *
 * @package EightshiftUtils
 */

declare(strict_types=1);

namespace EightshiftUtils;

use EightshiftUtilsVendor\EightshiftLibs\Plugin\HasActivationInterface;

/**
 * The plugin activation class.
 */
class Activate implements HasActivationInterface
{
	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate(): void
	{
		\flush_rewrite_rules();
	}
}
