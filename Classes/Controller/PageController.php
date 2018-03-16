<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Carmen Popoviciu <extensions@netcreators.nl>, Netcreators
*  (c) 2017 Leonie Philine Bitto <leonie@netcreators.nl>, Netcreators
*  			
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
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
use Netcreators\NcUsefulpages\Domain\Model\Page;
use Netcreators\NcUsefulpages\Domain\Repository\PageRepository;
use Netcreators\NcUsefulpages\Exception\InvalidControllerActionArgumentError;
use Netcreators\NcUsefulpages\Exception\MissingControllerActionArgumentError;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Controller for the Page object
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class PageController extends AbstractController {


	/**
	 * Note: TYPO3 requires the fully qualified name for automatic injection!
	 *
	 * @var \Netcreators\NcUsefulpages\Domain\Repository\PageRepository
	 * @inject
	 */
	protected $pageRepository;


	/**
	 * Index action (default action).
	 *
	 * @return void
	 */
	public function indexAction() {
		/** @var TypoScriptFrontendController $TSFE */
		global $TSFE;

		// Assign page ID of the current page.
		$this->view->assign('pageID', 				$TSFE->id);

		// Assign URL of the current page.
		$this->view->assign('pageURL', 				$this->getPageURL());

		// Assign title of the current page.
		$this->view->assign('pageTitle', 			$TSFE->page['title']);

		// Assign ratings
		$this->view->assign('ratingUseful',			self::RATING_USEFUL);
		$this->view->assign('ratingNotUseful',		self::RATING_NOT_USEFUL);
		$this->view->assign('ratingUndecided',		self::RATING_UNDECIDED);

		// Assign parameters of 3rd parties (TYPO3 CMS and other extensions) to forward and store with the rating.
		$this->view->assign('thirdPartyParameters',	$this->get3rdPartyParameters());
	}


	/**
	 * Rate action.
	 *
	 * @return void
	 */
	public function rateAction(){

		// Sanitize and validate incoming values.
		try {
			$pageID 	= $this->sanitizeIncoming('pageID', 	'integer');
			$pageURL	= $this->sanitizeIncoming('pageURL', 	'string');
			$pageTitle 	= $this->sanitizeIncoming('pageTitle', 	'string');
			$rating		= $this->sanitizeIncoming('rating',		'integer');

		} catch(MissingControllerActionArgumentError $exception) {

			$this->addFlashMessage(
				$exception->getMessage(),
				'Missing Controller Action Argument',
				FlashMessage::ERROR
			);

			/** @throws StopActionException */
			$this->redirectWith3rdPartyParameters(
				'index',
				[],
				$this->get3rdPartyParameters(),
				'tx-ncusefulpages-pi1'
			);

			// This return is never called.
			// It is just here for code flow clarity.
			// Also, it stops PHPStorm from complaining below about $pageID, $pageTitle, $pageURL and $rating
			// *possibly* not having been defined yet.
			return;
		}


		// Do not let robots tamper with our page rating.
		$robotest = $_POST['robotest'];
		if ($robotest != '') {
			$this->addFlashMessage(
				'Robot detected',
				FlashMessage::ERROR
			);

			$this->redirectWith3rdPartyParameters(
				'index',
				[],
				$this->get3rdPartyParameters(),
				'tx-ncusefulpages-pi1'
			);
			return;
		}


		$this->validateIncomingRating($rating);

		// Page rating records are identified by page ID and a normalized string of all GET parameters involved
		$normalizedParameterString = $this->getNormalizedParameterString($this->get3rdPartyParameters());

		// Find the page in the repository and update it. Create a new one if none matches.

		/** @var Page $page */
		$page = $this->pageRepository->findOneByPageIDAndParameters(
			$pageID,
			$normalizedParameterString
		);

		if(is_null($page)) {

			// Create a new page record.
			$page = new Page();
			$page->setPageID($pageID)
				->setPageURL($pageURL)
				->setPageTitle($pageTitle)
				->setPageParameters($normalizedParameterString)
				->updateRating($rating);

			$this->pageRepository->add($page);

			// Need to persist the newly created page to assign it a UID.
			// Otherwise Tx_Extbase_MVC_Web_Routing_UriBuilder::convertDomainObjectsToIdentityArrays() called through
			// $this->redirectWith3rdPartyParameters() with $page as argument will fail:
			// 		#1260881688: Could not serialize Domain Object \Netcreators\NcUsefulpages\Domain\Model\Page.
			// 		It is neither an Entity with identity properties set, nor a Value Object.

			/** @var PersistenceManager $persistenceManager */
			$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
			$persistenceManager->persistAll();

		} else {

			// Update the existing page.
			$page->updateRating($rating);
			$this->pageRepository->update($page);
		}

		$this->redirectToConfirmationPageIfConfigured($rating);

		$this->redirectWith3rdPartyParameters(
			'rated',
			[
				'page' => $page,
				'rating' => $rating
            ],
			$this->get3rdPartyParameters(),
			'tx-ncusefulpages-pi1'
		);
	}


	/**
	 * Rated action.
	 *
	 * @param Page $page
	 *
	 * @return void
	 */
	public function ratedAction(Page $page) {

		// Sanitize and validate incoming value.
		try {
			$rating = $this->sanitizeIncoming('rating', 'integer');

		} catch(MissingControllerActionArgumentError $exception) {

			$this->addFlashMessage(
				$exception->getMessage(),
				'Missing Controller Action Argument',
				FlashMessage::ERROR
			);

			/** @throws StopActionException */
			$this->redirectWith3rdPartyParameters(
				'index',
				[],
				$this->get3rdPartyParameters(),
				'tx-ncusefulpages-pi1'
			);

			// This return is never called.
			// It is just here for code flow clarity.
			// Also, it stops PHPStorm from complaining below about $rating
			// *possibly* not having been defined yet.
			return;
		}


		$this->validateIncomingRating($rating);

		$this->assignTemplateToggleByRating('showCommentForm', $rating);

		$this->assignTemplateToggleByRating('showContactLink', $rating);

		$this->view
			->assign('page', $page)
			->assign('rating', $rating)
			->assign('ratingAsLowerCamelCaseString', $this->getRatingAsString($rating, FALSE))
			->assign('thirdPartyParameters', $this->get3rdPartyParameters());
	}

	
	/**
	 * Gets the current page URL.
	 *
	 * @return string
	 */
	protected function getPageURL() {
		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
		
		$pageURL = $protocol. '://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		return $pageURL;
	}


	/**
	 * Redirects to the 'rated useful' / 'rated not useful' page, if such pages are configured.
	 *
	 * @param integer $rating
	 *
	 * @return void
	 */
	protected function redirectToConfirmationPageIfConfigured($rating) {

		$redirectToPID = (int)$this->getTypoScriptSettingByRating('redirectToPid', $rating);

		if($redirectToPID) {
			$this->redirectToURI("/?id=" . $redirectToPID);
		}
	}


	/**
	 * Validates the incoming rating Controller Action argument.
	 *
	 * @param integer	$rating
	 *
	 * @return void
	 */
	protected function validateIncomingRating($rating) {

		try {

			$this->validateIncomingInSet('rating', $rating, [
				self::RATING_USEFUL,
				self::RATING_NOT_USEFUL,
				self::RATING_UNDECIDED
            ]
            );

		} catch	(InvalidControllerActionArgumentError $exception) {

			$this->addFlashMessage(
				$exception->getMessage(),
				'Invalid Controller Action Argument',
				FlashMessage::ERROR
			);

			$this->redirectWith3rdPartyParameters(
				'index',
				[],
				$this->get3rdPartyParameters(),
				'tx-ncusefulpages-pi1'
			);
		}

	}

}

