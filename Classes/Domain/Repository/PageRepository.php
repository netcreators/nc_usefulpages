<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Carmen Popoviciu <extensions@netcreators.nl>, Netcreators
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

namespace Netcreators\NcUsefulpages\Domain\Repository;

use Netcreators\NcUsefulpages\Domain\Model\Page;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for \Netcreators\NcUsefulpages\Domain\Model\Page
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageRepository extends Repository {

	/**
	 * Find one Page by PageID and Parameters.
	 *
	 * @param integer	$pageID
	 * @param string	$normalizedParameterString
	 *
	 * @return Page|null
	 */
	public function findOneByPageIDAndParameters($pageID, $normalizedParameterString) {

		$query = $this->createQuery();
		$page = $query->matching(
			$query->logicalAnd(
				$query->equals('pageID', $pageID),
				$query->equals('pageParameters', $normalizedParameterString)
			)
		)
		->setLimit(1)
		->execute()
		->getFirst();
		return $page;
	}
}

