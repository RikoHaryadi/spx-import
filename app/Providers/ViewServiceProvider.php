<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Hub;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(
            'layouts.app',
            function ($view) {

                $view->with(
                    'contextHubs',
                    Hub::where('is_active',1)
                        ->orderBy('hub_name')
                        ->get()
                );

            }
        );
    }
}