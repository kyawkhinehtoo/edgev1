<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('emailTemplate')->get();
        $emailTemplates = EmailTemplate::all(['id', 'name']);
        return Inertia::render('Activities/Index', [
            'activities' => $activities,
            'emailTemplates' => $emailTemplates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:activities,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'notify_by_email' => 'boolean',
            'notify_by_sms' => 'boolean',
            'email_template_id' => 'nullable|exists:email_templates,id',
        ]);
        Activity::create($validated);
        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:activities,code,' . $activity->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'notify_by_email' => 'boolean',
            'notify_by_sms' => 'boolean',
            'email_template_id' => 'nullable|exists:email_templates,id',
        ]);
        $activity->update($validated);
        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }
}
