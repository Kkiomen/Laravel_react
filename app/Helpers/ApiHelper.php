<?php

namespace App\Helpers;

use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ApiHelper
{

    /**
     * Returns the OpenAI API key for the authenticated user.
     *
     * @return string|null The OpenAI API key for the authenticated user or null if not found.
     * @throws \Exception If the group's OpenAI API key is null and used as the API key for the user.
     */
    public static function getOpenAiApiKey(): ?string {
        try {
            $openAiKey = Crypt::decryptString((string) Auth::user()->open_ai_key);
        } catch (\Exception $e) {
            $openAiKey = null;
        }

        $groupUser = GroupUser::where('user_id', Auth::user()->id)->first();
        if ($groupUser) {
            if ($groupUser->used_api === 'group') {
                $group = Group::where('id', $groupUser->group_id)->first();
                if ($group) {
                    if (is_null($group->open_api_key)) {
                        throw new \Exception('Api Key Open Ai - is null');
                    }
                    $openAiKey = Crypt::decryptString((string) $group->open_api_key);
                }
            } else if ($groupUser->used_api === 'system') {
                $openAiKey = (string) getenv('OPEN_AI_KEY');
            }
        }

        return $openAiKey ?? null;
    }


    /**
     * Returns the Pinecone API key and URL for the authenticated user.
     *
     * @return array|null An array containing the Pinecone API key and URL or null if not found.
     * @throws \Exception If the group's Pinecone API key or URL is null and used as the API key or URL for the user.
     */
    public static function getPineconeApiKey(): ?array {
        try {
            $pineconeApiKey = Crypt::decryptString((string) Auth::user()->pinecone_api_key);
            $pineconeApiUrl = Crypt::decryptString((string) Auth::user()->pinecone_api_url);
        } catch (\Exception $e) {
            $pineconeApiKey = null;
            $pineconeApiUrl = null;
        }

        $groupUser = GroupUser::where('user_id', Auth::user()->id)->first();
        if ($groupUser) {
            if ($groupUser->used_long_term === 'group') {
                $group = Group::where('id', $groupUser->group_id)->first();
                if ($group) {
                    if (is_null($group->pinecone_api_key)) {
                        throw new \Exception('Api Key Pinecone - is null');
                    }

                    if (is_null($group->pinecone_api_url)) {
                        throw new \Exception('Api Url Pinecone - is null');
                    }
                    $pineconeApiKey = Crypt::decryptString((string) $group->pinecone_api_key);
                    $pineconeApiUrl = Crypt::decryptString((string) $group->pinecone_api_url);
                }
            } else if ($groupUser->used_long_term === 'system') {
                $pineconeApiKey = (string) getenv('PINECONE_API_KEY');
                $pineconeApiUrl = (string) getenv('PINECONE_INDEX_URL');
            }
        }

        $result = [
            'pinecone_api_key' => $pineconeApiKey ?? null,
            'pinecone_api_url' => $pineconeApiUrl ?? null
        ];

        return $result;
    }

    /**
     * Returns true if the user is authorized to use MemoryTerm, false otherwise.
     *
     * @return bool
     */
    public static function canUseMemoryTerm(): bool {
        $groupUser = GroupUser::where('user_id', Auth::user()->id)->first();
        if ($groupUser) {
            $group = Group::where('id', $groupUser->group_id)->first();
            if ($group) {


                if ($groupUser->used_long_term === 'group') {
                    if((empty($group->pinecone_api_key) || empty($group->pinecone_api_url))){
                        return false;
                    }
                }else if($groupUser->used_long_term === 'system'){
                    if (empty(getenv('PINECONE_API_KEY')) || empty(getenv('PINECONE_INDEX_URL'))) {
                        return false;
                    }
                }else if($groupUser->used_long_term === 'private'){
                    $user = Auth::user();
                    if (empty($user->pinecone_api_key) || empty($user->pinecone_api_url)) {
                        return false;
                    }
                }

                return true;
            }
        }else{
            $user = Auth::user();
            if (empty($user->pinecone_api_key) || empty($user->pinecone_api_url)) {
                return false;
            }
        }

        return false;
    }


    /**
     * Generate random string
     * @param int $length
     * @return string
     */
    public static function generateRandomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

}
