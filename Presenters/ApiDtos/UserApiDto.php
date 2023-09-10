<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class UserApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $fullName;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $lastName;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $timezone;
    /**
     * @var string
     */
    public $languageCode;
    /**
     * @var int|null
     */
    public $currentCreditCount;
    /**
     * @var int[]
     */
    public $groupIds = [];
    /**
     * @var string
     */
    public $reservationColor;

    /**
     * @param UserDto[] $users
     * @param GroupItemView[] $groupList
     * @param bool $hideDetails
     * @param bool $showName
     * @param bool $hideEmail
     * @param int $currentUserId
     * @return UserApiDto[]
     */
    public static function FromList($users, $groupList, $hideDetails, $showName, $hideEmail, $currentUserId): array
    {
        $private = Resources::GetInstance()->GetString('Private');

        $limitedGroups = [];
        foreach ($groupList as $group) {
            if ($group->LimitedOnReservation()) {
                $limitedGroups[] = $group->Id();
            }
        }

        $dtos = [];
        foreach ($users as $user) {

            $showUser = $currentUserId == $user->Id() || (empty($groupList) || self::GroupIsShown($user, $limitedGroups));
            if (!$showUser) {
                continue;
            }

            $dto = new UserApiDto();
            $dto->id = intval($user->UserId);
            $dto->fullName = ($hideDetails && !$showName) ? $private : apidecode($user->FullName());
            $dto->firstName = ($hideDetails && !$showName) ? $private : apidecode($user->FirstName) . '';
            $dto->lastName = ($hideDetails && !$showName) ? $private : apidecode($user->LastName) . '';
            $dto->email = ($hideDetails) ? $private : apidecode($user->EmailAddress) . '';
            if ($hideEmail) {
                $dto->email = "";
            }
            $dto->timezone = $user->Timezone;
            $dto->languageCode = $user->LanguageCode;
            $dto->groupIds = array_map('intval', $user->GroupIds());
            $dto->reservationColor = apidecode($user->GetPreference(UserPreferences::RESERVATION_COLOR));
//			$dto->currentCreditCount = floatval($user->CurrentCreditCount);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * @param UserDto $user
     * @param int[] $groupList
     * @return bool
     */
    private static function GroupIsShown(UserDto $user, $groupList)
    {
        if (empty($groupList)) {
            return true;
        }

        foreach ($user->GroupIds() as $gid) {
            if (in_array($gid, $groupList)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param UserItemView $users
     * @return UserApiDto
     */
    public static function FromUserItemView(UserItemView $user, $hideUserEmail): UserApiDto
    {
        $dto = new UserApiDto();
        $dto->id = intval($user->Id);
        $dto->fullName = apidecode(FullName::AsString($user->First, $user->Last));
        $dto->firstName = apidecode($user->First);
        $dto->lastName = apidecode($user->Last);
        $dto->email = $hideUserEmail ? "" : apidecode($user->Email);
        $dto->timezone = $user->Timezone;
        $dto->languageCode = $user->Language;
        return $dto;
    }

    public static function FromUserDto(UserDto $user): UserApiDto {
        $dto = new UserApiDto();
        $dto->id = intval($user->UserId);
        $dto->fullName = $user->FullName;
        $dto->firstName = $user->FirstName;
        $dto->lastName = $user->LastName;
        $dto->email = $user->EmailAddress;
        $dto->timezone = $user->Timezone;
        $dto->languageCode = $user->Language();
        return $dto;
    }
}
