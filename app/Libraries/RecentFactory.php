<?php

namespace App\Libraries;

use App\Models\Recent;

//using a factory here as this has some logic around deleting older recents
class RecentFactory
{
    protected $userId;

    protected $entityType;

    protected $entityId;

    public function __construct($userId, $entityType, $entityId)
    {
        $this->userId = $userId;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }

    public function create()
    {
        if (! $this->sameAsMostRecentOfType()) {
            $recent = new Recent();
            $recent->user_id = $this->userId;
            $recent->entity_type = $this->entityType;
            $recent->entity_id = $this->entityId;
            $recent->save();
        }

        $this->deleteOlderRecents();
    }

    protected function sameAsMostRecentOfType()
    {
        //get the most recent of type
        $recent = Recent::where('user_id', $this->userId)
        ->where('entity_type', $this->entityType)
        ->orderBy('created_at', 'desc')
        ->first();

        if (! $recent) {
            return false;
        }

        //is it the same as the one we're about to make?
        if ($recent->entity_id == $this->entityId) {
            return true;
        }

        return false;
    }

    //this is incredibly clunky because skip() kept causing db errors. refactor sometime please
    protected function deleteOlderRecents()
    {
        $recents = Recent::where('user_id', $this->userId)
            ->where('entity_type', $this->entityType)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($recents->count() <= 5) {
            return;
        }

        $recents = Recent::where('user_id', $this->userId)
            ->where('entity_type', $this->entityType)
            ->orderBy('created_at', 'desc')
            ->skip(5)
            ->take(10)
            ->get();

        $recents->each(function ($recent) {
            $recent->delete();
        });
    }
}
