<?php
/**
 * ResourceController.php
 *
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 */

namespace Lbrs\Rest;

use Yii;
use CController;

/**
 * Controller that manages a class of Resources
 *
 * @package    lbrs-rest
 * @subpackage resources
 */
abstract class ResourceController extends CController
{
	/**
	 * @var ResponderInterface
	 */
	public $responder;

	/**
	 * @var array<NegotiationRule>
	 */
	public $negotiationRules;

	/**
	 * Sends a resource representation back to the client
	 *
	 * @param ResourceInterface $resource The resource
	 * @param array $converterOptions Additional options for converting the resource
	 * to the MIME type given by negotiateType()
	 * @param array $responderOptions Additional options passed to the responder
	 *
	 * @return void Sends the response to the client
	 */
	public function respondWith(ResourceInterface $resource, $converterOptions = array(), $responderOptions = array())
	{
		$mimeType   = $this->negotiateType();
		$serialized = $resource->convertTo($mimeType, $converterOptions);
		$this->responder->sendResponse($serialized, $responderOptions);
	}

	/**
	 * Returns the negotiated MIME type of the response
	 *
	 * @return string The MIME type with which to respond to the current HTTP request
	 */
	public function negotiateType()
	{
		foreach ($this->negotiationRules as $rule) {
			if (($mimeType = $rule->negotiate(Yii::app()->request)) !== null) {
				return $mimeType;
			}
		}

		return $this->defaultMimeType();
	}

	/**
	 * Returns the default MIME type for responses returned by this controller
	 *
	 * @return string Default MIME type
	 */
	public function defaultMimeType()
	{
		return MIME::VIEW;
	}
}
