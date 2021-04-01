<?php

namespace Sunnysideup\AvoidChildDeletion\Extensions;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;

class AvoidChildDeletionExtension extends SiteTreeExtension
{
    public function updateCMSActions(FieldList $fields)
    {
        if ($this->hasChildrenOrIsTooImportant()) {
            $fields->addFieldsToTab(
                'ActionMenus.MoreOptions',
                [
                    LiteralField::create(
                        'ArchiveNote',
                        '
                        <div class=\'cms-sitetree-information\'>
                        	<p class="meta-info" style="white-space: normal;">
                                This page can not be deleted because it has children or it is the home page.
                                To delete this page, you need to either move / delete its child pages or
                                carefully consider if you really want to delete your home page.
                        	</p>
                        </div>'
                    ),
                ]
            );
        }
    }

    public function canDelete($member = null)
    {
        return $this->canArchive($member);
    }

    public function canArchive($member = null)
    {
        if ($this->hasChildrenOrIsTooImportant()) {
            return false;
        }
        // no return to let normal return take its course ...
    }

    protected function hasChildrenOrIsTooImportant(): bool
    {
        $isHomePage = $this->owner->URLSegment === RootURLController::get_homepage_link();
        $haschildren = SiteTree::get()->filter('ParentID', $this->owner->ID)->count() > 0;
        return $isHomePage || $haschildren;
    }
}
