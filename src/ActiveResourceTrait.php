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
	 * @var array Converter configurations indexed by MIME-type
	 */
	public $converters;

	/**
	 * @var array<ConverterInterface>
	 */
	private $_converters = array();

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
		return $this->getPrimaryKey();
	}

	/**
	 * Converts the resource to the given MIME type
	 *
	 * @param string $mimeType MIME type into which the resource should be converted
	 * @param array $options Additional options passed to the underlying converter
	 *
	 * @return string Resource representation in the given MIME-type
	 *
	 * @throws Exception If the resource cannot be converted to the given type
	 *
	 * @see ResourceInterface::convertTo()
	 * @see ConverterInterface::convert()
	 */
	public function convertTo($mimeType, $options = array())
	{
		if (!$this->isConvertableTo($mimeType, $options)) {
			throw new Exception(sprintf('%s::$converters is not configured for type "%s".', get_class($this), $mimeType));
		}

		return $this->getConverter($mimeType, $options)->convert($this, $mimeType, $options);
	}

	/**
	 * Returns whether the resource can be converted to the given type
	 *
	 * @param string $mimeType The MIME type to check
	 * @param array $options Additional conversion options
	 *
	 * @return bool Whether the resource is convertible to the given type
	 */
	public function isConvertableTo($mimeType, $options = array())
	{
		return $this->getConverter($mimeType, $options) !== null;
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

	/**
	 * Returns the converter object for a given MIME type
	 *
	 * If an existing converter is able to handle the given MIME type, it is returned.
	 * Otherwise, this checks to see if a converter configuration exists for the
	 * given MIME type. If it does, it attempts to instantiate and return the
	 * converter.
	 *
	 * @param string $mimeType MIME-type the converter must be able to handle
	 *
	 * @return ConverterInterface|null
	 *
	 * @throws type description
	 */
	public function getConverter($mimeType, $options = array())
	{
		// Return the first converter able to handle the MIME type
		foreach ($this->_converters as $converter) {
			if ($converter->canConvert($mimeType, $options)) {
				return $converter;
			}
		}

		// Check for a fallback configuration
		$configurations = (array) $this->converters;

		if (!isset($configurations[$mimeType])) {
			return null;
		}

		// There is a configuration. Try to create, cache, and return it
		$converter = $configurations[$mimeType];
		if (is_string($converter) || is_array($converter)) {
			$converter = Yii::createComponent($converter);
		}

		$this->addConverter($converter);

		return $converter;
	}

	/**
	 * Adds a new converter
	 *
	 * @param ConverterInterface $converter The converter
	 * @param int $position Zero-indexed position at which the converter should
	 * be added to the existing converters list. Defaults to null, meaning it
	 * is added to the end of the list.
	 *
	 * @return self For chaining
	 *
	 * @throws InvalidArgumentException If the converter does not implement ConverterInterface
	 */
	public function addConverter($converter, $position = null)
	{
		if (!$converter instanceof ConverterInterface) {
			throw new \InvalidArgumentException(sprintf('Converter must be an instance of ConverterInterface, %s given.', is_object($converter) ? get_class($converter) : gettype($converter)));
		}

		if ($position === null) {
			$this->_converters[] = $converter;
		} else {
			$this->_converters =
				array_slice($this->_converters, 0, $position) +
				array($converter) +
				array_slice($this->_converters, $position, count($this->_converters) - 1);
		}
		return $this;
	}
}
