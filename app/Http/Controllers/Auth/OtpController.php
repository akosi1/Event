<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\OtpVerification;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OtpController extends Controller
{
    /**
     * Show OTP verification form
     */
    public function show(Request $request): View
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'registration');
        
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        return view('auth.verify-otp', compact('email', 'type'));
    }

    /**
     * Send OTP to email
     */
    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|ends_with:@mcclawis.edu.ph',
            'type' => 'required|in:registration,login',
            'student_name' => 'required_if:type,registration|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');
        $type = $request->input('type');
        $studentName = $request->input('student_name', 'Student');

        // For registration, check if student exists in database
        if ($type === 'registration') {
            $existingStudent = Student::where('email', $email)->first();
            if (!$existingStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found in student database. Please contact administration.'
                ], 404);
            }
            $studentName = $existingStudent->full_name;
        }

        try {
            // Generate OTP
            $otp = OtpVerification::generateOtp($email, $type, $request->ip());
            
            // Send email
            Mail::to($email)->send(new OtpVerificationMail(
                $otp->otp_code,
                $studentName,
                $type
            ));

            // Store in session for verification
            $request->session()->put('otp_email', $email);
            $request->session()->put('otp_type', $type);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your MS365 email.',
                'expires_in' => $otp->remaining_time
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send OTP', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6',
            'email' => 'required|email',
            'type' => 'required|in:registration,login'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP format.',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');
        $otpCode = $request->input('otp_code');
        $type = $request->input('type');

        // Verify session matches
        if ($request->session()->get('otp_email') !== $email) {
            return response()->json([
                'success' => false,
                'message' => 'Session mismatch. Please request a new OTP.'
            ], 400);
        }

        // Verify OTP
        if (OtpVerification::verifyOtp($email, $otpCode, $type)) {
            // Clear session data
            $request->session()->forget(['otp_email', 'otp_type']);
            
            // Mark email as verified for the type
            $request->session()->put('otp_verified_' . $type, $email);
            $request->session()->put('otp_verified_at', now());

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'redirect_url' => $type === 'registration' ? route('register.complete') : route('dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP. Please try again.'
        ], 400);
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request): JsonResponse
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'registration');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please start over.'
            ], 400);
        }

        // Check rate limiting (prevent spam)
        $recentOtp = OtpVerification::where('email', $email)
            ->where('created_at', '>', now()->subMinute())
            ->first();

        if ($recentOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another OTP.',
                'retry_after' => 60 - now()->diffInSeconds($recentOtp->created_at)
            ], 429);
        }

        $studentName = 'Student';
        if ($type === 'registration') {
            $student = Student::where('email', $email)->first();
            if ($student) {
                $studentName = $student->full_name;
            }
        }

        try {
            // Generate new OTP
            $otp = OtpVerification::generateOtp($email, $type, $request->ip());
            
            // Send email
            Mail::to($email)->send(new OtpVerificationMail(
                $otp->otp_code,
                $studentName,
                $type
            ));

            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully.',
                'expires_in' => $otp->remaining_time
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }
}