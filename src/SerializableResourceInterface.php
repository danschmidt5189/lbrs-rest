<?php
/**
 * SerializableResourceInterface.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

use Serializable;
use JsonSerializable;

/**
 * Interface for resources that support self-serialization
 *
 * This expands ResourceInterface to include methods that convert the implementing
 * class into arrays and strings, e.g. for serializing and JSON-encoding.
 *
 * @package    lbrs-rest
 * @subpackage interfaces
 */
interface SerializableResourceInterface extends ResourceInterface, ArrayableInterface, Serializable, JsonSerializable
{
	//
}
