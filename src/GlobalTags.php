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
 * Class GlobalTags
 *
 * @category Custom
 * @package  HookedFiltered
 * @author   AI Assistant
 * @license  GPL-3.0-or-later https://www.gnu.org/licenses/gpl-3.0.html
 * @link     https://hookedfiltered.com
 */
class GlobalTags {

	/**
	 * Singleton instance
	 *
	 * @var GlobalTags|null
	 */
	private static $instance = null;

	/**
	 * Array to store registered tags
	 *
	 * @var array
	 */
	private $tags = [];

	/**
	 * Cache for custom tag results
	 *
	 * @var array
	 */
	private $cache = [];

	/**
	 * Private constructor to prevent direct instantiation
	 *
	 * @return void
	 */
	private function __construct() {
		$this->add_hooks();
	}

	/**
	 * Get the singleton instance
	 *
	 * @return GlobalTags
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add WordPress hooks
	 *
	 * @return void
	 */
	private function add_hooks() {
		add_filter( 'bricks/dynamic_tags_list', [ $this, 'add_custom_tags' ] );
		add_filter( 'bricks/dynamic_data/render_tag', [ $this, 'get_custom_tag' ], 20, 3 );
		add_filter( 'bricks/dynamic_data/render_content', [ $this, 'render_custom_tag' ], 20, 3 );
		add_filter( 'bricks/frontend/render_data', [ $this, 'render_custom_tag' ], 20, 2 );
	}

	/**
	 * Register a new custom tag
	 *
	 * @param string   $name     The tag name.
	 * @param array    $options  The tag options.
	 * @param callable $callback The callback function to process the tag.
	 *
	 * @return void
	 */
	public function register_tag( $name, $options, $callback ) {
		$default_options = [
			'label' => '',
			'group' => '',
			// Add more default options as needed.
		];

		$options = wp_parse_args( $options, $default_options );

		$tag_id = $this->parse_tag_id( $name );

		$this->tags[ $tag_id ] = [
			'name'     => $name,
			'options'  => $options,
			'callback' => $callback,
		];
	}

	/**
	 * Add custom tags to the list of tags.
	 *
	 * @param array{name:string,label:string,group:string}[] $tags The list of tags.
	 *
	 * @return array{name:string,label:string,group:string}[]
	 */
	public function add_custom_tags( $tags ) {
		foreach ( $this->tags as $tag ) {
			$tags[] = [
				'name'  => $tag['name'],
				'label' => isset( $tag['options']['label'] ) ? $tag['options']['label'] : '',
				'group' => isset( $tag['options']['group'] ) ? $tag['options']['group'] : '',
			];
		}
		return $tags;
	}

	/**
	 * Get the value of a custom tag if it exists.
	 *
	 * @param string       $tag     The tag name.
	 * @param \WP_Post|int $post    The post object or ID.
	 * @param string       $context The context of the tag.
	 *
	 * @return string
	 */
	public function get_custom_tag( $tag, $post = null, $context = 'text' ) {
		if ( ! is_string( $tag ) ) {
			return $tag;
		}

		$post_id = $this->get_post_id( $post );
		$cache_key = $this->get_cache_key( $tag, $post_id, $context );

		// Check if the result is already cached
		if ( isset( $this->cache[ $cache_key ] ) ) {
			return $this->cache[ $cache_key ];
		}

		$tag_id = $this->parse_tag_id( $tag );

		if ( isset( $this->tags[ $tag_id ] ) && is_callable( $this->tags[ $tag_id ]['callback'] ) ) {
			$result = call_user_func( $this->tags[ $tag_id ]['callback'], $post_id, $tag, $context );
			$this->cache[ $cache_key ] = $result; // Cache the result
			return $result;
		}

		return $tag;
	}

	/**
	 * Generate a unique cache key based on tag, post ID, and context
	 *
	 * @param string $tag     The tag name.
	 * @param int    $post_id The post ID.
	 * @param string $context The context of the tag.
	 *
	 * @return string
	 */
	private function get_cache_key( $tag, $post_id, $context ) {
		return md5( $tag . '|' . $post_id . '|' . $context );
	}

	/**
	 * Render a custom tag.
	 *
	 * @param string       $content The content to render.
	 * @param \WP_Post|int $post    The post object or ID.
	 * @param string       $context The context in which the content is rendered.
	 *
	 * @return string
	 */
	public function render_custom_tag( $content, $post = null, $context = 'text' ) {
		if ( empty( $this->tags ) ) {
			return $content;
		}

		$post_id = $this->get_post_id( $post );
		$pattern = $this->get_tag_pattern();

		return preg_replace_callback(
			$pattern,
			function ( $matches ) use ( $post_id, $context ) {
				return $this->get_custom_tag( $matches[0], $post_id, $context );
			},
			$content
		);
	}

	/**
	 * Get post ID from various input types
	 *
	 * @param \WP_Post|int|null $post The post object, ID, or null.
	 *
	 * @return int
	 */
	private function get_post_id( $post = null ) {
		if ( is_object( $post ) ) {
			return $post->ID;
		}
		if ( is_numeric( $post ) ) {
			return (int) $post;
		}
		return get_the_ID();
	}

	/**
	 * Parse tag id from full tag string
	 *
	 * @param string $tag The full tag string.
	 *
	 * @return string
	 */
	private function parse_tag_id( $tag ) {
		$trimmed_tag = trim( $tag, '{}' );
		$parts       = explode( ':', $trimmed_tag );

		return $parts[0];
	}

	/**
	 * Get regex pattern for matching tags
	 *
	 * @return string
	 */
	private function get_tag_pattern() {
		static $pattern = null;

		if ( null === $pattern ) {
			$tag_names = array_map(
				function ( $tag ) {
					return preg_quote( trim( $tag['name'], '{}' ), '/' );
				}, $this->tags
			);

			$pattern = '/\{(' . implode( '|', $tag_names ) . ')(:[^}]+)?\}/';
		}

		return $pattern;
	}
}
