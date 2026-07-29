<x-admin-layout title="Kelola User" page-title="Kelola User">
    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Buyer</div>
            <div class="admin-stat-value">{{ $totalBuyers }}</div>
            <div class="admin-stat-accent"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Seller</div>
            <div class="admin-stat-value">{{ $totalSellers }}</div>
            <div class="admin-stat-accent" style="background: var(--color-cyan);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Aktif</div>
            <div class="admin-stat-value">{{ $totalActive }}</div>
            <div class="admin-stat-accent" style="background: var(--color-warning);"></div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar User</h2>
            <a href="{{ route('manager.users.create') }}" class="admin-btn admin-btn-primary">
                + Buat Seller Baru
            </a>
        </div>
        <div class="admin-card-body">
            <!-- Search & Filter -->
            <form method="GET" class="admin-form-row" style="margin-bottom: 24px;">
                <div class="admin-form-group">
                    <input 
                        type="text" 
                        name="search" 
                        class="admin-form-input" 
                        placeholder="Cari nama, username, atau email..."
                        value="{{ request('search') }}"
                    />
                </div>
                <div class="admin-form-group">
                    <select name="role" class="admin-form-select">
                        <option value="">Semua Role</option>
                        <option value="buyer" {{ request('role') === 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Seller</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <button type="submit" class="admin-btn admin-btn-secondary">Cari</button>
                </div>
            </form>

            @if($users->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="font-semibold">{{ $user->name }}</td>
                                    <td><code>{{ $user->username }}</code></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="admin-badge admin-badge-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge admin-badge-{{ $user->is_active ? 'active' : 'inactive' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-btn-group">
                                            <a href="{{ route('manager.users.edit', $user->id) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('manager.users.toggle-active', $user->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="admin-btn admin-btn-sm {{ $user->is_active ? 'admin-btn-danger' : 'admin-btn-success' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="margin-top: 24px;">
                    {{ $users->links() }}
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">◎</div>
                    <div class="admin-empty-state-text">Tidak ada user ditemukan</div>
                    <div class="admin-empty-state-sub">Coba ubah filter pencarian atau buat seller baru</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
