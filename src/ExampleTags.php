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

/**
 * ExampleTags class.
 */
class ExampleTags extends \HookedFiltered\BricksBuilder\CustomTags {

	/**
	 * Get the list of custom tags.
	 *
	 * @return array{name:string,label:string,group:string}[]
	 */
	public function tags() {
		return [
			[
				'name'  => '{tag}',
				'label' => 'Tag',
				'group' => 'Example',
			],
			[
				'name'  => '{tag_with_attr}',
				'label' => 'Tag With Attr',
				'group' => 'Example',
			],
		];
	}

	/**
	 * Handle the 'tag' custom tag without attributes.
	 *
	 * @param int    $post_id The post ID.
	 * @param string $tag     The full tag string, e.g., "{tag}".
	 * @param string $context The context of the tag.
	 *
	 * @return string The processed tag value.
	 */
	public function get_tag( $post_id, $tag, $context ) {
		// Implement logic specific to 'tag'
		// For demonstration, return a static value or fetch post meta.
		return 'Processed Tag Value';
	}

	/**
	 * Handle the 'tag_with_attr' custom tag with attributes.
	 *
	 * @param int    $post_id The post ID.
	 * @param string $tag     The full tag string, e.g., "{tag_with_attr:attr1:attr2}".
	 * @param string $context The context of the tag.
	 *
	 * @return string The processed tag value.
	 */
	public function get_tag_with_attr( int $post_id, string $tag, string $context ): string {
		// Remove curly braces.
		$trimmed_tag = trim( $tag, '{}' );

		// Split into tag name and attributes.
		$parts = explode( ':', $trimmed_tag );
		array_shift( $parts ); // Remove the tag name.

		// Extract attributes safely.
		$attribute1 = isset( $parts[0] ) ? sanitize_text_field( $parts[0] ) : '';
		$attribute2 = isset( $parts[1] ) ? sanitize_text_field( $parts[1] ) : '';

		// Implement logic based on attributes.
		// For demonstration, concatenate attributes.
		return "Attribute1: {$attribute1}, Attribute2: {$attribute2}";
	}
}

new ExampleTags();
