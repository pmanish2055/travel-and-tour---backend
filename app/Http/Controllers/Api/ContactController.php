<?php
/**
 * File: app/Http/Controllers/Api/ContactController.php
 * Purpose: Handle contact form and newsletter subscription.
 *          Routes: POST /api/v1/contact, POST /api/v1/subscribe
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /** Store contact message */
    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name"=>"required|string|max:255",
            "email"=>"required|email",
            "phone"=>"nullable|string",
            "subject"=>"nullable|string",
            "message"=>"required|string|max:2000"
        ]);
        $msg = ContactMessage::create($data);
        return response()->json(["success"=>true,"message"=>"Message sent! We will reply soon.","data"=>$msg],201);
    }

    /** Subscribe to newsletter */
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate(["email"=>"required|email|unique:subscribers,email"]);
        $sub = Subscriber::create(["email"=>$data["email"], "is_verified"=>false]);
        return response()->json(["success"=>true,"message"=>"Subscribed successfully!","data"=>$sub],201);
    }

    /** Check if email already subscribed - GET /api/v1/subscribe/check?email= */
    public function check(Request $request): JsonResponse
    {
        $request->validate(["email"=>"required|email"]);
        $exists = Subscriber::where("email", $request->email)->exists();
        return response()->json([
            "success"=>true,
            "data"=>["email"=>$request->email, "is_subscribed"=>$exists],
            "message"=>$exists ? "Email already subscribed" : "Email not yet subscribed"
        ]);
    }
}
