<?php

namespace App\Http\Controllers;

use App\Enums\SourcingRequestStatus;
use App\Models\SourcingRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SourcingController extends Controller
{
    public function create()
    {
        return view('pages.sourcing');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact' => ['required', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'title' => ['required', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2000'],
            'estimated_quantity' => ['nullable', 'integer', 'min:1'],
            'technical_specifications' => ['required', 'string', 'max:10000'],
            'sample' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $email = filter_var($data['contact'], FILTER_VALIDATE_EMAIL) ? $data['contact'] : null;
        $phone = $email ? null : $data['contact'];
        $user = User::query()
            ->when($email, fn ($query) => $query->where('email', $email))
            ->when($phone, fn ($query) => $query->where('phone', $phone))
            ->first();

        if ($user && (! filled($data['password'] ?? null) || ! Auth::attempt(
            array_filter(['email' => $email, 'phone' => $phone, 'password' => $data['password'] ?? null]),
            true,
        ))) {
            throw ValidationException::withMessages([
                'contact' => 'این حساب قبلاً ثبت شده است؛ برای ادامه رمز عبور آن را وارد کنید.',
                'password' => 'رمز عبور حساب صحیح نیست.',
            ]);
        }

        [$user, $requestRecord] = DB::transaction(function () use ($data, $email, $phone, $user, $request): array {
            $user ??= User::create([
                'name' => $data['name'],
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make($data['password'] ?: Str::random(32)),
                'role' => 'customer',
                'email_verified_at' => $email ? now() : null,
            ]);

            $attachments = [];
            if ($request->hasFile('sample')) {
                $attachments[] = [
                    'path' => $request->file('sample')->store('sourcing-requests', 'public'),
                    'original_name' => $request->file('sample')->getClientOriginalName(),
                ];
            }

            $requestRecord = SourcingRequest::create([
                'user_id' => $user->id,
                'status' => SourcingRequestStatus::PENDING,
                'title' => $data['title'],
                'technical_specifications' => $data['technical_specifications']
                    . ($data['source_url'] ? "\n\nلینک منبع: " . $data['source_url'] : ''),
                'estimated_quantity' => $data['estimated_quantity'] ?? null,
                'attachments' => $attachments ?: null,
            ]);

            return [$user, $requestRecord];
        });

        Auth::login($user, true);

        return redirect('/portal/portal-sourcing-requests')
            ->with('success', 'درخواست سورسینگ شما ثبت شد. کد پیگیری: ' . $requestRecord->reference_code);
    }
}
