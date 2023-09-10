<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageOAuthPage.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class ManageOAuthPresenter extends ActionPresenter
{
    private IManageOAuthPage $page;
    private IOAuthRepository $repository;

    public function __construct(IManageOAuthPage $page, IOAuthRepository $repository)
    {
        $this->page = $page;
        $this->repository = $repository;

        parent::__construct($page);

        $this->AddApi('load', 'LoadProviders');
        $this->AddApi('add', 'AddProvider');
        $this->AddApi('update', 'UpdateProvider');
        $this->AddApi('delete', 'DeleteProvider');
    }

    public function LoadProviders(): ApiActionResult
    {
        $providers = $this->repository->LoadAll();
        return new ApiActionResult(true, ['providers' => array_map('OAuthClientApiDto::FromClient', $providers)]);
    }

    public function AddProvider($json): ApiActionResult
    {
        /** @var OAuthClientApiDto $dto */
        $dto = $json;

        $client = $this->UpdateFromJson($dto, OAuthClient::CreateNew());
        $this->repository->Add($client);

        Log::Debug("Adding new oauth provider.", ['name' => $client->GetName(), 'id' => $client->GetPublicId()]);

        return new ApiActionResult(true, OAuthClientApiDto::FromClient($client));
    }

    public function UpdateProvider($json): ApiActionResult
    {
        /** @var OAuthClientApiDto $dto */
        $dto = $json;

        $client = $this->repository->LoadByPublicId($dto->publicId);
        if (empty($client)) {
            return new ApiActionResult(false, null, new ApiErrorList("Not found"));
        }

        $client = $this->UpdateFromJson($dto, $client);
        $this->repository->Update($client);

        Log::Debug("Updating oauth provider.", ['name' => $client->GetName(), 'publicId' => $client->GetPublicId()]);

        return new ApiActionResult(true, OAuthClientApiDto::FromClient($client));
    }

    public function DeleteProvider($json): ApiActionResult
    {
        $providerId = intval($json->id);
        Log::Debug('Deleting oauth provider', ['id' => $providerId]);

        $this->repository->DeleteById($providerId);

        return new ApiActionResult(true, ["id" => $providerId]);
    }

    /**
     * @param OAuthClientApiDto $dto
     * @return OAuthClient
     */
    private function UpdateFromJson($dto, OAuthClient $client): OAuthClient
    {
        $client->SetName(apiencode($dto->name));
        $client->SetClientId(apiencode($dto->clientId));
        $client->SetClientSecret(apiencode($dto->clientSecret));
        $client->SetAccessTokenGrant(apiencode($dto->accessTokenGrant));
        $client->SetUrlAccessToken(apiencode($dto->urlAccessToken));
        $client->SetUrlAuthorize(apiencode($dto->urlAuthorize));
        $client->SetUrlUserDetails(apiencode($dto->urlUserDetails));
        $client->SetScope(apiencode($dto->scope));
        $client->SetFieldMappings(OAuthFieldMappings::Create(apiencode($dto->fieldMappings->email), apiencode($dto->fieldMappings->givenName), apiencode($dto->fieldMappings->surName)));
        return $client;
    }
}