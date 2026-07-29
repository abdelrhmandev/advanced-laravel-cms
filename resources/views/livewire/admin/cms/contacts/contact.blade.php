<div class="space-y-6">
    <div class="flex items-center justify-between">

        <div>
            <flux:heading size="xl">{{ __('Contacts') }}</flux:heading>
            <flux:subheading>{{ __('Manage Contacts') }} ({{ $contacts->total() }})</flux:subheading>
        </div>
    </div>

    {{-- Table --}}
    <flux:card class="p-0 overflow-hidden" wire:loading.class="opacity-50 pointer-events-none">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <th class="px-4 py-3 text-start">
                        {{ __('Name') }}
                    </th>
                    <th class="px-4 py-3 text-start">
                        {{ __('Email') }}
                    </th>



                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Subject') }}
                    </th>
                    <th class="px-4 py-3 text-start">
                        {{ __('Message') }}
                    </th>
                    <th class="px-4 py-3 text-start">
                        {{ __('Created at') }}
                    </th>
                    <th class="px-4 py-3 text-end font-medium text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse ($contacts as $contact)
                    <tr wire:key="contact-{{ $contact->id }}"
                        class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">

                        {{-- Name + Email --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                {{ $contact->name }}
                            </div>
                        </td>



                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            {{ $contact->email }}
                        </td>


                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            {{ $contact->subject }}
                        </td>


                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 max-w-xs">

                            @if (strlen($contact->message) > 80)
                                <div class="flex items-center justify-between gap-2">


                                    <span class="line-clamp-1">
                                        {{ Str::limit($contact->message, 60) }}
                                    </span>

                                    <flux:button wire:click="viewMessage({{ $contact->id }})" size="sm"
                                        variant="ghost" class="shrink-0">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </flux:button>

                                </div>
                            @else
                                {{ $contact->message }}
                            @endif

                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-3 text-xs text-zinc-400">
                             <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $contact->created_at->diffForHumans() }}
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    {{ $contact->created_at->format('d M, Y - h:i A') }}
                                </span>
                            </div>
                        </td>



                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">

                                <?php
                                $auth = auth('admin')->user();
                                $canDelete = $auth->can('contacts.delete');
                                ?>

                                @if ($canDelete)
                                    <flux:button wire:click="confirmDelete({{ $contact->id }})" size="sm"
                                        variant="ghost" icon="trash" class="text-red-500" />
                                @endif

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-zinc-400">
                            No Contacts Added Yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </flux:card>

    {{-- Pagination --}}
    <div>{{ $contacts->links() }}</div>

    {{-- Delete Confirm Modal --}}
    <flux:modal name="confirm-delete-contact" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Contact') }}</flux:heading>
                <flux:subheading>
                    {{ __('Are you sure you want to delete this contact? This action cannot be undone.') }}
                </flux:subheading>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteContact" variant="danger">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    <flux:modal wire:model="showMessageModal" maxWidth="lg">

        <div class="p-6 space-y-4">

            {{-- Header --}}
            <div>
                <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                    Message Details
                </h2>
            </div>

            {{-- Info --}}
            @if ($selectedMessage)
                <div class="space-y-2 text-sm">

                    <div>
                        <span class="font-medium">Name:</span>
                        {{ $selectedMessage->name }}
                    </div>

                    <div>
                        <span class="font-medium">Email:</span>
                        {{ $selectedMessage->email }}
                    </div>

                    <div>
                        <span class="font-medium">Subject:</span>
                        {{ $selectedMessage->subject }}
                    </div>

                </div>

                {{-- Message --}}
                <div
                    class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-sm text-zinc-600 dark:text-zinc-300 whitespace-pre-line">
                    {{ $selectedMessage->message }}
                </div>
            @endif

            {{-- Footer --}}
            <div class="flex justify-end">
                <flux:button wire:click="closeModal" variant="primary">
                    Close
                </flux:button>
            </div>

        </div>

    </flux:modal>

</div>
