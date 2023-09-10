<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OAuthClientApiDto
{
    public int $id;
    public string $publicId;
    public string $name;
    public string $clientId;
    public string $clientSecret;
    public string $urlAuthorize;
    public string $urlAccessToken;
    public string $urlUserDetails;
    public string $accessTokenGrant;
    public string $scope;
    public OAuthFieldMappings $fieldMappings;
    public string $dateCreated;

    public static function FromClient(OAuthClient $provider): OAuthClientApiDto
    {
        $dto = new OAuthClientApiDto();
        $dto->id = intval($provider->GetId());
        $dto->publicId = apidecode($provider->GetPublicId());
        $dto->name = apidecode($provider->GetName());
        $dto->clientId = apidecode($provider->GetClientId());
        $dto->clientSecret = apidecode($provider->GetClientSecret());
        $dto->urlAuthorize = apidecode($provider->GetUrlAuthorize());
        $dto->urlAccessToken = apidecode($provider->GetUrlAccessToken());
        $dto->urlUserDetails = apidecode($provider->GetUrlUserDetails());
        $dto->accessTokenGrant = apidecode($provider->GetAccessTokenGrant());
        $dto->scope = apidecode($provider->GetScope());
        $dto->fieldMappings = $provider->GetFieldMappings();
        $dto->dateCreated = $provider->GetDateCreated()->ToSystem();
        return $dto;
    }
}