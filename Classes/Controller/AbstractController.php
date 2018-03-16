<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  (c) 2011 Bastian Waidelich <bastian@typo3.org>
 *  (c) 2017 Leonie Philine Bitto <leonie@netcreators.nl>, Netcreators
 * 
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Netcreators\NcUsefulpages\Controller;

use Netcreators\NcUsefulpages\Exception\InvalidControllerActionArgumentError;
use Netcreators\NcUsefulpages\Exception\MissingControllerActionArgumentError;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


/**
 * Abstract base controller for the NcUsefulpages extension
 */
abstract class AbstractController extends ActionController {

	/**
	 * User ratings
	 */
	const RATING_USEFUL = 1, RATING_NOT_USEFUL = 2, RATING_UNDECIDED = 3;


	/**
	 * Sanitize incoming values
	 *
	 * @param string $argumentName
	 * @param string $type
	 *
	 * @throws \Netcreators\NcUsefulpages\Exception\MissingControllerActionArgumentError
	 * @return mixed
	 */
	protected function sanitizeIncoming($argumentName, $type) {

		if(!$this->request->hasArgument($argumentName)) {
			throw new MissingControllerActionArgumentError($argumentName);
		}

		// Validate and sanitize
		$argumentValue = $this->request->getArgument($argumentName);

		switch($type) {
			case 'integer':
				return (int)trim($argumentValue);

			default:
				return trim($argumentValue);
		}
	}


	/**
	 * Make sure a given value is one of a defined set
	 *
	 * @param string $argumentName
	 * @param string $argumentValue
	 * @param array $validValues
	 *
	 * @throws \Netcreators\NcUsefulpages\Exception\InvalidControllerActionArgumentError
	 *
	 * @return void
	 */
	protected function validateIncomingInSet($argumentName, $argumentValue, $validValues) {

		if(!in_array($argumentValue, $validValues)) {
			throw new InvalidControllerActionArgumentError($argumentName);
		}
	}


	/**
	 * Returns the correct TypoScript settings key depending on the user rating
	 *
	 * @param integer	$rating
	 * @param boolean	$upperCamelCase
	 *
	 * @return string
	 */
	protected function getRatingAsString($rating, $upperCamelCase = TRUE) {
		switch($rating) {
			case self::RATING_USEFUL:
				$ratingAsString = 'useful';
				break;

			case self::RATING_NOT_USEFUL:
				$ratingAsString = 'notUseful';
				break;

			case self::RATING_UNDECIDED:
			default:
				$ratingAsString = 'undecided';
				break;
		}

		if($upperCamelCase) {
			return ucfirst($ratingAsString);
		}

		return $ratingAsString;
	}


	/**
	 * Assigns a boolean template toggle variable
	 *
	 * @param string	$toggleKey
	 * @param integer	$rating
	 *
	 * @return void
	 */
	protected function assignTemplateToggleByRating($toggleKey, $rating) {

		// Select the correct TypoScript setting
		$this->view->assign($toggleKey,
			(boolean)intval(
				$this->getTypoScriptSettingByRating($toggleKey, $rating)
			)
		);
	}


	/**
	 * Returns a rating-based TypoScript setting.
	 *
	 * @param string	$typoScriptSettingKey
	 * @param integer	$rating
	 *
	 * @return string
	 */
	protected function getTypoScriptSettingByRating($typoScriptSettingKey, $rating) {

		// Select the correct TypoScript setting
		return 	$this->settings[
			$this->request->getControllerName()
		][
			$this->request->getControllerActionName()
		][
			$typoScriptSettingKey
		][
			'rated' . $this->getRatingAsString($rating)
		];
	}


	/**
	 * Override getErrorFlashMessage to present nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		$defaultFlashMessage = parent::getErrorFlashMessage();
		$localLangKey = sprintf('error.%s.%s', $this->request->getControllerName(), $this->actionMethodName);
		return $this->translate($localLangKey, $defaultFlashMessage);
	}


	/**
	 * helper function to render localized flashmessages
	 *
	 * @param string $action
	 * @param integer $severity optional severity code. One of the \TYPO3\CMS\Core\Messaging\FlashMessage constants
	 * @return void
	 */
	public function addFlashMessageForAction($action, $severity = FlashMessage::OK) {
		$messageLocallangKey = sprintf('flashMessage.%s.%s', $this->request->getControllerName(), $action);
		$localizedMessage = $this->translate($messageLocallangKey, '[' . $messageLocallangKey . ']');
		$titleLocallangKey = sprintf('%s.title', $messageLocallangKey);
		$localizedTitle = $this->translate($titleLocallangKey, '[' . $titleLocallangKey . ']');

		$this->addFlashMessage($localizedMessage, $localizedTitle, $severity);
	}


