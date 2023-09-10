<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/OAuthClient.php');

interface IOAuthRepository
{
    public function LoadByPublicId($publicId): ?OAuthClient;

    /**
     * @return array|OAuthClient[]
     */
    public function LoadAll(): array;

    public function Add(OAuthClient $client): int;

    public function Update(OAuthClient $client);

    public function DeleteById(int $id);
}

class OAuthRepository implements IOAuthRepository
{
    public function LoadByPublicId($publicId): ?OAuthClient
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetOAuthProviderByPublicIdCommand($publicId));
        if ($row = $reader->GetRow()) {
            return OAuthClient::FromRow($row);
        }
        return null;
    }

    public function LoadAll(): array
    {
        $items = [];
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllOAuthProvidersCommand());
        while ($row = $reader->GetRow()) {
            $items[] = OAuthClient::FromRow($row);
        }

        return $items;
    }

    public function Add(OAuthClient $client): int
    {
        $addCommand = new AddOAuthProviderCommand(
            $client->GetPublicId(),
            $client->GetName(),
            $client->GetClientId(),
            $client->GetClientSecret(),
            $client->GetAccessTokenGrant(),
            $client->GetUrlAuthorize(),
            $client->GetUrlAccessToken(),
            $client->GetUrlUserDetails(),
            $client->GetFieldMappings()->ToJson(),
            $client->GetScope(),
            $client->GetDateCreated(),
        );
        $id = ServiceLocator::GetDatabase()->ExecuteInsert($addCommand);

        $client->SetId($id);

        return intval($id);
    }

    public function Update(OAuthClient $client)
    {
        $addCommand = new UpdateOAuthProviderCommand(
            $client->GetId(),
            $client->GetName(),
            $client->GetClientId(),
            $client->GetClientSecret(),
            $client->GetAccessTokenGrant(),
            $client->GetUrlAuthorize(),
            $client->GetUrlAccessToken(),
            $client->GetUrlUserDetails(),
            $client->GetFieldMappings()->ToJson(),
            $client->GetScope(),
        );
        ServiceLocator::GetDatabase()->Execute($addCommand);
    }

    public function DeleteById(int $id)
    {
        $deleteCommand = new DeleteOAuthProviderCommand($id);
        ServiceLocator::GetDatabase()->Execute($deleteCommand);
    }
}