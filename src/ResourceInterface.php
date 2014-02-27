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
 * A resource is an abstract datatype that can be represented in multiple formats.
 * Resources must be able to convert themselves to representations in an arbitrary
 * format, throwing an Exception when they are unable to do so. In addition, Resources
 * have the concept of "freshness" and persistence.
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface ResourceInterface extends ConvertableInterface
{
	/**
	 * Returns the unique resource id
	 *
	 * @return mixed The unique resource id, or null if it is not yet persisted
	 */
	public function getResourceId();

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
