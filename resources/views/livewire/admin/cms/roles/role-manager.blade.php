<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">Role Management</flux:heading>
        <flux:subheading>Manage system access levels and role-based permissions.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Form & List --}}
        <div class="space-y-6">
            {{-- 1. Create/Edit Form --}}




            <flux:card class="space-y-4">
                <flux:heading size="md">{{ $selectedRoleId ? 'Update Role' : 'Create New Role' }} <span class="text-red-500 ms-1">*</span></flux:heading>



                <flux:input wire:model="name" label="Role Title" placeholder="e.g. Content Editor" />

                <div class="flex gap-2">
                    @if ($selectedRoleId)
                        @if (auth('admin')->user()?->can('roles.edit'))
                            <flux:button wire:click="updateRole" variant="primary">
                                Update Role
                            </flux:button>
                        @endif
                    @else
                        @if (auth('admin')->user()?->can('roles.create'))
                            <flux:button wire:click="createRole" variant="primary">
                                Create Role
                            </flux:button>
                        @endcan
                    @endif

                    @if ($selectedRoleId)
                        <flux:button wire:click="resetForm" variant="ghost">
                            Cancel
                        </flux:button>
                    @endif
            </div>
        </flux:card>



        {{-- 2. Roles List --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">Existing Roles ({{ $roles->total() }})</flux:heading>

            <flux:table>
                <flux:table.rows>
                    @foreach ($roles as $role)
                        <flux:table.row :class="$selectedRoleId == $role->id ? '!bg-zinc-100' : ''">
                            <flux:table.cell>
                                <div>

                                   {{ Str::title(str_replace('-', ' ', $role->name)) }}

                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                @if ($role->name !== 'super-admin')
                                    @if (auth('admin')->user()?->can('roles.edit'))
                                        <flux:button variant="ghost" icon="pencil-square" size="sm"
                                            class="text-zinc-400 hover:text-blue-500 hover:bg-blue-50"
                                            wire:click="editRole({{ $role->id }})" />
                                    @endif
                                    @if (auth('admin')->user()?->can('roles.delete'))




                                <flux:button
                                    variant="ghost"
                                    icon="trash"
                                    size="sm"
                                    title="Delete Role"
                                    class="text-zinc-400 hover:text-red-500 hover:bg-red-50"
                                    wire:click="confirmDelete({{ $role->id }})" />


                                <flux:modal name="confirm-delete-role" title="Are you sure?">
                                    <p>You are about to delete <strong>{{ $roleNameBeingDeleted }}</strong>. This action cannot be undone.</p>
                                    <div class="flex gap-2 justify-end mt-4">
                                        <flux:button variant="ghost" x-on:click="$flux.modal('confirm-delete-role').close()">
                                            Cancel
                                        </flux:button>
                                        <flux:button variant="danger" wire:click="deleteRoleConfirmed({{ $roleIdBeingDeleted }})">
                                            Confirm Delete
                                        </flux:button>
                                    </div>
                                </flux:modal>



                                    @endif
                                @else
                                    <flux:badge color="zinc" icon="lock-closed">Locked</flux:badge>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            @if($roles->total() > 10)
            <div class="mt-4">
                <div class="mt-4 flex items-center justify-between gap-2">
                    <flux:button wire:click="previousPage" :disabled="$roles->onFirstPage()" variant="ghost"
                        size="sm" icon="chevron-left" />

                    <div class="flex gap-1">
                        @for ($i = 1; $i <= $roles->lastPage(); $i++)
                            <flux:button wire:click="gotoPage({{ $i }})" size="sm"
                                variant="{{ $roles->currentPage() == $i ? 'primary' : 'ghost' }}">
                                {{ $i }}
                            </flux:button>
                        @endfor
                    </div>

                    <flux:button wire:click="nextPage" :disabled="$roles->hasMorePages() === false" variant="ghost"
                        size="sm" icon="chevron-right" />
                </div>
            </div>
            @endif
        </flux:card>



    </div>

    {{-- Right Column: Permissions Matrix --}}
    <div class="lg:col-span-2">
        <flux:card class="h-full">
            <flux:heading size="md" class="mb-4">Assigned Permissions</flux:heading>
            @include('livewire.admin.cms.roles.assigned_permissions')
        </flux:card>
    </div>
</div>
</div>
