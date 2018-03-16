<?php
/***************************************************************
 *  Copyright notice
 *
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

namespace Netcreators\NcUsefulpages\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * A page comment
 */
class Comment extends AbstractEntity {

	/**
	 * User ratings are defined as constants 1 through 3.
	 * @see \Netcreators\NcUsefulpages\Controller\AbstractController
	 *
	 * @var integer
	 * @validate NotEmpty, NumberRange(minimum=1, maximum=3)
	 */
	protected $rating;

	/**
	 * @var string
	 * @validate NotEmpty
	 */
	protected $content;

	/**
	 * @var string
	 * @validate StringLength(maximum=255)
	 */
	protected $authorName;

	/**
	 * @var string
	 * @validate StringLength(maximum=255)
	 */
	protected $authorEmail;

	/**
	 * Note: TYPO3 requires the fully qualified name of \Netcreators\NcUsefulpages\Domain\Model\Page
	 * for its persistence layer!
	 *
	 * @var \Netcreators\NcUsefulpages\Domain\Model\Page
	 */
	protected $page;

	/**
	 * Sets the commenter's page rating for this comment
	 *
	 * @param integer $rating
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setRating($rating) {
		$this->rating = $rating;

		return $this;
	}

	/**
	 * Getter for rating
	 *
	 * @return integer
	 */
	public function getRating() {
		return $this->rating;
	}

	/**
	 * Sets the content for this comment
	 *
	 * @param string $content
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setContent($content) {
		$this->content = $content;

		return $this;
	}

	/**
	 * Getter for content
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Sets this comment's author's name
	 *
	 * @param string $authorName
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setAuthorName($authorName) {
		$this->authorName = $authorName;

		return $this;
	}

	/**
	 * Getter for authorName
	 *
	 * @return string
	 */
	public function getAuthorName() {
		return $this->authorName;
	}

	/**
	 * Sets this comment's author's e-mail address
	 *
	 * @param string $authorEmail
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setAuthorEmail($authorEmail) {
		$this->authorEmail = $authorEmail;

		return $this;
	}

	/**
	 * Getter for authorEmail
	 *
	 * @return string
	 */
	public function getAuthorEmail() {
		return $this->authorEmail;
	}

	/**
	 * Setter for page
	 *
	 * @param Page $page
	 *
	 * @return self Returning itself for method call chaining.
	 */
	public function setPage(Page $page=NULL) {
		$this->page = $page;

		return $this;
	}

	/**
	 * Getter for page
	 *
	 * @return Page
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Returns this comment as a formatted string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->content;
	}
}

