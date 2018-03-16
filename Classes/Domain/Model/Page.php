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

namespace Netcreators\NcUsefulpages\Domain\Model;

use Netcreators\NcUsefulpages\Controller\PageController;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Page Object
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Page extends AbstractEntity {
	
	/**
	 * ID of the page being rated
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $pageID;
	
	/**
	 * Title of the page being rated
	 * @var string
	 * @validate NotEmpty
	 */
	protected $pageTitle;
	
	/**
	 * URL of the page being rated
	 * @var string
	 * @validate NotEmpty
	 */
	protected $pageURL;

	/**
	 * @var string
	 */
	protected $pageParameters;
	
	/**
	 * useful
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $useful;
	
	/**
	 * notuseful
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $notuseful;

	/**
	 * undecided
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $undecided;

	/**
	 * Note: TYPO3 requires the fully qualified name of both \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 * as well as \Netcreators\NcUsefulpages\Domain\Model\Comment for its persistence layer!
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Netcreators\NcUsefulpages\Domain\Model\Comment>
	 * @lazy
	 * @cascade remove
	 */
	protected $comments;

	/**
	 * Constructs this page
	 */
	public function initializeObject() {
		$this->comments = new ObjectStorage();
	}

	/**
	 * Update this page's rating
	 *
	 * @param integer $rating The user rating
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function updateRating($rating) {
		switch($rating) {
			case PageController::RATING_USEFUL:
				$this->setUseful($this->getUseful() + 1);
				break;

			case PageController::RATING_NOT_USEFUL:
				$this->setNotuseful($this->getNotuseful() + 1);
				break;

			case PageController::RATING_UNDECIDED:
				$this->setUndecided($this->getUndecided() + 1);
				break;
		}

		return $this;
	}
	
	/**
	 * Setter for pageID
	 *
	 * @param integer $pageID ID of the page being rated
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setPageID($pageID) {
		$this->pageID = $pageID;

		return $this;
	}

	/**
	 * Getter for pageID
	 *
	 * @return integer ID of the page being rated
	 */
	public function getPageID() {
		return $this->pageID;
	}
	
	/**
	 * Setter for pageTitle
	 *
	 * @param string $pageTitle Title of the page being rated
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setPageTitle($pageTitle) {
		$this->pageTitle = $pageTitle;

		return $this;
	}

	/**
	 * Getter for pageTitle
	 *
	 * @return string Title of the page being rated
	 */
	public function getPageTitle() {
		return $this->pageTitle;
	}
	
	/**
	 * Setter for pageURL
	 *
	 * @param string $pageURL URL of the page being rated
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setPageURL($pageURL) {
		$this->pageURL = $pageURL;

		return $this;
	}

	/**
	 * Getter for pageURL
	 *
	 * @return string URL of the page being rated
	 */
	public function getPageURL() {
		return $this->pageURL;
	}

	/**
	 * Setter for pageParameters
	 *
	 * @param string $pageParameters Request parameters of the page being rated
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setPageParameters($pageParameters) {
		$this->pageParameters = $pageParameters;

		return $this;
	}

	/**
	 * Getter for pageParameters
	 *
	 * @return string Parameters of the page being rated
	 */
	public function getPageParameters() {
		return $this->pageParameters;
	}
	
	/**
	 * Setter for useful
	 *
	 * @param integer $useful useful
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setUseful($useful) {
		$this->useful = $useful;

		return $this;
	}

	/**
	 * Getter for useful
	 *
	 * @return integer useful
	 */
	public function getUseful() {
		return $this->useful;
	}
	
	/**
	 * Setter for notuseful
	 *
	 * @param integer $notuseful notuseful
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setNotuseful($notuseful) {
		$this->notuseful = $notuseful;

		return $this;
	}

	/**
	 * Getter for notuseful
	 *
	 * @return integer notuseful
	 */
	public function getNotuseful() {
		return $this->notuseful;
	}

	/**
	 * Setter for undecided
	 *
	 * @param integer $undecided undecided
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setUndecided($undecided) {
		$this->undecided = $undecided;

		return $this;
	}

	/**
	 * Getter for undecided
	 *
	 * @return integer undecided
	 */
	public function getUndecided() {
		return $this->undecided;
	}

	/**
	 * Adds a comment to this post
	 *
	 * @param Comment $comment
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function addComment(Comment $comment) {
		$this->comments->attach($comment);

		return $this;
	}
}

