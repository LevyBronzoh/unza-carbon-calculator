<?php

namespace App\Http\Controllers;

use App\Models\User; // Imports the User model to interact with user data.
use Illuminate\Http\Request; // Imports the Request class to handle HTTP requests.
use Illuminate\Support\Facades\Auth; // Imports the Auth facade for user authentication.
use Illuminate\Support\Facades\Hash; // Imports the Hash facade for password hashing.
use Illuminate\Support\Facades\Validator; // Imports the Validator facade for manual validation.
use Illuminate\Validation\ValidationException; // Imports the ValidationException for throwing specific validation errors.
use Illuminate\Support\Facades\Log; // Imports the Log facade for logging errors.

/**
 * AuthController for UNZA Carbon Calculator
 *
 * This controller handles user authentication including registration, login, and logout.
 * Extends Laravel's Controller class (Polymorphism) - inherits controller functionality.
 *
 * @author Levy Bronzoh, Climate Yanga
 * @version 1.0
 * @since 2025-07-12
 *
 * @mixin \Illuminate\Foundation\Auth\Access\AuthorizesRequests // Helps Intelephense recognize the middleware() method.
 * @mixin \Illuminate\Foundation\Bus\DispatchesJobs // Helps Intelephense recognize dispatch() method.
 * @mixin \Illuminate\Foundation\Validation\ValidatesRequests // Helps Intelephense recognize validate() method.
 */
class AuthController extends Controller
{
    /**
     * Constructor method.
     * Sets up middleware for authentication (except for register and login routes).
     *
     * @return void
     */
    public function __construct()
    {
        // Applies 'auth:sanctum' middleware to all methods in this controller,
        // except for the 'register' and 'login' methods.
        // This ensures users must be authenticated via Sanctum token to access other methods.
        $this->middleware('auth:sanctum')->except(['register', 'login']);
    }

