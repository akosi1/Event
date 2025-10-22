<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;



use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $certificates = Certificate::with('event')->where('user_id', $user->id)->get();

        // dd($certificates);

        return view('certificates', compact('certificates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function generateCertificate(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $user = Auth::user();
        $fullName = $user->first_name . ' ' . $user->last_name;
        $certificateTemplate = $event->certificate_template_image;

        // load the based64 image
        $base64Data = substr($certificateTemplate, strpos($certificateTemplate, ',') + 1);


        if(!$certificateTemplate){
            return response()->json([
                'success' => false,
                'message' => 'Certificate Template Not Found!',
            ]);
        }
        //decode the based64 image
        $image = Image::make(base64_decode($base64Data));

        // get image width and height
        $imgWidth = $image->width();
        $imgHeight = $image->height();

        $fontSize = 30;

        // calculate the coordinate
        $lineY = 450;
        $textY = $lineY - 195;


        $image->text($fullName, $imgWidth / 2, $textY, function($font) use ($fontSize) {
            $font->file(public_path('fonts/PlayfairDisplay-Bold.ttf'));
            $font->size($fontSize);
            $font->color('#5e4823');
            $font->align('center');
            $font->valign('bottom');
        });


        $generatedBase64 = (string) $image->encode('jpg'); // raw binary
        $generatedBase64 = 'data:image/jpeg;base64,' . base64_encode($generatedBase64);

        Certificate::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'certificate_path' => $generatedBase64,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificate generated successfully!',
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
