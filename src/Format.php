<?php
/**
 * Format.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Represents content formats
 *
 * Formats are essentially abbreviations for specific MIME types. A format corresponds
 * to just a single MIME type, however a MIME type may correspond to multiple formats.
 *
 * @package    lbrs-rest
 * @subpackage resources
 */
class Format
{
	const HTML    = 'html';
	const VIEW    = 'view';
	const PARTIAL = 'partial';
	const JSON    = 'json';
	const HAL     = 'hal';
	const XML     = 'xml';

	/**
	 * @var array Lists of formats indexed by the corresponding MIME type
	 */
	public $typeMap = array(
		MIME::XML     => array(self::XML),
		MIME::HAL     => array(self::HAL),
		MIME::JSON    => array(self::JSON),
		MIME::HTML    => array(self::HTML, self::VIEW),
		MIME::PARTIAL => array(self::PARTIAL),
	);

	/**
	 * Returns the MIME type of a given format
	 *
	 * @param string $format Name of the format
	 *
	 * @return string|null MIME type for the given format
	 */
	public function getTypeOfFormat($format)
	{
		foreach ($this->typeMap as $mimeType => $formats) {
			if (in_array(strtolower($format), array_map('strtolower', (array) $formats))) {
				return $mimeType;
			}
		}

		return null;
	}
}
