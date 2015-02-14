<?php namespace Modules\User\Commands\Handlers;

use Illuminate\Support\Facades\Event;
use Modules\Core\Contracts\Authentication;
use Modules\User\Events\UserHasRegistered;
use Modules\User\Repositories\RoleRepository;

class RegisterNewUserCommandHandler
{
    protected $input;

    /**
     * @var Authentication
     */
    private $auth;
    /**
     * @var RoleRepository
     */
    private $role;

    public function __construct(Authentication $auth, RoleRepository $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    /**
     * Handle the command
     *
     * @param $input
     * @return mixed
     */
    public function handle($input)
    {
        $this->input = $input;

        $user = $this->createUser();

        $this->assignUserToUsersGroup($user);

        event(new UserHasRegistered($user));

        return $user;
    }

    private function createUser()
    {
        return $this->auth->register((array) $this->input);
    }

    private function assignUserToUsersGroup($user)
    {
        $role = $this->role->findByName('User');

        $this->auth->assignRole($user, $role);
    }
}
