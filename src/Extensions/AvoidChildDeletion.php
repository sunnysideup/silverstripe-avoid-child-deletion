<?php

namespace Sunnysideup\AvoidChildDeletion\Extensions;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Security\Member;

class AvoidChildDeletion extends SiteTreeExtension
{
    public function updateCMSActions(FieldList $fields)
    {
        if ($this->hasChildrenOrIsTooImportant()) {
            $phrase = _t(__CLASS__ . '.ReasonsThisPageCanNotBeDeleted', 'This page can not be archived because it has children or it is the home page.');
            $fields->addFieldsToTab(
                'ActionMenus.MoreOptions',
                [
                    LiteralField::create(
                        'ArchiveNote',
                        '
                        <div class=\'cms-sitetree-information\'>
                        	<p class="meta-info" style="white-space: normal;">' . $phrase . '</p>
                        </div>'
                    ),
                ]
            );
        }
    }

    /**
     *
     * @param  Member $member - optional
     */
    public function canDelete($member = null)
    {
        return $this->canArchive($member);
    }

    /**
     *
     * @param  Member $member - optional
     */
    public function canArchive($member = null)
    {
        return ! $this->hasChildrenOrIsTooImportant();
    }

    protected function hasChildrenOrIsTooImportant(): bool
    {
        $isHomePage = $this->owner->URLSegment === RootURLController::get_homepage_link();
        $haschildren = SiteTree::get()->filter('ParentID', $this->owner->ID)->count() > 0;

        return $isHomePage || $haschildren;
    }
}
