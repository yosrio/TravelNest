<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;
use App\Http\Requests\EditProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileController extends \App\Http\Controllers\Controller
{
    /**
     * Return the authenticated user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'bio']);

        return response()->json($user->only(['id', 'name', 'email', 'role', 'profile_photo', 'bio']));
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @param  \App\Http\Requests\EditProfile  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditProfile $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'bio']);

        if ($request->has('profile_photo')) {
            $base64_image = $request->input('profile_photo');

            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                $image_data = substr($base64_image, strpos($base64_image, ',') + 1);
                $image_data = base64_decode($image_data);

                $extension = strtolower($type[1]);

                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    return response()->json(['error' => 'Unsupported image type.'], 422);
                }

                $fileName = 'profile_' . Str::random(10) . '.' . $extension;
                $filePath = 'profile_photos/' . $fileName;

                Storage::disk('public')->put($filePath, $image_data);

                $data['profile_photo'] = Storage::url($filePath);
            } else {
                return response()->json(['error' => 'Invalid base64 image format.'], 422);
            }
        }

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $user->only(['name', 'profile_photo', 'bio']),
        ]);
    }
}
