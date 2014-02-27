<?php
/**
 * ActiveResourceTrait.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * ActiveRecord trait that implements the SerializableResourceInterface
 *
 * If you don't want to inherit from Lbrs\Rest\ActiveResource, add this trait to
 * your ActiveRecord base class and add "implements SerializableResourceInterface"
 * to its class definition.
 *
 * @package    lbrs-rest
 * @subpackage resources
 */
trait ActiveResourceTrait
{
	/**
	 * @see ConvertableTrait implements the ConvertableInterface
	 */
	use ConvertableTrait;

	/**
	 * Returns whether the resource is in sync with its version at the given timestamp
	 *
	 * Child classes should override this method to perform actual checking.
	 *
	 * @see ResourceInterface::isFresh()
	 */
	public function isFresh($timestamp = null)
	{
		throw new Exception(sprintf('%s::isFresh(int $timestamp = null) is not implemented.', get_class($this)));
	}

	/**
	 * @read-property isFresh
	 */
	public function getIsFresh()
	{
		return $this->isFresh();
	}

	/**
	 * Returns the resource ID
	 *
	 * @return mixed The resource ID
	 *
	 * @see ResourceInterface::getResourceId()
	 */
	public function getResourceId()
	{
		return $this->primaryKey;
	}

	/**
	 * Returns the attributes of the record
	 *
	 * @param array $options Attribute names. Leave empty to return all attributes.
	 *
	 * @return array Record attribute values indexed by name
	 *
	 * @see SerializableResourceInterface::toArray()
	 */
	public function toArray($options = array())
	{
		return $this->getAttributes($options ?: null);
	}

	/**
	 * Reconstructs the record from its array representation
	 *
	 * @param array $arrayed The array representation of the record
	 * @param array $options Additional reconstruction options
	 *
	 * @return void
	 *
	 * @see SerializableResourceInterface::fromArray()
	 */
	public function fromArray($arrayed, $options = array())
	{
		$this->setAttributes($arrayed, false);

		if (!$this->refresh()) {
			throw new Exception(sprintf('Unable to reconstruct record of type %s', get_class($this)));
		}

		return $this;
	}

	/**
	 * Returns the json-encodable representation of the record
	 *
	 * @return array The json-serializable representation of the record
	 *
	 * @see JsonSerializable
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Serializes the record as a string
	 *
	 * @return string
	 *
	 * @see Serializable
	 */
	public function serialize()
	{
		return serialize($this->toArray());
	}

	/**
	 * Unserializes the record
	 *
	 * @param string $string Serialized record representation
	 *
	 * @throws Lbrs\Rest\Exception If the serialized record is invalid
	 *
	 * @see Serializable
	 */
	public function unserialize($string)
	{
		$this->fromArray(unserialize($string));
	}
}
