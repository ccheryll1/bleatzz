<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CanteenController extends Controller
{
    /** Helper: pastikan seller punya akses ke canteen ini */
    private function authorizeCanteen(Canteen $canteen): void
    {
        $sellerCanteenIds = auth()->user()->canteens()->select('canteens.id')->pluck('canteens.id');
        abort_unless($sellerCanteenIds->contains($canteen->id), 403, 'Anda tidak berhak mengelola kantin ini.');
    }

    /** Dashboard: list kantin yang dikelola seller */
    public function index(): View
    {
        $canteens = auth()->user()->canteens()
            ->withCount(['menus', 'toppings'])
            ->withAvg('reviews', 'rating')
            ->get();

        return view('pages.admin.seller.canteens.index', compact('canteens'));
    }

    /** Form edit info dasar kantin */
    public function edit(Canteen $canteen): View
    {
        $this->authorizeCanteen($canteen);
        $canteen->load('schedules');

        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $schedules = collect();
        foreach (range(0, 6) as $day) {
            $schedules->push($canteen->schedules->firstWhere('day_of_week', $day) ?? (object) [
                'day_of_week' => $day,
                'open_time' => null,
                'close_time' => null,
                'is_closed' => false,
            ]);
        }

        return view('pages.admin.seller.canteens.edit', compact('canteen', 'days', 'schedules'));
    }

    /** Update info dasar kantin */
    public function update(Request $request, Canteen $canteen): RedirectResponse
    {
        $this->authorizeCanteen($canteen);

        $validated = $request->validate([
            'canteen_name'       => ['required', 'string', 'max:100'],
            'description'        => ['nullable', 'string', 'max:500'],
            'photo'              => ['nullable', 'image', 'max:2048'],
            'estimated_time_min' => ['nullable', 'integer', 'min:1', 'max:180'],
            'is_open'            => ['nullable', 'boolean'],
        ]);

        $data = collect($validated)->only(['canteen_name', 'description', 'estimated_time_min'])->all();
        $data['is_open'] = $request->boolean('is_open', false);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('canteens', 'public');
        }

        $canteen->update($data);

        return back()->with('success', 'Info kantin berhasil diperbarui.');
    }

    /** Update jadwal operasional */
    public function updateSchedule(Request $request, Canteen $canteen): RedirectResponse
    {
        $this->authorizeCanteen($canteen);

        $request->validate([
            'schedules'                  => ['required', 'array', 'size:7'],
            'schedules.*.day_of_week'    => ['required', 'integer', 'min:0', 'max:6'],
            'schedules.*.is_closed'      => ['nullable', 'boolean'],
            'schedules.*.open_time'      => ['required_unless:schedules.*.is_closed,1', 'nullable', 'date_format:H:i'],
            'schedules.*.close_time'     => ['required_unless:schedules.*.is_closed,1', 'nullable', 'date_format:H:i', 'after:schedules.*.open_time'],
        ]);

        foreach ($request->schedules as $schedule) {
            $isClosed = !empty($schedule['is_closed']);
            Schedule::updateOrCreate(
                [
                    'canteen_id'  => $canteen->id,
                    'day_of_week' => $schedule['day_of_week'],
                ],
                [
                    'open_time'  => $isClosed ? null : ($schedule['open_time'] ?? null),
                    'close_time' => $isClosed ? null : ($schedule['close_time'] ?? null),
                    'is_closed'  => $isClosed,
                ]
            );
        }

        return back()->with('success', 'Jadwal operasional berhasil diperbarui.');
    }
}
