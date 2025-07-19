<?php

namespace App\Http\Middleware;

use Closure; // Imports the Closure class, representing an anonymous function.
use Illuminate\Http\Request; // Imports the Request class to handle HTTP requests.
use Illuminate\Support\Facades\Auth; // Imports the Auth facade for user authentication.
use Illuminate\Support\Facades\Log; // IMPORTS THE LOG FACADE TO RESOLVE INTELEPHENSE WARNING AND ENABLE LOGGING.
use Symfony\Component\HttpFoundation\Response; // Imports the Response class for type hinting the return.

/**
 * Admin Middleware - UNZA Carbon Calculator
 *
 * This middleware ensures that only users with administrative privileges
 * can access admin-only routes and functionality. It acts as a security
 * gate before admin controllers are executed.
 *
 * Key Features:
 * - Checks if user is authenticated
 * - Verifies user has admin privileges
 * - Redirects unauthorized users appropriately
 * - Logs unauthorized access attempts
 *
 * This middleware implements the Strategy Pattern by defining a specific
 * authentication strategy for admin routes, separate from regular user auth.
 *
 * @package App\Http\Middleware
 * @author Developed by Levy Bronzoh, Climate Yanga
 * @version 1.0
 * @since July 2025
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request and check for admin privileges
     *
     * This method implements the middleware pattern by:
     * 1. Checking if user is authenticated
     * 2. Verifying admin privileges
     * 3. Either allowing access or redirecting/denying
     *
     * The method follows Laravel's middleware contract by accepting
     * a Request object and Closure, then returning a Response
     *
     * @param Request $request - The incoming HTTP request
     * @param Closure $next - The next middleware or controller in the pipeline
     * @return Response - HTTP response (either continues or redirects)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First, check if user is authenticated
        // This is a defensive check even though auth middleware should run first
        if (!Auth::check()) {
            // User is not authenticated at all
            // Log the attempt to access admin area without authentication.
            Log::warning('Unauthenticated admin access attempt', [
                'requested_url' => $request->url(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            // Redirect to login page with intended URL for after login
            return redirect()->route('login')
                             ->with('error', 'Please login to access this area.')
                             ->withInput(['intended' => $request->url()]);
        }

        // Get the currently authenticated user
        $user = Auth::user();

        // Check if user has admin privileges using multiple methods for flexibility.
        // Method 1: Check if user_type is 'admin'
        if ($user->user_type === 'admin') {
            // User is admin, allow access to continue
            return $next($request);
        }

        // Method 2: Check if user has specific admin role/permission
        // This could be extended to use a roles/permissions system
        if ($this->isUserAdmin($user)) {
            // User has admin privileges, allow access
            return $next($request);
        }

        // Method 3: Check if user email is in admin whitelist
        // Useful for initial setup or emergency access
        if ($this->isEmailInAdminWhitelist($user->email)) {
            // User email is whitelisted as admin, allow access
            return $next($request);
        }

        // User is authenticated but not an admin
        // Log the unauthorized access attempt for security monitoring
        Log::warning('Unauthorized admin access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_type' => $user->user_type,
            'requested_url' => $request->url(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        // Determine appropriate response based on request type
        if ($request->expectsJson()) {
            // API request - return JSON error response
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have administrator privileges to access this resource.',
                'code' => 403
            ], 403);
        }

        // Web request - redirect to dashboard with error message
        return redirect()->route('dashboard')
                         ->with('error', 'You do not have administrator privileges to access that area.');
    }

    /**
     * Check if user has admin privileges through role-based system
     *
     * This method can be extended to work with more complex role/permission
     * systems like Spatie Laravel Permission package
     *
     * @param \App\Models\User $user - The user to check
     * @return bool - True if user has admin privileges
     */
    private function isUserAdmin($user): bool
    {
        // Check for admin role in user_type field
        if (in_array($user->user_type, ['admin', 'super_admin', 'administrator'])) {
            return true;
        }

        // Check if user has admin permission (if using permission system)
        // Uncomment below if you implement a permission system
        // if ($user->can('access-admin-panel')) {
        //     return true;
        // }

        // Check if user has admin role (if using role system)
        // Uncomment below if you implement a role system
        // if ($user->hasRole('admin')) {
        //     return true;
        // }

        return false;
    }

    /**
     * Check if user email is in admin whitelist
     *
     * This method provides a fallback admin access mechanism
     * Useful for initial setup or emergency access
     *
     * @param string $email - The user's email address
     * @return bool - True if email is in admin whitelist
     */
    private function isEmailInAdminWhitelist(string $email): bool
    {
        // Define admin whitelist - these emails automatically get admin access
        // You can move this to config file or database for easier management
        $adminWhitelist = [
            'admin@climateyanga.com',
            'fred.kisela@climateyanga.com',
            'levy.bronzoh@climateyanga.com',
            'admin@unza.zm'
        ];

        // Check if current user email is in the whitelist
        return in_array(strtolower($email), array_map('strtolower', $adminWhitelist));
    }

    /**
     * Check if user is accessing from allowed IP addresses (optional security layer)
     *
     * This method adds an extra security layer by restricting admin access
     * to specific IP addresses or ranges
     *
     * @param string $ipAddress - The user's IP address
     * @return bool - True if IP is allowed for admin access
     */
    private function isIpAllowedForAdmin(string $ipAddress): bool
    {
        // Define allowed IP addresses/ranges for admin access
        // This is optional and should be used carefully
        $allowedIps = [
            '127.0.0.1',        // Localhost
            '::1',              // IPv6 localhost
            '192.168.1.0/24',   // Local network range
            // Add your office/trusted IP addresses here
        ];

        // Check if current IP is in allowed list
        foreach ($allowedIps as $allowedIp) {
            if ($this->ipInRange($ipAddress, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP address is within specified range
     *
     * Helper method to check if an IP address falls within a CIDR range
     *
     * @param string $ip - IP address to check
     * @param string $range - CIDR range (e.g., '192.168.1.0/24')
     * @return bool - True if IP is in range
     */
    private function ipInRange(string $ip, string $range): bool
    {
        // Handle single IP addresses
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        // Handle CIDR ranges
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }

    /**
     * Get user's admin level for granular permissions
     *
     * This method can be used to implement different levels of admin access
     * For example: super_admin, admin, moderator, etc.
     *
     * @param \App\Models\User $user - The user to check
     * @return string - The user's admin level
     */
    private function getUserAdminLevel($user): string
    {
        // Map user types to admin levels
        $adminLevels = [
            'super_admin' => 'super_admin',
            'admin' => 'admin',
            'administrator' => 'admin',
            'moderator' => 'moderator',
            'staff' => 'staff',
            'student' => 'none'
        ];

        return $adminLevels[$user->user_type] ?? 'none';
    }
}
