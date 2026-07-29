<?php
namespace App\Services\Users;
use App\Models\User;
use App\Traits\Upload;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use Upload;

    public function save(?int $id, array $data, $avatar = null, $existingAvatar = null, ?string $uploadDir = null): ?string
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (empty($data['mobile'])) {
            $data['mobile'] = null;
        }

        if ($avatar instanceof \Illuminate\Http\UploadedFile) {
            $data['avatar'] = $this->upload($avatar, $uploadDir ?? 'users/avatars', $existingAvatar);
        }

        if ($id) {
            $user = User::findOrFail($id);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        if (!empty($data['selectedRoles'])) {
            $user->roles()->sync(collect($data['selectedRoles'])->filter()->map(fn($id) => (int) $id)->toArray());
        }

        return $user->avatar;
    }
}
