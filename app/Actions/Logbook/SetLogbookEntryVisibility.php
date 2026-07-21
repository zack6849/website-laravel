<?php

declare(strict_types=1);

namespace App\Actions\Logbook;

use App\Models\LogbookEntry;
use App\Models\LogbookEntryVisibilityOverride;
use App\Services\LogbookEntryIdentity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SetLogbookEntryVisibility
{
    public function __construct(
        private readonly LogbookEntryIdentity $identity,
    ) {
    }

    public function hide(LogbookEntry $entry): void
    {
        $this->setHidden($entry, true);
    }

    public function show(LogbookEntry $entry): void
    {
        $this->setHidden($entry, false);
    }

    private function setHidden(LogbookEntry $entry, bool $hidden): void
    {
        DB::transaction(function () use ($entry, $hidden): void {
            if ($entry->entry_key === null || $entry->entry_key === '') {
                $entry->entry_key = $this->identity->forEntry($entry);
            }

            $entry->hidden_from_public = $hidden;
            $entry->save();

            if ($hidden) {
                $this->rememberOverride($entry);

                return;
            }

            $this->forgetOverride($entry);
        });
    }

    /**
     * Persist the override so the nightly re-import (which wipes and rebuilds
     * logbook_entries) can re-apply the hidden flag to the recreated row.
     */
    private function rememberOverride(LogbookEntry $entry): void
    {
        $lookup = $entry->qrz_logid !== null
            ? ['qrz_logid' => $entry->qrz_logid]
            : ['entry_key' => $entry->entry_key];

        LogbookEntryVisibilityOverride::query()->updateOrCreate(
            $lookup,
            [
                'qrz_logid' => $entry->qrz_logid,
                'hidden_from_public' => true,
                'entry_key' => $entry->entry_key,
            ],
        );
    }

    private function forgetOverride(LogbookEntry $entry): void
    {
        LogbookEntryVisibilityOverride::query()
            ->where('entry_key', $entry->entry_key)
            ->when($entry->qrz_logid !== null, function (Builder $query) use ($entry): void {
                $query->orWhere('qrz_logid', $entry->qrz_logid);
            })
            ->delete();
    }
}
