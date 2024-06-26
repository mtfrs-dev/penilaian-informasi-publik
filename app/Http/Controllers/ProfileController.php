<?php

namespace App\Http\Controllers;

use App\Models\WorkUnit;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\WorkUnitUpdateRequest;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();
        
        if(isset($request->profile_picture)) {
            if($user->profile_picture){
                if( Storage::exists($user->profile_picture)){
                    Storage::delete($user->profile_picture);
                }
            }
            $storeFile = $request->file('profile_picture')->store('ID-Cards');
            $validated["profile_picture"] = $storeFile;
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Data Pribadi Responden berhasil diperbarui!');
    }

    public function updateWorkUnit(WorkUnitUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $work_unit = WorkUnit::findOrFail($request->user()->work_unit_id);

        $work_unit->fill($validated);

        if ($validated['email'] !== $work_unit->email) {
            $work_unit->email_verified_at = null;
        }

        $work_unit->save();

        return Redirect::route('profile.edit')->with('success', 'Data Unit Kerja berhasil diperbarui!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
