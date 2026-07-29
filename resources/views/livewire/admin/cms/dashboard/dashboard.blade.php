{{-- resources/views/livewire/admin/cms/dashboard/dashboard.blade.php --}}

<div>

    @php
        $admin = auth('admin')->user();
    @endphp

    @if (!$admin || !$admin->can('dashboard.getdashboarddata'))
        @include('livewire.admin.partials.permissions.403_message')
    @else
        @if ($is_skeleton)
            {{-- Skeleton --}}
            <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (range(1, 4) as $i)
                        <div
                            class="animate-pulse rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 p-5 h-28">
                        </div>
                    @endforeach
                </div>
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div
                        class="animate-pulse rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 h-72">
                    </div>
                    <div
                        class="animate-pulse rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 h-72">
                    </div>
                </div>
            </div>
        @else
            {{-- Dashboard Content --}}
            <div class="flex h-full w-full flex-1 flex-col gap-6">

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div>

                        {{-- {{ app('settings')['site_title_' . app()->getLocale()] ?? '' }} --}}


                        <flux:heading size="xl">Dashboard</flux:heading>
                        <flux:subheading>Welcome back, {{ auth('admin')->user()->name }}</flux:subheading>
                    </div>
                    <flux:button wire:click="refresh" icon="arrow-path" size="sm" variant="outline">
                        Refresh
                    </flux:button>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Total Users --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                <flux:icon.users class="size-5" />
                            </div>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Total Users</span>
                        </div>
                        <div class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($stats['total_users']) }}
                        </div>
                    </div>

                    {{-- Active Users --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="p-2 rounded-lg bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                <flux:icon.check-circle class="size-5" />
                            </div>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Active Users</span>
                        </div>
                        <div class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($userStatus['active']) }}
                        </div>
                        <div class="text-xs text-zinc-400 mt-1">
                            {{ $userStatus['inactive'] }} inactive
                        </div>
                    </div>

                    {{-- Inactive Users --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="p-2 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                <flux:icon.x-circle class="size-5" />
                            </div>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Inactive Users</span>
                        </div>
                        <div class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($userStatus['inactive']) }}
                        </div>
                        @if ($stats['total_users'] > 0)
                            <div class="text-xs text-zinc-400 mt-1">
                                {{ round(($userStatus['inactive'] / $stats['total_users']) * 100, 1) }}% of total
                            </div>
                        @endif
                    </div>

                    {{-- Blocks --}}
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="p-2 rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                <flux:icon.squares-2x2 class="size-5" />
                            </div>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Blocks</span>
                        </div>
                        <div class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($stats['blocks']) }}
                        </div>
                    </div>
                </div>

                {{-- Charts Row --}}


 {{-- Charts Row --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

    {{-- Roles Chart --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6" wire:ignore>
        <flux:heading size="sm" class="mb-4">Users by Role</flux:heading>
        @if (!empty($rolesNames))
            <div class="relative h-64">
                <canvas id="rolesChart"></canvas>
            </div>
        @else
            <div class="flex items-center justify-center h-52 text-zinc-400 text-sm">
                No roles found
            </div>
        @endif
    </div>

    {{-- User Status Chart --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6" wire:ignore>
        <flux:heading size="sm" class="mb-4">User Status</flux:heading>
        <div class="relative h-64">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="flex justify-center gap-6 mt-4">
            <div class="flex items-center gap-2 text-sm text-zinc-500">
                <span class="inline-block size-3 rounded-full bg-green-400"></span>
                Active ({{ $userStatus['active'] }})
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-500">
                <span class="inline-block size-3 rounded-full bg-amber-400"></span>
                Inactive ({{ $userStatus['inactive'] }})
            </div>
        </div>
    </div>

</div>



                {{-- Roles Breakdown Table --}}
                @if (!empty($rolesNames))
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                        <flux:heading size="sm" class="mb-4">Roles Breakdown</flux:heading>
                        <div class="space-y-3">
                            @foreach ($rolesNames as $i => $role)
                                @php
                                    $count = $rolesCount[$i] ?? 0;
                                    $percent =
                                        $stats['total_users'] > 0
                                            ? round(($count / $stats['total_users']) * 100, 1)
                                            : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span
                                            class="text-zinc-600 dark:text-zinc-300 capitalize">{{ $role }}</span>
                                        <span class="text-zinc-500">{{ number_format($count) }}
                                            ({{ $percent }}%)
                                        </span>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                                            style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        @endif
    @endif

</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endonce

@script
<script>
    let rolesChart = null;
    let statusChart = null;

    function renderCharts({ names, counts, userStatus }) {
        const rolesCanvas = document.getElementById('rolesChart');
        if (rolesCanvas) {
            if (rolesChart) {
                rolesChart.destroy();
                rolesChart = null;
            }

            rolesChart = new Chart(rolesCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: names,
                    datasets: [{
                        data: counts,
                        backgroundColor: [
                            '#3b82f6', '#8b5cf6', '#f59e0b',
                            '#10b981', '#ef4444', '#06b6d4',
                        ],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        }

        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            if (statusChart) {
                statusChart.destroy();
                statusChart = null;
            }

            statusChart = new Chart(statusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Inactive'],
                    datasets: [{
                        data: [userStatus.active, userStatus.inactive],
                        backgroundColor: ['#4ade80', '#fbbf24'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                },
            });
        }
    }

    function waitForChartThenRender(data) {
        if (typeof Chart === 'undefined') {
            // CDN script may not have finished loading yet on first paint
            setTimeout(() => waitForChartThenRender(data), 50);
            return;
        }
        renderCharts(data);
    }

    $wire.on('initChart', (event) => {
        const data = event[0];
        waitForChartThenRender(data);
    });
</script>
@endscript
