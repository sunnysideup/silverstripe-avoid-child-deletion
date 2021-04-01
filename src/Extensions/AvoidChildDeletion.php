<?php

namespace Sunnysideup\AvoidChildDeletion\Extensions;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;

use SilverStripe\CMS\Model\SiteTreeExtension;

class AvoidChildDeletionExtension extends SiteTreeExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->hasChildrenOrIsTooImportant()) {
            $fields->addFieldsToTab(
                'Root.Archive',
                [
                    LiteralField::create(
                        'ArchiveNote',
                        '<p>
                            This page can not be deleted because it has children or it is the home page.
                            To delete this page, you need to either move / delete its child pages or
                            carefully consider if you really want to delete your home page.
                        </p>'
                    ),
                ]
            );
        }
    }

    public function canDelete($member = null)
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
