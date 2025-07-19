<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmissionsCalculator;
use App\Models\BaselineData;
use App\Models\ProjectData;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * The emissions calculator service instance.
     */
    protected $emissionsCalculator;

    /**
     * Create a new controller instance.
     *
     * @param  EmissionsCalculator  $emissionsCalculator
     * @return void
     */
    public function __construct(EmissionsCalculator $emissionsCalculator)
    {
        $this->middleware('auth');
        $this->emissionsCalculator = $emissionsCalculator;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's baseline and project data if they exist
        $baseline = BaselineData::where('user_id', $user->id)->first();
        $project = ProjectData::where('user_id', $user->id)->first();

        // Calculate emissions data if both baseline and project exist
        $emissionsData = null;
        if ($baseline && $project) {
            $emissionsData = $this->emissionsCalculator->calculateComprehensiveEmissions(
                [
                    'daily_fuel_use' => $baseline->daily_fuel_use,
                    'fuel_type' => $baseline->fuel_type,
                    'efficiency' => $baseline->efficiency
                ],
                [
                    'fuel_use_project' => $project->fuel_use_project,
                    'new_fuel_type' => $project->new_fuel_type,
                    'new_efficiency' => $project->new_efficiency
                ]
            );
        }

        return view('home', [
            'user' => $user,
            'baseline' => $baseline,
            'project' => $project,
            'emissionsData' => $emissionsData,
            'hasData' => $baseline || $project
        ]);
    }

    /**
     * Show the application welcome page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        // Show welcome page only to guests
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('welcome');
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        return view('about', [
            'emissionFactors' => $this->emissionsCalculator->getEmissionFactors()
        ]);
    }

    /**
     * Show the methodology page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function methodology()
    {
        return view('methodology');
    }

    /**
     * Show the contact page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('contact');
    }
    public function contactSubmit(Request $request)
    {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string'
    ]);

    // Process the contact form (send email, save to database, etc.)

    return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }

}
