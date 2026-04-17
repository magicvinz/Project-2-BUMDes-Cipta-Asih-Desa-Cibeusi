<?php

namespace App\Http\Controllers\Pengunjung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $totalTiketUsed = \App\Models\Tiket::where('id_user', $user->id)
                                ->where('status', 'used')
                                ->count();
        $totalTiketBeli = \App\Models\Tiket::where('id_user', $user->id)->count();
        
        $totalUlasan = \App\Models\Review::where('id_user', $user->id)->count();
        $reviews = \App\Models\Review::where('id_user', $user->id)->with('wisata')->latest()->take(5)->get();
        
        return view('pengunjung.profil', compact('user', 'totalTiketUsed', 'totalTiketBeli', 'totalUlasan', 'reviews'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'asal_kota' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'no_hp' => $validated['no_hp'],
            'asal_kota' => $validated['asal_kota'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists and it's not a default URL
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
