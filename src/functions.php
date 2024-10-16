<?php
/**
 * Singleton class for managing global custom dynamic tags in Bricks Builder.
 *
 * @category   Custom
 * @package    HookedFiltered
 * @author     Daniel Iser <daniel@hookedfiltered.com>
 * @license    GPL-3.0-or-later https://www.gnu.org/licenses/gpl-3.0.html
 * @version    GIT: <git_id>
 * @link       https://hookedfiltered.com
 * @copyright: Hooked & Filtered 2024
 */

namespace HookedFiltered\BricksBuilder;

/**
 * Helper function to register a custom tag
 *
 * @param string   $name     The tag name.
 * @param array    $options  The tag options.
 * @param callable $callback The callback function to process the tag.
 *
 * @return void
 */
function register_custom_tag( $name, $options, $callback ) {
	\HookedFiltered\BricksBuilder\GlobalTags::get_instance()->register_tag( $name, $options, $callback );
}
