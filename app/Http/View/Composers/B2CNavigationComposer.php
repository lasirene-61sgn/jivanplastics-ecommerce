<?php

namespace App\Http\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class B2CNavigationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $categories = Category::with([
            'subcategories' => function ($query) {
                $query->where('is_active', true);
            }
        ])->where('is_active', true)->get();

        $view->with('navCategories', $categories);
    }
}
