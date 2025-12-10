<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecommendationSetting;

class RecommendationSettingsController extends Controller
{
    public function edit()
    {
        $settings = RecommendationSetting::first();
        if (!$settings) {
            $settings = RecommendationSetting::create([
                'views_weight' => 1.0,
                'enrollments_weight' => 2.0,
                'activity_weight' => 1.0,
                'default_trending_days' => 7,
                'personalized_top_tags_limit' => 5,
            ]);
        }

        return view('admin.recommendations.settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'views_weight' => ['required','numeric','min:0'],
            'enrollments_weight' => ['required','numeric','min:0'],
            'activity_weight' => ['required','numeric','min:0'],
            'default_trending_days' => ['required','integer','min:1','max:365'],
            'personalized_top_tags_limit' => ['required','integer','min:1','max:50'],
        ]);

        $settings = RecommendationSetting::first();
        if (!$settings) {
            $settings = RecommendationSetting::create($data);
        } else {
            $settings->update($data);
        }

        return redirect()->route('admin.recommendations.settings')->with('status', 'Recommendation settings updated');
    }
}