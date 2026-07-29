<x-admin-layout title="Edit Kantin" page-title="Edit Kantin: {{ $canteen->canteen_name }}">
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 28px;">
        <!-- Form Edit Kantin -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Edit Informasi Kantin</h2>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('seller.canteens.update', $canteen) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="canteen_name" class="admin-form-label admin-form-label-required">Nama Kantin</label>
                            <input 
                                type="text" 
                                id="canteen_name" 
                                name="canteen_name" 
                                class="admin-form-input {{ $errors->has('canteen_name') ? 'is-error' : '' }}"
                                value="{{ old('canteen_name', $canteen->canteen_name) }}"
                                required
                            />
                            @if($errors->has('canteen_name'))
                                <span class="admin-form-error">{{ $errors->first('canteen_name') }}</span>
                            @endif
                        </div>

                        <div class="admin-form-group">
                            <label for="estimated_time_min" class="admin-form-label">Estimasi Waktu (menit)</label>
                            <input 
                                type="number" 
                                id="estimated_time_min" 
                                name="estimated_time_min" 
                                class="admin-form-input {{ $errors->has('estimated_time_min') ? 'is-error' : '' }}"
                                value="{{ old('estimated_time_min', $canteen->estimated_time_min) }}"
                                min="1"
                                max="180"
                            />
                            @if($errors->has('estimated_time_min'))
                                <span class="admin-form-error">{{ $errors->first('estimated_time_min') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="description" class="admin-form-label">Deskripsi</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="admin-form-textarea {{ $errors->has('description') ? 'is-error' : '' }}"
                        >{{ old('description', $canteen->description) }}</textarea>
                        @if($errors->has('description'))
                            <span class="admin-form-error">{{ $errors->first('description') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="photo" class="admin-form-label">Update Foto Kantin</label>
                        <input 
                            type="file" 
                            id="photo" 
                            name="photo" 
                            class="admin-form-input {{ $errors->has('photo') ? 'is-error' : '' }}"
                            accept="image/*"
                        />
                        <span class="admin-form-helper">Format: JPG, PNG (Max: 2MB)</span>
                        @if($errors->has('photo'))
                            <span class="admin-form-error">{{ $errors->first('photo') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="is_open" class="admin-form-label">
                            <input type="checkbox" id="is_open" name="is_open" value="1" {{ old('is_open', $canteen->is_open) ? 'checked' : '' }} />
                            <span style="margin-left: 8px;">Kantin Buka Hari Ini</span>
                        </label>
                    </div>

                    <div style="border-top: 2px dashed var(--color-gray-300); padding-top: 20px; margin-top: 8px;">
                        <div class="admin-form-actions">
                            <a href="{{ route('seller.canteens.index') }}" class="admin-btn admin-btn-secondary">
                                ← Batal
                            </a>
                            <button type="submit" class="admin-btn admin-btn-primary">
                                ✓ Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Foto Preview -->
            @if($canteen->photo)
                <div class="admin-card">
                    <div class="admin-card-body" style="padding: 0;">
                        <img src="{{ Storage::url($canteen->photo) }}" alt="{{ $canteen->canteen_name }}" style="width: 100%; height: auto; display: block;">
                    </div>
                </div>
            @else
                <div class="admin-card" style="background: var(--color-gray-50);">
                    <div class="admin-card-body" style="text-align: center; padding: 24px;">
                        <div style="font-size: 32px; color: var(--color-gray-300); margin-bottom: 8px;">▣</div>
                        <div style="font-size: 12px; color: var(--color-gray-500);">Belum ada foto</div>
                    </div>
                </div>
            @endif

            <!-- Jadwal -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title" style="margin: 0;">Jadwal Operasional</h3>
                </div>
                <div class="admin-card-body">
                    <form id="scheduleForm" method="POST" action="{{ route('seller.canteens.schedule.update', $canteen) }}">
                        @csrf

                        <div style="margin-bottom: 16px; padding: 12px; border: 2.5px solid #000; background: #e0f7fa; box-shadow: 4px 4px 0 #000;">
                            <div style="font-size: 12px; font-weight: 700; margin-bottom: 8px; font-family: 'Courier New', monospace;">⚡ TERAPKAN KE SENIN-SABTU</div>
                            <div style="display: flex; gap: 6px; margin-bottom: 8px;">
                                <input 
                                    id="bulk_open" 
                                    type="time" 
                                    style="flex: 1; padding: 4px; font-size: 11px; border: 2px solid #000;"
                                />
                                <input 
                                    id="bulk_close" 
                                    type="time" 
                                    style="flex: 1; padding: 4px; font-size: 11px; border: 2px solid #000;"
                                />
                            </div>
                            <button 
                                type="button" 
                                id="bulkApplyBtn"
                                style="width: 100%; padding: 6px; font-size: 11px; font-weight: 700; background: #00bcd4; color: #000; border: 2px solid #000; box-shadow: 3px 3px 0 #000; cursor: pointer;"
                            >
                                ⇩ SALIN KE SENIN - SABTU
                            </button>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($schedules as $idx => $schedule)
                                @php
                                    $dow = $schedule->day_of_week ?? $idx;
                                    $isClosed = old('schedules.'.$idx.'.is_closed', $schedule->is_closed);
                                    $openVal = old('schedules.'.$idx.'.open_time', $schedule->open_time ? substr($schedule->open_time, 0, 5) : '');
                                    $closeVal = old('schedules.'.$idx.'.close_time', $schedule->close_time ? substr($schedule->close_time, 0, 5) : '');
                                @endphp
                                <div class="schedule-row" data-day="{{ $dow }}" style="padding: 8px 0; border-bottom: 1px dashed var(--color-gray-300);">
                                    <input type="hidden" name="schedules[{{ $idx }}][day_of_week]" value="{{ $dow }}" />

                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 12px;">
                                        <label style="display: flex; align-items: center; gap: 6px; flex: 1;">
                                            <input 
                                                class="schedule-closed"
                                                type="checkbox" 
                                                name="schedules[{{ $idx }}][is_closed]" 
                                                value="1"
                                                {{ $isClosed ? 'checked' : '' }}
                                            />
                                            <span style="font-weight: 600;">{{ $days[$dow] }}</span>
                                        </label>
                                    </div>

                                    <div class="schedule-times" style="display: {{ $isClosed ? 'none' : 'flex' }}; gap: 6px; margin-top: 6px;">
                                        <input 
                                            class="schedule-open"
                                            type="time" 
                                            name="schedules[{{ $idx }}][open_time]" 
                                            value="{{ $openVal }}"
                                            style="flex: 1; padding: 4px; font-size: 11px; border: 1px solid var(--color-gray-300);"
                                        />
                                        <input 
                                            class="schedule-close"
                                            type="time" 
                                            name="schedules[{{ $idx }}][close_time]" 
                                            value="{{ $closeVal }}"
                                            style="flex: 1; padding: 4px; font-size: 11px; border: 1px solid var(--color-gray-300);"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="admin-btn admin-btn-secondary" style="width: 100%; margin-top: 12px;">
                            Simpan Jadwal
                        </button>
                    </form>
                </div>
            </div>

            <script>
            (function () {
                var rows = document.querySelectorAll('.schedule-row');

                rows.forEach(function (row) {
                    var closedChk = row.querySelector('.schedule-closed');
                    var timesWrap = row.querySelector('.schedule-times');
                    var openInp = row.querySelector('.schedule-open');
                    var closeInp = row.querySelector('.schedule-close');

                    closedChk.addEventListener('change', function () {
                        if (closedChk.checked) {
                            timesWrap.style.display = 'none';
                            openInp.value = '';
                            closeInp.value = '';
                        } else {
                            timesWrap.style.display = 'flex';
                        }
                    });
                });

                var bulkBtn = document.getElementById('bulkApplyBtn');
                if (bulkBtn) {
                    bulkBtn.addEventListener('click', function () {
                        var bulkOpen = document.getElementById('bulk_open').value;
                        var bulkClose = document.getElementById('bulk_close').value;

                        if (!bulkOpen || !bulkClose) {
                            alert('Isi kedua kolom jam terlebih dahulu!');
                            return;
                        }
                        if (bulkOpen >= bulkClose) {
                            alert('Jam tutup harus lebih besar dari jam buka!');
                            return;
                        }

                        rows.forEach(function (row) {
                            var day = parseInt(row.getAttribute('data-day'), 10);
                            if (day >= 1 && day <= 6) {
                                var closedChk = row.querySelector('.schedule-closed');
                                var timesWrap = row.querySelector('.schedule-times');
                                var openInp = row.querySelector('.schedule-open');
                                var closeInp = row.querySelector('.schedule-close');

                                closedChk.checked = false;
                                timesWrap.style.display = 'flex';
                                openInp.value = bulkOpen;
                                closeInp.value = bulkClose;
                            }
                        });
                    });
                }
            })();
            </script>
        </div>
    </div>
</x-admin-layout>
