<?php
/**
 * ConverterInterface.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Interface for classes that convert resources to strings
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface ConverterInterface
{
	/**
	 * Converts a resource to the given MIME type
	 *
	 * @param mixed $resource Data to be converted to $mimeType
	 * @param string $mimeType MIME type to which the resource should be converted
	 * @param array $options Additional conversion options
	 *
	 * @return string The representation of the resource in the given MIME type
	 *
	 * @throws Lbrs\Rest\Exception If conversion fails or is impossible
	 */
	public function convert($resource, $mimeType, $options = array());

	/**
	 * Returns whether the converter can convert resources to the given type
	 *
	 * @param string $mimeType Type to convert to
	 * @param array $options Conversion options
	 *
	 * @return bool Whether the converter can convert resources to the given type with options
	 */
	public function canConvert($mimeType, $options = array());
}
