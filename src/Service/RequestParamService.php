<?php

namespace App\Service;

use App\DataTransferObjects\PaginationDto;
use Symfony\Component\HttpFoundation\Request;

class RequestParamService
{
    private Request $request;

    private bool $filterIsSet = false;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __get(mixed $key): mixed
    {
        $item = $this->request->query->get($key);

        if (empty($item)) {
            return null;
        }

        return urldecode($item);
    }

    public function getSearch(): string|null
    {
        $search = $this->request->query->get('search');

        if (empty($search)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($search);
    }

    public function getPage(): int
    {
        $page = $this->request->query->get('page');

        if (is_numeric($page) && $page > 0) {
            return (int) $page;
        } else {
            return 1;
        }
    }

    public function getLocation(): string|null
    {
        $location = $this->request->query->get('location');

        if (empty($location)) {
            return null;
        }

        $this->filterIsSet = true;

        return $location;
    }

    public function getSize(): string|null
    {
        $size = $this->request->query->get('size');

        if (empty($size)) {
            return null;
        }

        $this->filterIsSet = true;

        return $size;
    }

    public function getSupplyArea(): string|null
    {
        $supplyArea = $this->request->query->get('supplyArea');

        if (empty($supplyArea)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($supplyArea);
    }

    public function getDispatchArea(): string|null
    {
        $dispatchArea = $this->request->query->get('dispatchArea');

        if (empty($dispatchArea)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($dispatchArea);
    }

    public function getHospital(): string|null
    {
        $hospital = $this->request->query->get('hospital');

        if (empty($hospital)) {
            return null;
        }

        $this->filterIsSet = true;

        return $hospital;
    }

    public function getOccasion(): string|null
    {
        $occasion = $this->request->query->get('occasion');

        if (empty($occasion)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($occasion);
    }

    public function getAssignment(): string|null
    {
        $assignment = $this->request->query->get('assignment');

        if (empty($assignment)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($assignment);
    }

    public function getTransport(): string|null
    {
        $transport = $this->request->query->get('transport');

        if (empty($transport)) {
            return null;
        }

        $this->filterIsSet = true;

        return $transport;
    }

    public function getPZC(): string|null
    {
        $PZC = $this->request->query->get('pzc');

        if (empty($PZC)) {
            return null;
        }

        $this->filterIsSet = true;

        return $PZC;
    }

    public function getReqResus(): string|null
    {
        $reqResus = $this->request->query->get('reqResus');

        if (empty($reqResus)) {
            return null;
        }

        $this->filterIsSet = true;

        return $reqResus;
    }

    public function getReqCath(): string|null
    {
        $reqCath = $this->request->query->get('reqCath');

        if (empty($reqCath)) {
            return null;
        }

        $this->filterIsSet = true;

        return $reqCath;
    }

    public function getIsCPR(): string|null
    {
        $isCPR = $this->request->query->get('isCPR');

        if (empty($isCPR)) {
            return null;
        }

        $this->filterIsSet = true;

        return $isCPR;
    }

    public function getIsVentilated(): string|null
    {
        $isVent = $this->request->query->get('isVent');

        if (empty($isVent)) {
            return null;
        }

        $this->filterIsSet = true;

        return $isVent;
    }

    public function getIsShock(): string|null
    {
        $isShock = $this->request->query->get('isShock');

        if (empty($isShock)) {
            return null;
        }

        $this->filterIsSet = true;

        return $isShock;
    }

    public function getIsWithDoctor(): string|null
    {
        $isWithDoc = $this->request->query->get('isWithDoc');

        if (empty($isWithDoc)) {
            return null;
        }

        $this->filterIsSet = true;

        return $isWithDoc;
    }

    public function getIsPregnant(): string|null
    {
        $isPreg = $this->request->query->get('isPreg');

        if (empty($isPreg)) {
            return null;
        }

        $this->filterIsSet = true;

        return $isPreg;
    }

    public function getIsWorkAccident(): string|null
    {
        $isWork = $this->request->query->get('isWork');

        if (empty($isWork)) {
            return null;
        }

        $this->filterIsSet = true;

        return $isWork;
    }

    public function getStartDate(): string|null
    {
        $date = $this->request->query->get('startDate');

        if (empty($date)) {
            return null;
        }

        $this->filterIsSet = true;

        return $date;
    }

    public function getEndDate(): string|null
    {
        $date = $this->request->query->get('endDate');

        if (empty($date)) {
            return null;
        }

        $this->filterIsSet = true;

        return $date;
    }

    public function getShow(): string|null
    {
        $show = $this->request->query->get('show');

        if (empty($show)) {
            return null;
        }

        $this->filterIsSet = true;

        return $show;
    }

    public function getOrderBy(): string
    {
        $order = $this->request->query->get('orderBy');

        if (empty($order)) {
            return 'desc';
        }

        $this->filterIsSet = true;

        return $order;
    }

    public function getSortBy(): string|null
    {
        $sortBy = $this->request->query->get('sortBy');

        if (empty($sortBy)) {
            return null;
        }

        $this->filterIsSet = true;

        return $sortBy;
    }

    public function getUser(): string|null
    {
        $user = $this->request->query->get('user');

        if (empty($user)) {
            return null;
        }

        $this->filterIsSet = true;

        return $user;
    }

    public function getUrgency(): string|null
    {
        $urgency = $this->request->query->get('urgency');

        if (empty($urgency)) {
            return null;
        }

        if ('SK' === $urgency) {
            return null;
        }

        $this->filterIsSet = true;

        return $urgency;
    }

    public function getSpeciality(): string|null
    {
        $speciality = $this->request->query->get('speciality');

        if (empty($speciality)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($speciality);
    }

    public function getSpecialityDetail(): string|null
    {
        $specialityDetail = $this->request->query->get('specialityDetail');

        if (empty($specialityDetail)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($specialityDetail);
    }

    public function getInfection(): string|null
    {
        $infection = $this->request->query->get('infection');

        if (empty($infection)) {
            return null;
        }

        $this->filterIsSet = true;

        return urldecode($infection);
    }

    public function isFilterIsSet(): bool
    {
        return $this->filterIsSet;
    }

    public function getPagination(int $count, int $page, int $perPage): PaginationDto
    {
        $last = floor($count / $perPage);

        if (0 == $count % 10) {
            --$last;
        }

        if (0 == (int) $last) {
            $last = 1;
        }

        if ($page > 1) {
            $previous = $page - 1;
        } else {
            $previous = null;
        }

        if ($page < $last) {
            $next = $page + 1;
        } else {
            $next = null;
        }

        return new PaginationDto($page, $perPage, $previous, $next, (int) $last);
    }
}
