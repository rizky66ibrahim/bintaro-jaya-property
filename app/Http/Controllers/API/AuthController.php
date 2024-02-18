<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // ! Private Function to handle phone number
    private function handlePhoneNumber($phoneNumber)
    {
        // * Handle Phone Number must start with 62
        if (substr($phoneNumber, 0, 2) === '08') {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }

// ! Private Function to Generate Profile Picture
    private function generateProfilePicture($name)
    {
        // * Generate Profile Picture by Name
        $words = explode(' ', $name);
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));

// * List of colors
        $colors = [
            '#ff80ed', '#065535', '#0000ff', '#00ffff', '#ffa500',
            '#ff0000', '#008080', '#ffff00', '#800080', '#00ff00',
            '#cc0000', '#ff00ff', '#ccff00', '#000080', '#800000',
        ];
        // * Background color
        $bgColor = $colors[array_rand($colors)];

        // * Text color
        $textColor = '#ffffff';

        // * Create a blank image
        $image = imagecreatetruecolor(200, 200);
        $bg = imagecolorallocate($image, hexdec(substr($bgColor, 1, 2)), hexdec(substr($bgColor, 3, 2)), hexdec(substr($bgColor, 5, 2)));
        $text = imagecolorallocate($image, hexdec(substr($textColor, 1, 2)), hexdec(substr($textColor, 3, 2)), hexdec(substr($textColor, 5, 2)));
        imagefill($image, 0, 0, $bg);

        // * Add the text
        $font_size = 75;
        $text_box = imagettfbbox($font_size, 0, public_path('fonts/arial.ttf'), $initials);
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[1] - $text_box[7];
        $x = (200 - $text_width) / 2;
        $y = (200 - $text_height) / 2 + $text_height;
        imagettftext($image, $font_size, 0, $x, $y, $text, public_path('fonts/arial.ttf'), $initials);

        // * Save the image to a file
        $avatarPath = public_path('storage/profile_pictures/') . time() . '.png';
        if (!file_exists(public_path('storage/profile_pictures/'))) {
            mkdir(public_path('storage/profile_pictures/'), 0755, true);
        }
        imagepng($image, $avatarPath);
        imagedestroy($image);

        return asset(str_replace(public_path(), '', $avatarPath));
    }

    // ! Private Function to handle upload profile picture
    private function handleUploadProfilePicture($request)
    {
        // * Handle Profile Picture
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/profile_pictures'), $imageName);
            $profilePicturePath = 'storage/profile_pictures/' . $imageName;
        } else {
            $profilePicturePath = null;
        }

        return $profilePicturePath;
    }

    // ! Register
    public function register(Request $request)
    {
        // * Validate Request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:users,phone_number',
            'address' => 'sometimes|nullable|string',
            'subdistrict' => 'sometimes|nullable|string',
            'district' => 'sometimes|nullable|string',
            'city' => 'sometimes|nullable|string',
            'province' => 'sometimes|nullable|string',
            'postal_code' => 'sometimes|nullable|string',
            'profile_picture' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gender' => 'sometimes|nullable',
            'date_of_birth' => 'sometimes|nullable|date',
            'place_of_birth' => 'sometimes|nullable|string',
            'password' => 'required|string|confirmed',
        ]);

        // * If Validator Fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }
        // * Handle Phone Number must start with 62
        $phoneNumber = $this->handlePhoneNumber($request->input('phone_number'));

        // * Handle Upload Profile Picture
        $profilePicturePath = $this->handleUploadProfilePicture($request);

        // * If No Profile Picture Uploaded, Generate Profile Picture by Name
        if (!$profilePicturePath) {
            $profilePicturePath = $this->generateProfilePicture($request->input('name'));
        }

        // * Create User
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $phoneNumber,
            'address' => $request->address,
            'subdistrict' => $request->subdistrict,
            'district' => $request->district,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'profile_picture' => $profilePicturePath,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'place_of_birth' => $request->place_of_birth,
            'password' => Hash::make($request->password),
        ]);

        // * Return Response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
        ], 201);
    }

    // ! Login
    public function login(Request $request)
    {
        // * Validate Request
        $validator = Validator::make($request->all(), [
            'credential' => 'required|string',
            'password' => 'required|string',
        ]);

        // * If Validator Fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // * Credentials to be used for login
        $credentials = $request->only('credential', 'password');

        // * Check if the credential is a phone number and starts with 0
        if (preg_match('/^0/', $credentials['credential'])) {
            // Ubah nomor telepon yang dimulai dengan 0 menjadi 62
            $credentials['credential'] = '62' . substr($credentials['credential'], 1);
        }

        // * Attempt to login with username, email, or phone number
        if (Auth::attempt(['username' => $credentials['credential'], 'password' => $credentials['password']])
            || Auth::attempt(['email' => $credentials['credential'], 'password' => $credentials['password']])
            || Auth::attempt(['phone_number' => $credentials['credential'], 'password' => $credentials['password']])
        ) {
            // * If login success
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'User authenticated successfully',
                'token' => $token,
                'user' => $user,
            ], 200);
        } else {
            // * If login fails
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
    }

    // ! Logout
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // ! Get User
    public function user(Request $request)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'user' => $request->user(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
