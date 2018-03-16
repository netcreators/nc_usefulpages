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

namespace Netcreators\NcUsefulpages\Controller;

use Netcreators\NcUsefulpages\Domain\Model\Comment;
use Netcreators\NcUsefulpages\Domain\Model\Page;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The comment controller for the NcUsefulpages extension
 */
class CommentController extends AbstractController {


	/**
	 * Note: TYPO3 requires the fully qualified name for automatic injection!
	 *
	 * @var \Netcreators\NcUsefulpages\Domain\Repository\PageRepository
	 * @inject
	 */
	protected $pageRepository;


	/**
	 * Adds a comment to a page rating and sends out an email if a recipient is configured.
	 *
	 * @param Page $page The page the comment is related to
	 * @param Comment $newComment The comment to create
	 * @return void
	 */
	public function createAction(Page $page, Comment $newComment = null) {

		// Add Comment to Page
		if($newComment != null) {
			$page->addComment($newComment);

			// This should not be required, but it is, since we use getPage before committing the page to the database.
			// Extbase does not update the values on both ends of the relation.
			$newComment->setPage($page);

			// Make sure the modified Page is stored with the newly added comment.
			$this->pageRepository->update($page);

			// Send the Comment by e-mail
			$this->sendEmail($newComment);

			$this->addFlashMessageForAction('created');

			$this->view
				->assign('showContactLink', false)
				->assign('page', $newComment->getPage());
			}
	}

	/**
	 * Sends the comment as e-mail if a recipient is defined.
	 *
	 * @param Comment $comment
	 */
	protected function sendEmail(Comment $comment) {
		if(!$this->settings['Comment']['create']['email']['recipientEmail']) {
			return;
		}

		if(!GeneralUtility::validEmail($this->settings['Comment']['create']['email']['recipientEmail'])) {
			return;
		}

		$ratingAsLocalizedString = $this->translate('Rating.' . $this->getRatingAsString($comment->getRating(), FALSE));


		/** @var MailMessage $mailer */
        $mailer = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');

	    $mailer->setSubject('[' . $ratingAsLocalizedString . ']' . $comment->getPage()->getPageTitle());

        // Set sender e-mail and - if set - sender name as entered by the user.
		// Note:
		// - If the user entered an e-mail address but no name, we do NOT want to use the default sender name.
		// - If the user entered a name but no e-mail address, we do NOT want to use this name to label the
		//   default email address: Most e-mail clients would only display the name but not the e-mail address.
		//   Therefore it would not be obvious that a 'noreply@domain.tld' e-mail address would be hidden behind it.
		$authorName = trim($comment->getAuthorName());
		$authorEmail = trim($comment->getAuthorEmail());

		if($authorEmail && GeneralUtility::validEmail($authorEmail)) {

			// $mailer->from_email = $authorEmail;

			if($authorName) {
				// $mailer->from_name = $authorName;
                $mailer->setFrom($authorEmail, $authorName);
			}else{
                $mailer->setFrom($authorEmail);
            }

		} else {

			// We (Netcreators) should rather be setting $TYPO3_CONF_VARS['MAIL']['defaultMailFromAddress'] and
			// $TYPO3_CONF_VARS['MAIL']['defaultMailFromName'] and leave $mailer->from_* empty.
			// We do not seem to be setting these default values, though.
			// $mailer->from_email = $this->settings['Comment']['create']['email']['defaultSenderEmail'];
			// $mailer->from_name = $this->settings['Comment']['create']['email']['defaultSenderName'];

            $mailer->setFrom($this->settings['Comment']['create']['email']['defaultSenderEmail'], $this->settings['Comment']['create']['email']['defaultSenderName']);

		}


        $mailer->addPart(implode("\n",
            [

                // Metadata for quick and easy checking of the user's claims.
                'URL: ' . $comment->getPage()->getPageURL(),
                '',
                $this->translate('Rating') . ': ' . $ratingAsLocalizedString,
                '',
                $this->translate('Comment.form.authorName')  . ': ' . $authorName,
                $this->translate('Comment.form.authorEmail') . ': ' . $authorEmail,
                '',

                // The user's complaints.
                $comment->getContent()
            ]
        ));

		if($this->settings['Comment']['create']['email']['debugMode']) {

			// FIXME: Shall we replace or remove this?
			// echo 'E-Mail from: ' . $mailer->from_name . ' &lt;' . $mailer->from_email . '&gt;: ';
			// $mailer->setContent();
			// $mailer->preview();
			return;

		}

        $mailer->setTo($this->settings['Comment']['create']['email']['recipientEmail']);
        $mailer->send();
	}
}

