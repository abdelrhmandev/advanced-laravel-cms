<?php
namespace App\Livewire\Admin\CMS\Contacts;
use App\Models\Contact;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout('layouts.admin.app')]
#[Title('Contacts')]
class ContactManager extends Component
{
    public ?int $deletingId = null;

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        Flux::modal('confirm-delete-contact')->show();
    }

    public $selectedMessage = null;
public $showMessageModal = false;

public function viewMessage($id)
{
    $contact = Contact::findOrFail($id);

    $this->selectedMessage = $contact;
    $this->showMessageModal = true;
}

public function closeModal()
{
    $this->reset(['selectedMessage', 'showMessageModal']);
}

    public function render()
    {
        return view('livewire.admin.cms.contacts.contact', [
            'contacts' => Contact::paginate(10),
        ]);
    }

    public function deleteContact(): void
    {
        if (!$this->deletingId) {
            return;
        }

        Contact::findOrFail($this->deletingId)->delete();
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Contact deleted successfully.');
        Flux::modal('confirm-delete-contact')->close();
    }
}
