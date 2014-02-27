<?php
/**
 * ConvertableInterface.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Interface for classes that can convert themselves to strings in different formats
 *
 * This class is like the native Serializable interface, except that it expects
 * the implementing class to know how to handle conversion to an arbitrary MIME type.
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface ConvertableInterface
{
	/**
	 * Represents the object as a string of the given MIME type
	 *
	 * @param string $mimeType MIME-type to which the object is converted
	 * @param array $options Additional conversion options
	 *
	 * @return string The object represented in the given type
	 *
	 * @throws Lbrs\Rest\Exception If the object cannot be represented as the given type
	 */
	public function convertTo($mimeType, $options = array());

	/**
	 * Returns whether the object can be converted to the given type
	 *
	 * @param string $mimeType The type to convert to
	 * @param array $options Additional conversion options
	 *
	 * @return bool Whether the object can be converted to the given MIME type
	 */
	public function isConvertableTo($mimeType, $options = array());
}
