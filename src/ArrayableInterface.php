<?php
/**
 * ArrayableInterface.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Interface for classes that have an array representation
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface ArrayableInterface
{
	/**
	 * Returns the array representation of the object
	 *
	 * @param array $options Options for converting the object to an array
	 *
	 * @return array Array representation of the object
	 */
	public function toArray($options = array());

	/**
	 * Reconstructs a resource from its array representation
	 *
	 * @param array $arrayed The array representation of the resource. This should
	 * be the value returned by toArray().
	 * @param array $options Additional options for the reconstruction process
	 *
	 * @return void
	 *
	 * @throws Lbrs\Rest\Exception If unable to reconstruct from the given data
	 */
	public function fromArray($arrayed, $options = array());
}
