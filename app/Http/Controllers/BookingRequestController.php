<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Models\BookingRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Events\BookingRequestUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use ZipArchive;
use File;

class BookingRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(Request $request)
    {
        return inertia('Admin/BookingRequests/Index', [
            'booking_requests' => BookingRequest::with('user', 'reservations', 'reservations.room')->get(),
            'rooms' => Room::hideUserRestrictions($request->user())->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('Requestee/BookingForm', [
            // example of the expected reservations format
            'room' => Room::get()->random(),
            'reservations' => [
                [
                    'start' => now(),
                    'end' => now()->addMinutes(2),
                ],
                [
                    'start' => now()->addDay(),
                    'end' => now()->addDay()->addMinutes(2),
                ],
            ]
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateBookingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBookingRequest $request)
    {
        $data = $request->validated();
        $room = Room::findOrFail($data['room_id']);
        $reservation = $data['reservations'][0];

        // validate room still available at given times
        foreach ($data['reservations'] as $value) {
            //$room->verifyDatesAreWithinRoomRestrictions($value['start'], $value['end']);
            //$room->verifyDatetimesAreWithinAvailabilities($value['start'], $value['end']);
            //$room->verifyRoomIsFreeValidation($value['start'], $value['end']);
        }

        if (array_key_exists('files', $data)) {
            // save the uploaded files
            $referenceFolder = "{$room->id}_".strtotime($reservation['start']).'_reference';

            foreach($data['files'] as $file) {
                $name = $file->getClientOriginalName();
                Storage::disk('public')->putFileAs($referenceFolder. '/', $file, $name);
            }
        }
        \DB::beginTransaction();

        // store booking in db
        $booking = BookingRequest::create([
            'user_id' => $request->user()->id,
            'start_time' => $reservation['start'],
            'end_time' => $reservation['end'],
            'status' => 'review',
            'event' => $data['event'],
            'onsite_contact' => $data['onsite_contact'] ?? [],
            'notes' => $data['notes'] ?? '',
            'reference' => (array_key_exists('files', $data) && count($data['files']) > 0) ?
                ["path" => $referenceFolder] : [],
        ]);


        foreach ($data['reservations'] as $reservation) {
            Reservation::create([
                'room_id' => $room->id,
                'booking_request_id' => $booking->id,
                'start_time' => $reservation['start'],
                'end_time' => $reservation['end'],
            ]);
        }

        \DB::commit();

        $log = '[' . date("F j, Y, g:i a") . ']' . ' - Created booking request';
        BookingRequestUpdated::dispatch($booking, $log);

        return redirect()->route('bookings.index')
            ->with('flash', ['banner' => 'Your Booking Request was submitted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookingRequest  $bookingRequest
     * @return \Illuminate\Http\Response
     */
    // public function show(BookingRequest $bookingRequest)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BookingRequest  $bookingRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(BookingRequest $bookingRequest)
    { 
        //$booking = $bookingRequest->load('user', 'reservations', 'reservations.room');
        //dd($booking);
        return inertia('Requestee/EditBookingForm', [
            'booking' => $bookingRequest->load('user', 'reservations', 'reservations.room'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookingRequest  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookingRequest $booking)
    {
        $date_format = "F j, Y, g:i a";

        $request->validateWithBag('updateBookingRequest', array(
            'room_id' => ['required', 'integer'],
            'start_time' => ['required', 'string', 'max:255'],
            'end_time' => ['required', 'string', 'max:255'],
        ));

        $room = Room::query()->findOrFail($request->room_id);
        $room->verifyDatetimesAreWithinAvailabilities($request->get('start_time'), $request->get('end_time'));
        $room->verifyDatesAreWithinRoomRestrictions($request->get('start_time'), $request->get('end_time'));

        $booking->fill($request->except(['reference']))->save();

        if($booking->wasChanged())
        {
            $log = '[' . date($date_format) . ']' . ' - Updated booking request location and/or date';
            BookingRequestUpdated::dispatch($booking, $log);
        }

        if($request->file())
        {
            $referenceFolder = $request->room_id.'_'.strtotime($request->start_time).'_reference';

            if(isset($booking->reference["path"]))
            {
                $referenceFolder = $booking->reference["path"];
            }
            foreach($request->reference as $file)
            {
                $name = $file->getClientOriginalName();
                Storage::disk('public')->putFileAs($referenceFolder . '/', $file, $name);
            }
            $booking->reference = ['path' => $referenceFolder];
            $booking->save();

            $log = '[' . date($date_format) . ']' . ' - Updated booking request reference file(s)';
            BookingRequestUpdated::dispatch($booking, $log);
        }
        //for now only one
        $reservation = $booking->reservations()->first();
        $reservation->room_id = $request->room_id;
        $reservation->start_time = $request->start_time;
        $reservation->end_time = $request->end_time;
        $reservation->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookingRequest  $bookingRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookingRequest $booking)
    {
        Reservation::where('booking_request_id', $booking->id)->delete();
        $booking->delete();

        return redirect(route('bookings.index'));
    }

    public function downloadReferenceFiles($folder)
    {
        $path = Storage::disk('public')->path($folder);

        $zip = new ZipArchive;

        $fileName = $folder . '.zip';

        $zip->open(Storage::disk('public')->path($fileName), ZipArchive::CREATE);
        $files = File::files($path);

        foreach ($files as $file) {
            $relativeNameInZipFile = basename($file);
            $zip->addFile($file, $relativeNameInZipFile);
        }
        $zip->close();

        return response()->download(Storage::disk('public')->path($fileName))->deleteFileAfterSend(true);

    }

    public function list(Request $request)
    {
        return inertia('Admin/BookingsList/Index', [
            'bookings' => BookingRequest::with('user','reservations.room')->get(),

        ]);
    }

 

}
