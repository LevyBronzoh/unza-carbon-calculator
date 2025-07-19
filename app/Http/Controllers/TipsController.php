<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Illuminate\Http\Request;

class TipsController extends Controller
{
    /**
     * Display a listing of clean cooking tips.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tips = [
            [
                'title' => 'Improving Charcoal Stove Efficiency',
                'content' => 'Keep your charcoal stove clean and free of ash buildup. Use a pot that fits the stove properly to minimize heat loss.',
                'category' => 'stove-efficiency'
            ],
            [
                'title' => 'Reducing Fuel Consumption',
                'content' => 'Soak beans and hard grains before cooking to reduce cooking time. Use lids on pots to retain heat and cook faster.',
                'category' => 'fuel-saving'
            ],
            [
                'title' => 'Safe Cooking Practices',
                'content' => 'Always cook in well-ventilated areas. Keep children away from hot stoves and store fuels safely.',
                'category' => 'safety'
            ],
            [
                'title' => 'Maintaining LPG Stoves',
                'content' => 'Regularly check for gas leaks with soapy water. Clean burner heads to ensure even flame distribution.',
                'category' => 'lpg-tips'
            ],
            [
                'title' => 'Electric Stove Savings',
                'content' => 'Use flat-bottomed pans that make full contact with the heating element. Turn off slightly before food is done to use residual heat.',
                'category' => 'electric-tips'
            ]
        ];

        return view('tips.index', [
            'tips' => $tips,
            'categories' => array_unique(array_column($tips, 'category'))
        ]);
    }

    /**
     * Display tips filtered by category.
     *
     * @param  string  $category
     * @return \Illuminate\Http\Response
     */
    public function category($category)
    {
        $allTips = [
            // Same array as in index()
        ];

        $filteredTips = array_filter($allTips, function($tip) use ($category) {
            return $tip['category'] === $category;
        });

        return view('tips.category', [
            'tips' => $filteredTips,
            'category' => $category
        ]);
    }
}
