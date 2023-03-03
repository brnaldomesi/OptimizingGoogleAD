<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('account-syncs.{account_id}', function ($user, $account_id) {
    return in_array($account_id, $user->accounts->pluck('id')->toArray());
});
