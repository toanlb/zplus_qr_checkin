<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MobileCheckInController extends Controller
{
    /**
     * Process QR code and perform check-in
     */
    public function processQrCode(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'QR code không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $qrCode = $request->qr_code;

        try {
            // Find user by QR code
            $member = User::where('qr_code', $qrCode)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thành viên với mã QR này',
                ], 404);
            }

            // Check if user has active membership
            if (!$member->hasActiveMembership()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thành viên không có gói membership đang hoạt động',
                    'user' => [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'member_type' => $member->member_type,
                    ]
                ], 400);
            }

            $today = Carbon::now()->toDateString();
            $now = Carbon::now();

            // Transaction to ensure data consistency
            DB::beginTransaction();
            try {
                // Check if user already checked in today
                $existingCheckIn = $member->checkIns()
                    ->whereDate('date', $today)
                    ->whereNull('check_out_time')
                    ->latest()
                    ->first();

                if ($existingCheckIn) {
                    // User is checking out
                    $existingCheckIn->update([
                        'check_out_time' => $now,
                    ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Check-out thành công',
                        'check_in' => [
                            'id' => $existingCheckIn->id,
                            'check_in_time' => $existingCheckIn->check_in_time->format('H:i:s'),
                            'check_out_time' => $now->format('H:i:s'),
                            'date' => $today,
                        ],
                        'user' => [
                            'id' => $member->id,
                            'name' => $member->name,
                            'email' => $member->email,
                            'member_type' => $member->member_type,
                        ]
                    ]);
                } else {
                    // User is checking in
                    $checkIn = $member->checkIns()->create([
                        'check_in_time' => $now,
                        'date' => $today,
                    ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Check-in thành công',
                        'check_in' => [
                            'id' => $checkIn->id,
                            'check_in_time' => $now->format('H:i:s'),
                            'check_out_time' => null,
                            'date' => $today,
                        ],
                        'user' => [
                            'id' => $member->id,
                            'name' => $member->name,
                            'email' => $member->email,
                            'member_type' => $member->member_type,
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Check-in error: ' . $e->getMessage(), [
                    'user_id' => $member->id,
                    'qr_code' => $qrCode,
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý check-in',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('QR scanning error: ' . $e->getMessage(), [
                'qr_code' => $qrCode,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xử lý mã QR',
            ], 500);
        }
    }

    /**
     * Get check-in history for staff viewing
     */
    public function getCheckInHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|date',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $date = $request->date ?? Carbon::now()->toDateString();
        $query = CheckIn::with('user')->whereDate('date', $date);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $checkIns = $query->latest('check_in_time')->get();

        return response()->json([
            'success' => true,
            'data' => $checkIns->map(function ($checkIn) {
                return [
                    'id' => $checkIn->id,
                    'check_in_time' => $checkIn->check_in_time?->format('H:i:s'),
                    'check_out_time' => $checkIn->check_out_time?->format('H:i:s'),
                    'date' => $checkIn->date->format('Y-m-d'),
                    'user' => [
                        'id' => $checkIn->user->id,
                        'name' => $checkIn->user->name,
                        'email' => $checkIn->user->email,
                        'member_type' => $checkIn->user->member_type,
                    ]
                ];
            }),
            'date' => $date,
        ]);
    }
}
