final <?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Gets current user profile with their role
     *
     * @return SuccessResponse
     */
    public function show(): SuccessResponse
    {
        $user = auth()->user();
        return new SuccessResponse($user->load('role'), 200);
    }

    /**
     * Edits current user's profile
     *
     * @param Request $request
     * @return SuccessResponse
     */
    public function update(Request $request): SuccessResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->id())],
            'password' => ['string', 'min:8'],
            'file' => ['nullable', 'image', 'max:10240']
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }
        unset($validated['file']);
        $user->update($validated);
        return new SuccessResponse($user, 200);
    }
}
