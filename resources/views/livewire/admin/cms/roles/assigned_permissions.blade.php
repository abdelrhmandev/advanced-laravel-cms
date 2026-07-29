<flux:card>

    <flux:heading size="lg" class="mb-6">
        Manage Permissions
    </flux:heading>


    <flux:error name="selectedPermissions" />


    <flux:table>
        <flux:table.rows>

            {{-- ✅ Select All --}}
            <flux:table.row>
                <flux:table.cell class="font-bold text-zinc-900 w-1/4">
                    Administrator Access
                </flux:table.cell>

                <flux:table.cell>
                    <div x-data="{
                        toggleAll(checked) {
                            if (checked) {
                                $wire.set('selectedPermissions', @js($permissions->pluck('name')));
                            } else {
                                $wire.set('selectedPermissions', []);
                            }
                        }
                    }">
                        <flux:checkbox label="Select All Permissions" class="font-semibold text-blue-600"
                            x-on:change="toggleAll($event.target.checked)" />
                    </div>
                </flux:table.cell>
            </flux:table.row>

            {{-- ✅ Group Permissions --}}
            @php
                $groupedPermissions = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
            @endphp

            @forelse ($groupedPermissions as $group => $groupPerms)

                <flux:table.row>
                    <flux:table.cell class="font-bold uppercase text-zinc-700 w-1/4">
                        {{ $group }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex flex-wrap gap-4">

                            @foreach ($groupPerms as $permission)
                                @php
                                    [$module, $action] = explode('.', $permission->name);
                                @endphp

                                <div x-data
                                    x-on:change="
                                        let checked = $event.target.checked;
                                        let module = '{{ $module }}';
                                        let action = '{{ $action }}';

                                        if (action !== 'index' && checked) {
                                            let indexPermission = module + '.index';

                                            if (!$wire.selectedPermissions.includes(indexPermission)) {
                                                $wire.selectedPermissions.push(indexPermission);
                                            }
                                        }

                                        if (action === 'index' && !checked) {
                                            let updated = $wire.selectedPermissions
                                                .filter(p => !p.startsWith(module + '.'));

                                            $wire.set('selectedPermissions', updated);
                                        }
                                    ">



                                    <div class="flex items-center gap-3">
                                        <flux:checkbox wire:model.live="selectedPermissions"
                                            value="{{ $permission->name }}" />
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300 capitalize">
                                            {{ ucfirst($action) }}
                                        </span>


                                    </div>


                                </div>
                            @endforeach


                        </div>
                    </flux:table.cell>
                </flux:table.row>

            @empty
                <flux:table.row>
                    <flux:table.cell colspan="2">

                        <flux:card
                            class="flex flex-col items-center justify-center py-16 gap-4 text-center border-dashed border-2">
                            <flux:icon name="shield-check" class="size-12 text-zinc-300" />

                            <div>
                                <flux:heading size="lg">
                                    No permissions configured
                                </flux:heading>

                                <flux:subheading>
                                    Permissions not found. Please sync first.
                                </flux:subheading>
                            </div>

                            <flux:button variant="primary" icon="arrow-path" href="{{ route('admin.permissions') }}">
                                Sync Now
                            </flux:button>

                        </flux:card>

                    </flux:table.cell>
                </flux:table.row>
            @endforelse

        </flux:table.rows>
    </flux:table>

</flux:card>
