<?php

namespace App\Rules;

use App\Models\User;
use App\Models\UserIdentifier;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueUserIdentifier implements ValidationRule
{
    protected $type;

    protected $userId;

    protected $excludeId;

    /**
     * Create a new rule instance.
     *
     * @param  string  $type  The type of identifier (email, phone, username)
     * @param  int|null  $userId  The user ID to exclude from uniqueness check (for updates)
     * @param  int|null  $excludeId  The identifier ID to exclude (for updates)
     */
    public function __construct(string $type, ?int $userId = null, ?int $excludeId = null)
    {
        $this->type = $type;
        $this->userId = $userId;
        $this->excludeId = $excludeId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        // Check in user_identifiers table
        $identifierQuery = UserIdentifier::where('type', $this->type)
            ->where('value', $value);

        // If updating, exclude the current user's identifiers
        if ($this->userId) {
            $identifierQuery->where('user_id', '!=', $this->userId);
        }

        // If updating a specific identifier, exclude it
        if ($this->excludeId) {
            $identifierQuery->where('id', '!=', $this->excludeId);
        }

        if ($identifierQuery->exists()) {
            $fail("This {$this->type} is already used by another user.");

            return;
        }

        // Check in users table (for email and phone only)
        if (in_array($this->type, ['email', 'phone'])) {
            $userQuery = User::where($this->type, $value);

            // If updating, exclude the current user
            if ($this->userId) {
                $userQuery->where('id', '!=', $this->userId);
            }

            if ($userQuery->exists()) {
                $fail("This {$this->type} is already used by another user.");

                return;
            }
        }
    }
}
