# Bricks Custom Dynamic Tags

A library for easily creating custom dynamic tags for Bricks Builder.

## Description

This library provides a simple way to create custom dynamic tags for use with the Bricks Builder WordPress page builder. It allows developers to quickly implement custom tags in their plugins, themes, or custom code.

For a detailed guide on how to use this library, check out our blog post: [Custom Dynamic Tags for Bricks Builder the Easy Way](https://hookedfiltered.com/custom-dynamic-tags-for-bricks-builder-the-easy-way/)

## Installation

1. Copy the `bricks-custom-dynamic-tags` folder into your project (e.g., your theme or plugin directory).
2. Include the necessary files in your project:

## Implementation

### Function-Based Implementation.

Quickly add custom tags with a simple function. Register your tags in the same place as any other related logic.

```php
use function HookedFiltered\BricksBuilder\register_custom_tag;

register_custom_tag('my_custom_tag', [
    'label' => 'My Custom Tag',
    'group' => 'Custom',
], function($post_id, $tag, $context) {
    // Implement your custom tag logic here
    return 'Custom tag value';
});
```

### Class-Based Implementation.

Keep your custom tags organized into separate classes.

1. Create a new class that extends `HookedFiltered\BricksBuilder\CustomTags`:

```php
use HookedFiltered\BricksBuilder\CustomTags;
class MyCustomTags extends CustomTags {
    public function tags() {
        return [
            [
                'name' => '{my_custom_tag}',
                'label' => 'My Custom Tag',
                'group' => 'Custom',
            ],
            // Add more tags as needed
        ];
    }
    public function get_my_custom_tag($post_id, $tag, $context) {
        // Implement your custom tag logic here
        return 'Custom tag value';
    }
}
```

Or for tags with attributes:

```php
use HookedFiltered\BricksBuilder\CustomTags;
class MyCustomTags extends CustomTags
{
    public function tags()
    {
        return [
			[
				'name'  => '{tag_with_attr}',
				'label' => 'Tag With Attr',
				'group' => 'Example',
			],
        ];
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
```

2. Instantiate your custom tags class:

```php
new MyCustomTags();
```

3. Your custom tags are now ready to use in Bricks Builder!

## Notes

- This library is intended for use within plugins, themes, or custom code implementations.
- It is not designed to be used as a standalone plugin.
- Make sure to properly namespace and integrate this library into your existing project structure.

For more information and advanced usage, please refer to our [detailed guide](https://hookedfiltered.com/custom-dynamic-tags-for-bricks-builder-the-easy-way/).
