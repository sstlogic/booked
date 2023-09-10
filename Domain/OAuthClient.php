<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

use League\OAuth2\Client\Provider\GenericProvider;

require_once(ROOT_DIR . 'vendor/autoload.php');

class OAuthClient
{
    private BookedOAuthProvider $provider;

    private int $id;
    private string $publicId;
    private string $name;
    private string $clientId;
    private string $clientSecret;
    private string $urlAuthorize;
    private string $urlAccessToken;
    private string $urlUserDetails;
    private string $accessTokenGrant;
    private string $scope;
    private Date $dateCreated;
    private OAuthFieldMappings $fieldMappings;

    public static function CreateNew(): OAuthClient
    {
        $client = new OAuthClient();
        $client->SetPublicId(BookedStringHelper::Random(20));
        $client->dateCreated = Date::Now();
        return $client;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function SetId($id)
    {
        $this->id = intval($id);
    }

    public function GetPublicId()
    {
        return $this->publicId;
    }

    public function SetPublicId($publicId)
    {
        $this->publicId = $publicId;
    }

    public function GetName()
    {
        return $this->name;
    }

    public function SetName($name)
    {
        $this->name = $name;
    }

    public function GetClientId()
    {
        return $this->clientId;
    }

    public function SetClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function GetClientSecret()
    {
        return $this->clientSecret;
    }

    public function SetClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    public function GetUrlAuthorize()
    {
        return $this->urlAuthorize;
    }

    public function SetUrlAuthorize($urlAuthorize)
    {
        $this->urlAuthorize = $urlAuthorize;
    }

    public function GetUrlAccessToken()
    {
        return $this->urlAccessToken;
    }

    public function SetUrlAccessToken($urlAccessToken)
    {
        $this->urlAccessToken = $urlAccessToken;
    }

    public function GetUrlUserDetails()
    {
        return $this->urlUserDetails;
    }

    public function SetUrlUserDetails($urlUserDetails)
    {
        $this->urlUserDetails = $urlUserDetails;
    }

    public function GetAccessTokenGrant()
    {
        return $this->accessTokenGrant;
    }

    public function SetAccessTokenGrant($accessTokenGrant)
    {
        $this->accessTokenGrant = $accessTokenGrant;
    }

    public function GetFieldMappings()
    {
        return $this->fieldMappings;
    }

    public function SetFieldMappings($fieldMappings)
    {
        $this->fieldMappings = $fieldMappings;
    }

    public function GetDateCreated(): Date
    {
        return $this->dateCreated;
    }

    private function GetProvider(): BookedOAuthProvider
    {
        if (empty($this->provider)) {
            $this->provider = new BookedOAuthProvider([
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'redirectUri' => Configuration::Instance()->GetScriptUrl() . '/integrate/oauth.php',
                'urlAuthorize' => $this->urlAuthorize,
                'urlAccessToken' => $this->urlAccessToken,
                'urlResourceOwnerDetails' => $this->urlUserDetails,
            ]);
        }
        return $this->provider;
    }

    public static function FromRow(array $row): OAuthClient
    {
        $client = new OAuthClient();
        $client->id = intval($row[ColumnNames::OAUTH_PROVIDER_ID]);
        $client->publicId = $row[ColumnNames::PUBLIC_ID];
        $client->name = $row[ColumnNames::OAUTH_PROVIDER_NAME];
        $client->clientId = $row[ColumnNames::OAUTH_CLIENT_ID];
        $client->clientSecret = $row[ColumnNames::OAUTH_CLIENT_SECRET];
        $client->urlAuthorize = $row[ColumnNames::OAUTH_URL_AUTHORIZE];
        $client->urlAccessToken = $row[ColumnNames::OAUTH_URL_ACCESS_TOKEN];
        $client->urlUserDetails = $row[ColumnNames::OAUTH_URL_USER_DETAILS];
        $client->accessTokenGrant = $row[ColumnNames::OAUTH_ACCESS_TOKEN_GRANT];
        $client->fieldMappings = OAuthFieldMappings::FromJson($row[ColumnNames::OAUTH_FIELD_MAPPINGS]);
        $client->scope = $row[ColumnNames::OAUTH_SCOPE];
        $client->dateCreated = Date::FromDatabase($row[ColumnNames::DATE_CREATED]);

        return $client;
    }

    public function GetState($resumeUrl): string
    {
        $state = OAuthState::CreateJson($this->publicId, $resumeUrl);
        return base64_encode($state);
    }

    public function GetAuthorizationUrl($state): string
    {
        return $this->GetProvider()->getAuthorizationUrl(['state' => $state, 'scope' => $this->GetScope()]);
    }

    public function GetUser($code): OAuthProfile
    {
        $token = $this->GetProvider()->getAccessToken($this->GetAuthGrant(), ['code' => $code]);
        $resourceOwner = $this->GetProvider()->getResourceOwner($token);

        $fields = $resourceOwner->toArray();

        $email = $this->fieldMappings->GetEmail($fields);
        $fname = $this->fieldMappings->GetFirstName($fields);
        $lname = $this->fieldMappings->GetLastName($fields);

        if (empty($email)) {
            Log::Error("Email not found in OAuth user", ['fields' => $fields]);
            throw new Exception("Email not found in OAuth User response");
        }

        return new OAuthProfile($email, $fname, $lname);
    }

    private function GetAuthGrant(): string
    {
        return empty($this->accessTokenGrant) ? 'authorization_code' : $this->accessTokenGrant;
    }

    public function GetScope(): string
    {
        return $this->scope;
    }

    public function SetScope($scope)
    {
        $this->scope = preg_replace('/\s+/', ' ', trim($scope));
    }
}

class OAuthProfile
{
    private string $email;
    private ?string $firstName;
    private ?string $lastName;

