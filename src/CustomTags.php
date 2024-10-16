<?php
/**
 * Abstract class for Quick custom dynamic tags in Bricks Builder.
 *
 * @category   Custom
 * @package    HookedFiltered
 * @author     Daniel Iser <daniel@hookedfiltered.com>
 * @license    GPL-3.0-or-later https://opensource.org/licenses/GPL-3.0
 * @version    GIT: <git_id>
 * @link       https://hookedfiltered.com
 * @copyright: Hooked & Filtered 2024
 */

namespace HookedFiltered\BricksBuilder;

use HookedFiltered\BricksBuilder\GlobalTags;

/**
 * Class CustomTags
 *
 * @category Custom
 * @package  HookedFiltered
 * @author   Daniel Iser <daniel@hookedfiltered.com>
 * @license  GPL-3.0-or-later https://opensource.org/licenses/GPL-3.0
 * @link     https://hookedfiltered.com
 */
abstract class CustomTags {

	/**
	 * Constructor to automatically register tags.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->register_tags();
	}

	/**
	 * Register all custom tags.
	 *
	 * @return void
	 */
	public function register_tags() {
		$tags = $this->tags();

		foreach ( $tags as $tag ) {
			$callable = 'get_' . str_replace( [ '{', '}' ], '', $tag['name'] );

			GlobalTags::get_instance()->register_tag(
				$tag['name'],
				[
					'label' => $tag['label'],
					'group' => $tag['group'],
				],
				is_callable( [ $this, $callable ] ) ? [ $this, $callable ] : '__return_empty_string'
			);
		}
	}

	/**
	 * Define the list of custom tags.
	 *
	 * @return array{name:string,label:string,group:string}[]
	 */
	abstract public function tags();
}
