<?php
/**
 * ActiveResource.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Base class for ActiveRecord objects that implement SerializableResourceInterface
 *
 * @package    lbrs-rest
 * @subpackage resources
 */
abstract class ActiveResource extends \CActiveRecord implements SerializableResourceInterface
{
	/**
	 * @see ActiveResourceTrait handles interface implementation
	 */
	use ActiveResourceTrait;
}
