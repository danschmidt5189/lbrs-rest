<?php
/**
 * ConvertableTrait.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

use Yii;

/**
 * Trait that implements the ConvertableInterface
 *
 * Delegates conversion to classes based on {@property converters} configurations.
 * Note that this uses Yii::createComponent() to instantiate converters based
 * on their configurations.
 *
 * @package    lbrs-rest
 * @subpackage resources
 */
trait ConvertableTrait
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
