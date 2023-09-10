<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/UserSessionRepository.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class WebServiceApiUserAuthentication extends Authentication
{
    private $userRepository;

    public function __construct(IRoleService $roleService, IUserRepository $userRepository, IGroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        parent::__construct($roleService, $userRepository, $groupRepository);
    }

    public function Login($username, $loginContext)
    {
        $user = $this->userRepository->LoadByUsername($username);
        if ($user->GetIsApiOnly()) {
            return parent::Login($username, $loginContext);
        }
        return null;
    }

    public function Validate($username, $passwordPlainText)
    {
        $user = $this->userRepository->LoadByUsername($username);
        if ($user->GetIsApiOnly()) {
            return parent::Validate($username, $passwordPlainText);
        }
        return false;
    }
}