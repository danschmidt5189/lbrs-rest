<?php
/**
 * MIME.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

/**
 * Holds MIME type constants
 *
 * @package    lbrs-rest
 * @subpackage resources
 */
abstract class MIME
{
	const HTML    = 'text/html';
	const PARTIAL = 'text/partial+html';
	const JSON    = 'application/json';
	const HAL     = 'application/hal+json';
	const XML     = 'application/xml';
}
