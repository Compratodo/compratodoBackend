<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller {

    //  METODO REGISTER
    public function register(Request $request) {
    
        $request->validate([
            'store_name' => 'required|string|unique:sellers,store_name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image',
            'banner' => 'nullable|image',
        ]);
    
        $user = $request->user();
    
        // Evitar duplicar vendedor por usuario
        if ($user->seller) {
            return response()->json([
                'success' => false,
                'message' => 'Ya estás registrado como vendedor'
            ], 400);
        }

        // Guardar archivos si existen
        $logoPath = $request->hasFile('logo') 
            ? $request->file('logo')->store('logos', 'public') 
            : null;

        $bannerPath = $request->hasFile('banner') 
            ? $request->file('banner')->store('banners', 'public') 
            : null;
    
        $seller = Seller::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'slug' => Str::slug($request->store_name),
            'description' => $request->description,
            'logo' => $logoPath,
            'banner' => $bannerPath,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Vendedor registrado correctamente',
            'seller' => $seller
        ], 201);
    }


    //  METODO PROFILE
    public function profile(Request $request) {

        $seller = $request->user()->seller;

        if (!$seller) {
            return response()->json(['message' => 'No registrado como vendedor'], 404);
        }

        $seller->logo = $seller->logo ? asset('storage/' . $seller->logo) : null;
        $seller->banner = $seller->banner ? asset('storage/' . $seller->banner) : null;

        return response()->json([
            'success' => true,
            'seller' => $seller
        ]);
    }

    //   METODO UPDATE
    public function update(Request $request) {

        $seller = $request->user()->seller;

        if (!$seller) {
            return response()->json(['message' => 'No registrado como vendedor'], 404);
        }

        $request->validate([
            'store_name' => 'sometimes|string|unique:sellers,store_name,' . $seller->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image',
            'banner' => 'nullable|image',
        ]);

        // Si se sube un nuevo logo
        if ($request->hasFile('logo')) {
            $seller->logo = $request->file('logo')->store('logos', 'public');
        }

        // Si se sube un nuevo banner
        if ($request->hasFile('banner')) {
            $seller->banner = $request->file('banner')->store('banners', 'public');
        }

        $seller->update([
            'store_name' => $request->store_name ?? $seller->store_name,
            'slug' => $request->store_name ? Str::slug($request->store_name) : $seller->slug,
            'description' => $request->description ?? $seller->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Información de vendedor actualizada',
            'seller' => $seller
        ]);
    }


}

