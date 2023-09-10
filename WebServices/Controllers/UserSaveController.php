<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'WebServices/Requests/User/CreateUserRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/User/UpdateUserRequest.php');
require_once(ROOT_DIR . 'WebServices/Validators/UserRequestValidator.php');
require_once(ROOT_DIR . 'lib/Application/User/namespace.php');

interface IUserSaveController
{
    /**
     * @param CreateUserRequest $request
     * @param WebServiceUserSession $session
     * @return UserControllerResult
     */
    public function Create($request, $session);

    /**
     * @param int $userId
     * @param UpdateUserRequest $request
     * @param WebServiceUserSession $session
     * @return UserControllerResult
     */
    public function Update($userId, $request, $session);

    /**
     * @param int $userId
     * @param UpdateUserStatusRequest $request
     * @param WebServiceUserSession $session
     * @return UserControllerResult
     */
    public function UpdateStatus($userId, $request, $session);

    /**
     * @param int $userId
     * @param WebServiceUserSession $session
     * @return UserControllerResult
     */
    public function Delete($userId, $session);

    /**
     * @param int $userId
     * @param string $password
     * @param WebServiceUserSession $session
     * @return UserControllerResult
     */
    public function UpdatePassword($userId, $password, $session);
}

class UserSaveController implements IUserSaveController
{
    /**
     * @var IManageUsersServiceFactory
     */
    private $serviceFactory;

    /**
     * @var IUserRequestValidator
     */
    private $requestValidator;

    public function __construct(IManageUsersServiceFactory $serviceFactory, IUserRequestValidator $requestValidator)
    {
        $this->serviceFactory = $serviceFactory;
        $this->requestValidator = $requestValidator;
    }

    public function Create($request, $session)
    {
        $errors = $this->requestValidator->ValidateCreateRequest($request);

        if (!empty($errors)) {
            return new UserControllerResult(null, $errors);
        }

        $userService = $this->serviceFactory->CreateAdmin();

        $phoneCountryCode = CountryCodes::Get($request->phoneCountryCode, $request->phone, $request->language);

        $extraAttributes = [
            UserAttribute::Phone => apiencode($request->phone),
            UserAttribute::Organization => apiencode($request->organization),
            UserAttribute::Position => apiencode($request->position),
            UserAttribute::PhoneCountryCode => apiencode($phoneCountryCode->code)];
        $customAttributes = array();
        foreach ($request->GetCustomAttributes() as $attribute) {
            $customAttributes[] = new AttributeValue($attribute->attributeId, apiencode($attribute->attributeValue));
        }

        $user = $userService->AddUser(
            apiencode($request->userName),
            apiencode($request->emailAddress),
            apiencode($request->firstName),
            apiencode($request->lastName),
            $request->password,
            $request->timezone,
            $request->language,
            Pages::DEFAULT_HOMEPAGE_ID,
            $extraAttributes,
            $customAttributes,
            false,
            $request->reservationColor);

        $userService->ChangeGroups($user, $request->groups);

        return new UserControllerResult($user->Id());
    }

    public function Update($userId, $request, $session)
    {
        $errors = $this->requestValidator->ValidateUpdateRequest($userId, $request);

        if (!empty($errors)) {
            return new UserControllerResult(null, $errors);
        }

        $userService = $this->serviceFactory->CreateAdmin();

        $phoneCountryCode = CountryCodes::Get($request->phoneCountryCode, $request->phone, $request->language);

        $extraAttributes = [
            UserAttribute::Phone => apiencode($request->phone),
            UserAttribute::Organization => apiencode($request->organization),
            UserAttribute::Position => apiencode($request->position),
            UserAttribute::PhoneCountryCode => apiencode($phoneCountryCode->code),
        ];

        $customAttributes = array();
        foreach ($request->GetCustomAttributes() as $attribute) {
            $customAttributes[] = new AttributeValue($attribute->attributeId, apiencode($attribute->attributeValue));
        }

        $user = $userService->UpdateUser(
            $userId,
            apiencode($request->userName),
            apiencode($request->emailAddress),
            apiencode($request->firstName),
            apiencode($request->lastName),
            apiencode($request->timezone),
            $extraAttributes,
            $customAttributes,
            false,
            $request->reservationColor);

        $userService->ChangeGroups($user, $request->groups);

        return new UserControllerResult($userId);
    }

    public function UpdateStatus($userId, $request, $session)
    {
        $errors = [];

        if (!in_array($request->statusId, [AccountStatus::ACTIVE, AccountStatus::INACTIVE])) {
            $errors[] = 'Invalid account status';
        }

        if (!empty($errors)) {
            return new UserControllerResult(null, $errors);
        }

        $userService = $this->serviceFactory->CreateAdmin();
        $userService->ChangeStatus($userId, $request->statusId);

        return new UserControllerResult($userId);
    }

    public function Delete($userId, $session)
    {
        $userService = $this->serviceFactory->CreateAdmin();
        $userService->DeleteUser($userId);

        return new UserControllerResult($userId);
    }

    public function UpdatePassword($userId, $password, $session)
    {
        $errors = $this->requestValidator->ValidateUpdatePasswordRequest($userId, $password);

        if (!empty($errors)) {
            return new UserControllerResult(null, $errors);
        }

        $userService = $this->serviceFactory->CreateAdmin();
        $userService->UpdatePassword($userId, $password);

        return new UserControllerResult($userId);
    }
}

class UserControllerResult
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var array|string[]
     */
    private $errors = array();

    /**
     * @param int $userId
     * @param array $errors
     */
    public function __construct($userId, $errors = array())
    {
        $this->userId = $userId;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function WasSuccessful()
    {
        return !empty($this->userId) && empty($this->errors);
    }

    /**
     * @return int
     */
    public function UserId()
    {
        return $this->userId;
    }

    /**
     * @return array|string[]
     */
    public function Errors()
    {
        return $this->errors;
    }
}
