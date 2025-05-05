<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Chỉ admin mới có quyền xem danh sách tất cả người dùng
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin có thể xem bất kỳ user nào
        // Member chỉ có thể xem thông tin của chính mình
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Chỉ admin mới có quyền tạo user mới
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin có thể cập nhật thông tin bất kỳ user nào
        // Member chỉ có thể cập nhật thông tin của chính mình
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Chỉ admin mới có quyền xóa user
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Chỉ admin mới có quyền khôi phục user
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Chỉ admin mới có quyền xóa vĩnh viễn user
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can generate QR code.
     */
    public function generateQrCode(User $user, User $model): bool
    {
        // Chỉ admin mới có quyền tạo QR code mới
        return $user->isAdmin();
    }
}