    public function __construct(string $email, ?string $firstName, ?string $lastName)
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function Email(): string
    {
        return $this->email;
    }

    public function FirstName(): ?string
    {
        return $this->firstName;
    }

    public function LastName(): ?string
    {
        return $this->lastName;
    }
}

class OAuthState
{
    /**
     * @var string
     */
    public $providerId;
    /**
     * @var string|null
     */
    public $resumeUrl;
    /**
     * @var string
     */
    public $key;

    public static function CreateJson($providerId, $resumeUrl): string
    {
        $state = new OAuthState();
        $state->providerId = $providerId;
        $state->resumeUrl = $resumeUrl;
        $state->key = BookedStringHelper::Random();
        return json_encode($state);
    }

    public static function CreateFromQueryString(?string $state)
    {
        if (!empty($state)) {
            $decoded = json_decode(base64_decode($state));
            $dto = new OAuthState();
            $dto->resumeUrl = $decoded->resumeUrl;
            $dto->providerId = $decoded->providerId;
            $dto->key = $decoded->key;
            return $dto;
        }

        return new OAuthState();
    }
}

class OAuthFieldMappings
{
    public string $email;
    public string $givenName;
    public string $surName;

    public static function Create($email, $givenName, $surName): OAuthFieldMappings
    {
        $dto = new OAuthFieldMappings();
        $dto->email = $email;
        $dto->givenName = $givenName;
        $dto->surName = $surName;
        return $dto;
    }

    public static function FromJson($json): OAuthFieldMappings
    {
        $decoded = json_decode($json);
        $dto = new OAuthFieldMappings();
        $dto->email = $decoded->email;
        $dto->givenName = $decoded->givenName;
        $dto->surName = $decoded->surName;
        return $dto;
    }

    public function GetEmail(array $fields): ?string
    {
        return $this->GetNestedFieldValue($this->email, $fields);
    }

    public function GetFirstName(array $fields): ?string
    {
        return $this->GetNestedFieldValue($this->givenName, $fields);
    }

    public function GetLastName(array $fields): ?string
    {
        return $this->GetNestedFieldValue($this->surName, $fields);
    }

    private function GetNestedFieldValue($path, array $fields): ?string
    {
        if (BookedStringHelper::Contains($path, '.')) {
            $paths = explode('.', $path);
            foreach ($paths as $path) {
                if (array_key_exists($path, $fields)) {
                    $fields = $fields[$path];
                } else {
                    return null;
                }
            }

            return $fields;
        }
        if (array_key_exists($this->email, $fields)) {
            return $fields[$this->email];
        }

        return null;
    }

    public function ToJson(): string
    {
        return json_encode($this);
    }
}

class BookedOAuthProvider extends GenericProvider
{
    public function getResourceOwnerDetailsUrl($token)
    {
        return str_replace('{accessToken}', $token, parent::getResourceOwnerDetailsUrl($token));
    }

    protected function createRequest($method, $url, $token, array $options)
    {
        $request = parent::createRequest($method, $url, $token, $options);
        $matches = [];
        $isMatch = preg_match("/(https?):\/\/(\w+):(\w+)(@)/", $url, $matches);
        if ($isMatch !== false && count($matches) > 3) {
            $authHeader = 'Basic ' . base64_encode($matches[2] . ':' . $matches[3]);
            return $request->withHeader('Authorization', $authHeader);
        }

        return $request;
    }
}