	/**
	 * helper function to use localized strings in NcUsefulpages controllers
	 *
	 * @param string $key locallang key
	 * @param string $defaultMessage the default message to show if key was not found
	 * @return string
	 */
	protected function translate($key, $defaultMessage = '') {
		$message = LocalizationUtility::translate($key, 'NcUsefulpages');
		if ($message === NULL) {
			$message = $defaultMessage;
		}
		return $message;
	}


	/**
	 * Usage 1: Forward _GET parameters of other plugins, so
	 * we end up on the same view as before when rating.
	 *
	 * Usage 2: Filter parameters which are to be taken into
	 * account when creating/choosing the 'page rating record' to
	 * apply the rating to.
	 *
	 * NOTE: We do NOT use any $_POST parameters, since this could
	 * mean sending emails, ordering products or making appointments in
	 * other plugins *twice* or at least creating errors.
	 *
	 * This means that if e.g. you find the search results for a
	 * certain search term useful, then the search term will only
	 * be registered and a separate page rating for each search term
	 * created, if the search is using GET instead of POST to send its
	 * form data.
	 *
	 * The same applies to the view being shown after the 'Useful' or
	 * 'Not useful' button has been clicked:
	 * Only if e.g. a search form has been submitted using the GET method,
	 * the same search results will be shown after the rating has been
	 * saved. If the POST method is used, an empty search form is shown.
	 *
	 * If wished for by a client then we *could*, however, think about
	 * creating a 'white list' of allowed, harmless $_POST parameters
	 * to forward.
	 *
	 * @return array
	 */
	public function get3rdPartyParameters()
	{
		$thirdPartyParameters = [];
		$thirdPartyParametersFilter = [

			// Exclude our own plugin argument namespace.
			'tx_ncusefulpages_ncusefulpages',

			// Cache Hash is being rebuilt for links and form action URIs.
			// For storing parameters we want cHash excluded altogether.
			'cHash'
        ];
		foreach ($_GET as $parameterName => $parameterValue) {
			if (in_array($parameterName, $thirdPartyParametersFilter)) {
				continue;
			}
			$thirdPartyParameters[$parameterName] = $parameterValue;
		}
		return $thirdPartyParameters;
	}


	/**
	 * Returns the given parameter array in a normalized form, sorted and urlEncoded, which allows to clearly identify
	 * each unique parameter set.
	 *
	 * @param array	$parameters
	 *
	 * @return string
	 */
	protected function getNormalizedParameterString($parameters) {

		$this->sortArrayKeysRecursively($parameters);

		return GeneralUtility::implodeArrayForUrl('', $parameters);
	}


	/**
	 * Alphabetically sort a deeply nested array by array keys, recursively.
	 *
	 * @param array		&$array
	 *
	 * @return void
	 */
	protected function sortArrayKeysRecursively(&$array)
	{
		foreach ($array as &$value) {
			if (is_array($value)) {
				$this->sortArrayKeysRecursively($value);
			}
		}

		ksort($array);
	}


	/**
	 * Redirects the request to another action and / or controller,
	 * with the option to pass on non-controller-prefixed 3rd party parameters,
	 * as well as with the option to jump to an #anchor on the target page.
	 *
	 * Redirect will be sent to the client which then performs another request to the new URI.
	 *
	 * NOTE: This method only supports web requests and will thrown an exception
	 * if used with other request types.
	 *
	 * @param string	$actionName Name of the action to forward to
	 * @param array		$pluginArguments Arguments to pass to the target action
	 * @param array		$thirdPartyParameters Arguments to pass on to other extensions on the same page
	 * @param string	$section The #anchor
	 *
	 * @return void
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException If the request is not a web request
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @see forward()
	 * @api
	 */
	protected function redirectWith3rdPartyParameters($actionName, array $pluginArguments = NULL, array $thirdPartyParameters = NULL, $section = '') {
		if (!$this->request instanceof Request) throw new UnsupportedRequestTypeException('redirectWith3rdPartyParameters() only supports web requests.', 1362411546);

		$uri = $this->uriBuilder
			->reset()
			->setArguments((array)$thirdPartyParameters)
			->uriFor($actionName, (array)$pluginArguments);

		if(strlen($section)) {
			$uri .= '#' . $section;
		}

		$this->redirectToUri($uri);
	}

}

