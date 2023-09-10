<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/Reservations/ManageReservationColorsPage.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');

class ManageReservationColorsPresenter extends ActionPresenter
{
    private IReservationRepository $reservationRepository;
    private IAttributeRepository $attributeRepository;

    public function __construct(IManageReservationColorsPage $page, IReservationRepository $reservationRepository, IAttributeRepository $attributeRepository)
    {
        parent::__construct($page);
        $this->reservationRepository = $reservationRepository;
        $this->attributeRepository = $attributeRepository;

        $this->AddApi('add', 'Add');
        $this->AddApi('update', 'Update');
        $this->AddApi('delete', 'Delete');
        $this->AddApi('load', 'Load');
    }

    public function Load(): ApiActionResult
    {
        $attributes = $this->attributeRepository->GetByCategory(CustomAttributeCategory::RESERVATION);
        $rules = $this->reservationRepository->GetReservationColorRules();

        return new ApiActionResult(true, ['attributes' => AttributeApiDto::FromList($attributes), 'rules' => ReservationColorRuleDto::FromList($rules)]);
    }

    public function Add($json): ApiActionResult
    {
        /** @var ReservationColorRuleDto $dto */
        $dto = $json;

        $colorRule = ReservationColorRule::Create(intval($dto->attributeId), trim($dto->value), trim($dto->color), intval($dto->comparisonType));
        $colorRule->SetPrioity($dto->priority);
        $this->reservationRepository->AddReservationColorRule($colorRule);

        return new ApiActionResult(true, ReservationColorRuleDto::FromRule($colorRule));
    }

    public function Update($json): ApiActionResult
    {
        /** @var ReservationColorRuleDto $dto */
        $dto = $json;
        $ruleId = intval($dto->id);
        $colorRule = $this->reservationRepository->GetReservationColorRule($ruleId);
        $colorRule->SetRequiredValue(trim($dto->value), intval($dto->comparisonType));
        $colorRule->SetColor(trim($dto->color));
        $colorRule->SetComparisonType(intval($dto->comparisonType), trim($dto->value));
        $colorRule->SetAttributeId(intval($dto->attributeId));
        $colorRule->SetPrioity($dto->priority);
        $this->reservationRepository->UpdateReservationColorRule($colorRule);

        return new ApiActionResult(true, ReservationColorRuleDto::FromRule($colorRule));
    }

    public function Delete($json): ApiActionResult
    {
        /** @var ReservationColorRuleDto $dto */
        $dto = $json;
        $ruleId = intval($dto->id);
        $rule = $this->reservationRepository->GetReservationColorRule($ruleId);
        $this->reservationRepository->DeleteReservationColorRule($rule);

        return new ApiActionResult(true, ["id" => $ruleId]);
    }
}