    /**
     * User registration method.
     * Creates a new user account with validation and returns authentication token.
     *
     * @param Request $request HTTP request containing user registration data.
     * @return \Illuminate\Http\JsonResponse JSON response with user data and token.
     */
    public function register(Request $request)
    {
        // Validate incoming request data.
        // Uses Laravel's validation system to ensure data integrity and proper format.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',                    // Full name required, max 255 chars.
            'email' => 'required|string|email|max:255|unique:users', // Email required, must be unique in 'users' table.
            'phone' => 'nullable|string|max:20',                    // Phone optional, max 20 chars.
            'password' => 'required|string|min:8|confirmed',        // Password min 8 chars, must match 'password_confirmation'.
            'user_type' => 'required|in:student,staff,faculty',     // Must be one of the specified user types.
            'location' => 'required|string|max:255',                // Location required, max 255 chars.
        ]);

        // If validation fails, return error response with specific validation errors.
        if ($validator->fails()) {
            Log::warning('User registration validation failed.', ['errors' => $validator->errors()->toArray(), 'request_data' => $request->except('password', 'password_confirmation')]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // HTTP 422 Unprocessable Entity.
        }

        try {
            // Create new user record in the database.
            // The 'password' field is explicitly hashed using Hash::make() for security.
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password), // Explicitly hash password for security.
                'user_type' => $request->user_type,
                'location' => $request->location,
            ]);

            // Create an authentication token for the new user using Laravel Sanctum.
            // 'auth_token' is the token name; plainTextToken retrieves the raw token string.
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response with selected user data and the generated token.
            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'location' => $user->location,
                    'display_name' => $user->display_name, // Uses accessor method from User model (if defined).
                ],
                'token' => $token,
            ], 201); // HTTP 201 Created.

        } catch (\Exception $e) {
            // Handle any unexpected errors during user creation and log them.
            Log::error('User registration failed: ' . $e->getMessage(), ['request_data' => $request->except('password', 'password_confirmation'), 'exception' => $e]);
            return response()->json([
                'message' => 'Registration failed',
                'error' => 'An error occurred while creating your account. Please try again.'
            ], 500); // HTTP 500 Internal Server Error.
        }
    }

    /**
     * User login method.
     * Authenticates user credentials and returns authentication token.
     *
     * @param Request $request HTTP request containing login credentials.
     * @return \Illuminate\Http\JsonResponse JSON response with user data and token.
     */
    public function login(Request $request)
    {
        // Validate incoming login request.
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',    // Email required and must be a valid email format.
            'password' => 'required|string', // Password required.
        ]);

        // If validation fails, return error response.
        if ($validator->fails()) {
            Log::warning('User login validation failed.', ['errors' => $validator->errors()->toArray(), 'request_email' => $request->email]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find user by email address.
            $user = User::where('email', $request->email)->first();

            // Check if user exists and if the provided password matches the hashed password in the database.
            if (!$user || !Hash::check($request->password, (string) $user->password)) {
                // If credentials are incorrect, throw a ValidationException.
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Create an authentication token for the successfully logged-in user.
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response with user data and the generated token.
            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'location' => $user->location,
                    'display_name' => $user->display_name,
                ],
                'token' => $token,
            ], 200); // HTTP 200 OK.

        } catch (ValidationException $e) {
            // Handle specific validation exceptions (e.g., invalid credentials).
            Log::info('Login attempt with invalid credentials.', ['email' => $request->email, 'ip_address' => $request->ip()]);
            return response()->json([
                'message' => 'Login failed',
                'errors' => $e->errors()
            ], 401); // HTTP 401 Unauthorized.

        } catch (\Exception $e) {
            // Handle any unexpected errors during login and log them.
            Log::error('Unexpected error during login: ' . $e->getMessage(), ['email' => $request->email, 'exception' => $e]);
            return response()->json([
                'message' => 'Login failed',
                'error' => 'An error occurred while logging in. Please try again.'
            ], 500); // HTTP 500 Internal Server Error.
        }
    }

    /**
     * User logout method.
     * Revokes the current authentication token.
     *
     * @param Request $request HTTP request containing authentication token.
     * @return \Illuminate\Http\JsonResponse JSON response confirming logout.
     */
    public function logout(Request $request)
    {
        try {
            // Delete the current access token associated with the authenticated user.
            // This effectively logs out the user by invalidating their token.
            $request->user()->currentAccessToken()->delete();

            // Return success response.
            return response()->json([
                'message' => 'Logged out successfully',
            ], 200); // HTTP 200 OK.

        } catch (\Exception $e) {
            // Handle any errors during logout and log them.
            Log::error('Error during logout: ' . $e->getMessage(), ['user_id' => Auth::id(), 'exception' => $e]);
            return response()->json([
                'message' => 'Logout failed',
                'error' => 'An error occurred while logging out.'
            ], 500); // HTTP 500 Internal Server Error.
        }
    }

    /**
     * Get current authenticated user information.
     * Returns the currently authenticated user's profile data.
     *
     * @param Request $request HTTP request containing authentication token.
     * @return \Illuminate\Http\JsonResponse JSON response with user data.
     */
    public function me(Request $request)
    {
        try {
            // Get the authenticated user from the request.
            $user = $request->user();

            // Return user profile data in a structured JSON response.
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'user_type' => $user->user_type,
                    'location' => $user->location,
                    'display_name' => $user->display_name,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'), // Format creation timestamp.
                ]
            ], 200); // HTTP 200 OK.

        } catch (\Exception $e) {
            // Handle any errors while fetching user data and log them.
            Log::error('Failed to retrieve user information: ' . $e->getMessage(), ['user_id' => Auth::id(), 'exception' => $e]);
            return response()->json([
                'message' => 'Failed to retrieve user information',
                'error' => 'An error occurred while fetching user data.'
            ], 500); // HTTP 500 Internal Server Error.
        }
    }

    /**
     * Update user profile information.
     * Allows authenticated users to update their profile data.
     *
     * @param Request $request HTTP request containing updated user data.
     * @return \Illuminate\Http\JsonResponse JSON response with updated user data.
     */
    public function updateProfile(Request $request)
    {
        // Get the authenticated user.
        $user = $request->user();

        // Validate the update request. 'sometimes' rule means the field is only validated if present.
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',          // Name can be updated.
            'phone' => 'sometimes|nullable|string|max:20',          // Phone can be updated.
            'location' => 'sometimes|required|string|max:255',      // Location can be updated.
            'password' => 'sometimes|required|string|min:8|confirmed', // Password can be updated, min 8 chars, confirmed.
        ]);

        // If validation fails, return error response.
        if ($validator->fails()) {
            Log::warning('User profile update validation failed.', ['user_id' => Auth::id(), 'errors' => $validator->errors()->toArray(), 'request_data' => $request->except('password', 'password_confirmation')]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // HTTP 422 Unprocessable Entity.
        }

        try {
            // Update user fields if provided in the request.
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('location')) {
                $user->location = $request->location;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->input('password')); // Store hashed password as string.
            }

            // Save the updated user data to the database.
            $user->save();

            // Return success response with updated user data.
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'user_type' => $user->user_type,
                    'location' => $user->location,
                    'display_name' => $user->display_name,
                ]
            ], 200); // HTTP 200 OK.

        } catch (\Exception $e) {
            // Handle any errors during profile update and log them.
            Log::error('Error updating user profile: ' . $e->getMessage(), ['user_id' => Auth::id(), 'request_data' => $request->except('password', 'password_confirmation'), 'exception' => $e]);
            return response()->json([
                'message' => 'Profile update failed',
                'error' => 'An error occurred while updating your profile.'
            ], 500); // HTTP 500 Internal Server Error.
        }
    }
}
