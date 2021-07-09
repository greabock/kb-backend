<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Auth\Azure\Login;

use App\Models\User;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class Action
{
    public function __invoke(): JsonResponse
    {
        //TODO: причесать этот ужас

        /** @var \SocialiteProviders\Manager\OAuth2\User $azureUser */
        $azureUser = Socialite::driver('azure')->stateless()->user();

        $user = User::where('login', $azureUser->getEmail())
            ->orWhere('login', $azureUser->getName())
            ->first();

        if (!$user) {
            /** @var User $user */
            $user = User::make([
                'login' => $azureUser->getEmail(),
                'name' => sprintf('%s %s', $azureUser->getRaw()['givenName'], $azureUser->getRaw()['surname']),
                'password' => Str::random(10),
            ]);

            $user->save();
            $user->role = 'user';
        }

        if (!$user->photo) {
            $http = new HttpClient();

            $tenant = config('services.azure.tenant');

            try {
                $result = $http->request('get', "https://graph.windows.net/$tenant/me/thumbnailPhoto?api-version=1.5", [
                    'headers' => ['Authorization' => 'Bearer ' . $azureUser->token]
                ]);

                $file = $user->id . '.' . explode('/', $result->getHeader('content-type')[0])[1];

                Storage::disk('local')->put('users/' . $file, $result->getBody());
                $user->photo = 'users/' . $file;

            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() !== 404) {
                    throw $e;
                }
            }
        }

        $user->name = sprintf('%s %s', $azureUser->getRaw()['givenName'], $azureUser->getRaw()['surname']);
        $user->email = $azureUser->getEmail();
        $user->login = $azureUser->getEmail();

        $user->save();

        return response()->json(['data' => $user->createToken('web')->plainTextToken]);
    }

}
