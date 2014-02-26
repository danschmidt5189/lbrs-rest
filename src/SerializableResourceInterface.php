<?php
/**
 * SerializableResourceInterface.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Interface for resources that can be serialized
 *
 * This expands the interface to include methods that normalize the implementing
 * class into arrays and strings, e.g. for serializing and JSON-encoding.
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface SerializableResourceInterface extends ResourceInterface, ArrayableInterface, \Serializable, \JsonSerializable
{
	//
}
