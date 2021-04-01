<?php

namespace Sunnysideup\AvoidChildDeletion\Extensions;

use SilverStripe\Forms\FieldList;
use \Page;


class AvoidChildDeletionExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        if($this->hasChildrenOrIsTooImportant())
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
                )
            ]
        );
        return $fields;
    }

    public function canDelete($member = null)
    {
        if ($this->hasChildrenOrIsTooImportant()) {
            return false;
        }
        // no return to let normal return take its course ...
    }

    protected function hasChildrenOrIsTooImportant() : bool
    {
        if (
            $this->owner->URLSegment === 'home'
            ||
            SiteTree::get()->filter('ParentID', $this->owner->ID)->count() > 0
        ) {
            return true;
        }
        return false;

    }
}
