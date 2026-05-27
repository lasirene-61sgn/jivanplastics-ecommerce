<?php

namespace App\Http\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class B2BNavigationComposer
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
            'subSubcategories' => function ($query) {
                $query->where('sub_subcategories.is_active', true);
            },
            'subcategories' => function ($query) {
                $query->where('is_active', true);
            }
        ])->where('is_active', true)->get();

        $view->with('navCategories', $categories);

        if (auth()->check()) {
            $view->with('customer', auth()->user()->customer);
        }
    }
}
