<?php

namespace App\Auth;

use App\Models\LoginSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Session;

/**
 * Class CustomGuard
 *
 * @package App\Auth
 */
class CustomGuard extends SessionGuard
{
    /** {@inheritDoc} */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        $this->fireAttemptEvent($credentials, $remember, $login);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials) && $this->checkLoginSession($user)) {
            if ($login) {
                $this->login($user, $remember);
            }

            return true;
        }

        if ($login) {
            $this->fireFailedEvent($user, $credentials);
        }

        return false;
    }

    protected function checkLoginSession(User $user)
    {
        $branch = $user->branch;

        if (!$branch->isActive() || !$branch->isLicensed()) {
            Session::flush();
            Session::flash('flashes.error', "Cannot login to branch '{$branch->name}'");

            return false;
        }

        if ($user->role === 'cashier') {
            $currentlyLoggedInSessions = $branch->currentlyLoggedInSessions()->get()->filter(function (LoginSession $session) { return $session->user->role === 'cashier'; });
            $currentlyLoggedInSession  = $currentlyLoggedInSessions->filter(function (LoginSession $session) use ($user) { return (int) $session->user_id === (int) $user->id; })->first();

            if ($currentlyLoggedInSession) {
                $currentlyLoggedInSession->logged_out_at = Carbon::now();
                $currentlyLoggedInSession->saveOrFail();
            } elseif ($currentlyLoggedInSessions->count() >= $branch->cash_counters_count) {
                Session::flush();
                Session::flash('flashes.error', 'Branch has reached max logged in cashier');

                return false;
            }

            return true;
        }

        return true;
    }
}