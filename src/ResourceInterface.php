<?php
/**
 * ResourceInterface.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Interface for classes that represent a resource
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface ResourceInterface
{
	/**
	 * Returns the unique resource id
	 *
	 * @return mixed The unique resource id, or null if it is not yet persisted
	 */
	public function getResourceId();

	/**
	 * Represents the resource as a string of the given MIME type
	 *
	 * @param string $mimeType MIME-type to which the resource is converted
	 * @param array $options Additional options for the representation
	 *
	 * @return string The resource represented in the given type
	 *
	 * @throws Lbrs\Rest\Exception If the resource cannot be represented as the given type
	 */
	public function convertTo($mimeType, $options = array());

	/**
	 * Returns whether the resource is fresh as of the given timestamp
	 *
	 * @param int $timestamp The timestamp. If the resource has been modified since
	 * this time, isFresh() should return false. Defaults to null, in which case
	 * the current timestamp should be used.
	 *
	 * @return bool Whether the resource is fresh as of the given timestamp
	 */
	public function isFresh($timestamp = null);
}
