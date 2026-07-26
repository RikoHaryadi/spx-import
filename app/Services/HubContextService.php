<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class HubContextService
{
    public static function apply(Builder $query): Builder
    {
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Owner
        if ($user->role === 'owner') {

            if (session()->has('hub_context') && session('hub_context')) {

                $query->where(
                    'hub_id',
                    session('hub_context')
                );

            }

            return $query;
        }

        // Selain Owner
        return $query->where(
            'hub_id',
            $user->hub_id
        );
    }

    public static function currentHubId()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user->role === 'owner') {

            return session('hub_context');

        }

        return $user->hub_id;
    }

    public static function isAllHub()
    {
        return auth()->check()
            && auth()->user()->role === 'owner'
            && (!session()->has('hub_context') || empty(session('hub_context')));
    }

    public static function currentHubName(): string
{
    $user = auth()->user();

    if (!$user) {
        return '-';
    }

    // SPV selalu melihat hub sendiri
    if ($user->role !== 'owner') {
        return $user->hub?->hub_name ?? '-';
    }

    // Owner memilih hub tertentu
    if (session()->has('hub_context')) {

        $hub = \App\Models\Hub::find(
            session('hub_context')
        );

        return $hub?->hub_name ?? 'Semua Hub';
    }

    return 'Semua Hub';
}
}