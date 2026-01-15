<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

/**
 * Profile Completion Filter
 * 
 * Checks if user has completed their profile (changed password, email, and uploaded photo)
 * Redirects to profile page if not completed
 */
class ProfileCompletionFilter implements FilterInterface
{
    /**
     * Before filter - check if user has completed profile
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $request;
        }

        $userId = session()->get('userId');
        
        // Get URI segments for more precise checking
        $uri = service('uri');
        $segment1 = $uri->getSegment(1); // First segment (e.g., 'profile', 'admin', 'guru')
        
        // Skip check if on profile routes or logout
        // Only check first segment to avoid matching URLs like /admin/user-profile
        if ($segment1 === 'profile' || $segment1 === 'logout' || $segment1 === 'login') {
            return $request;
        }
        
        // Performance optimization: Check session cache first
        // If profile was already marked as completed in this session, skip database check
        if (session()->get('profile_completed') === true) {
            return $request;
        }

        // Check if profile needs completion (database query)
        $userModel = new UserModel();
        if ($userModel->needsProfileCompletion($userId)) {
            // Set flash message to inform user
            session()->setFlashdata('warning', 'Lengkapi profil kamu dulu ya! Ganti password, isi email, dan upload foto profil 📝✨');
            
            // Redirect to profile page
            return redirect()->to('/profile');
        }
        
        // Profile is complete, cache this status in session to avoid repeated DB queries
        session()->set('profile_completed', true);

        return $request;
    }

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
