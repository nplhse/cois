<?php

namespace App\Factory;

use App\Entity\Allocation;
use App\Repository\AllocationRepository;
use Carbon\Carbon;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Allocation>
 *
 * @method static           Allocation|Proxy createOne(array $attributes = [])
 * @method static           Allocation[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static           Allocation|Proxy find(object|array|mixed $criteria)
 * @method static           Allocation|Proxy findOrCreate(array $attributes)
 * @method static           Allocation|Proxy first(string $sortedField = 'id')
 * @method static           Allocation|Proxy last(string $sortedField = 'id')
 * @method static           Allocation|Proxy random(array $attributes = [])
 * @method static           Allocation|Proxy randomOrCreate(array $attributes = [])
 * @method static           Allocation[]|Proxy[] all()
 * @method static           Allocation[]|Proxy[] findBy(array $attributes)
 * @method static           Allocation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static           Allocation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static           AllocationRepository|RepositoryProxy repository()
 * @method Allocation|Proxy create(array|callable $attributes = [])
 */
final class AllocationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $creationDate = Carbon::create(random_int(2019, 2021), random_int(1, 12), random_int(1, 31), random_int(0, 23), random_int(0, 59), random_int(0, 59), 'Europe/Berlin');
        $arrivalDate = $creationDate->addMinutes(random_int(1, 42));

        return [
            'createdAt' => $creationDate,
            'creationDate' => $creationDate->toDateString(),
            'creationTime' => $creationDate->toTimeString(),
            'creationDay' => $creationDate->day,
            'creationWeekday' => $creationDate->locale('de')->dayName,
            'creationMonth' => $creationDate->locale('de')->month,
            'creationYear' => $creationDate->year,
            'creationHour' => $creationDate->hour,
            'creationMinute' => $creationDate->minute,
            'arrivalAt' => $arrivalDate,
            'arrivalDate' => $arrivalDate->toDateString(),
            'arrivalTime' => $arrivalDate->toTimeString(),
            'arrivalDay' => $arrivalDate->day,
            'arrivalWeekday' => $arrivalDate->locale('de')->dayName,
            'arrivalMonth' => $arrivalDate->locale('de')->month,
            'arrivalYear' => $arrivalDate->month,
            'arrivalHour' => $arrivalDate->year,
            'arrivalMinute' => $arrivalDate->minute,
            'requiresResus' => self::faker()->boolean(),
            'requiresCathlab' => self::faker()->boolean(),
            'gender' => self::faker()->randomElement(['M', 'W', 'D']),
            'age' => random_int(1, 100),
            'isCPR' => self::faker()->boolean(),
            'isVentilated' => self::faker()->boolean(),
            'isShock' => self::faker()->boolean(),
            'isInfectious' => self::faker()->randomElement(['Keine', 'MRSA', '3MRGN', '4MRGN/CRE', 'Sonstiges']),
            'isPregnant' => self::faker()->boolean(),
            'isWithPhysician' => self::faker()->boolean(),
            'modeOfTransport' => self::faker()->randomElement(['Boden', 'Luft']),
            'assignment' => self::faker()->randomElement(['RD', 'Arzt/Arzt', 'Patient', 'ZLST']),
            'occasion' => self::faker()->randomElement(['Häuslicher Einsatz', 'Verkehrsunfall', 'Sonstiger Einsatz', 'aus Arztpraxis', 'Öffentlicher Raum']),
            'speciality' => self::faker()->randomElement(['Innere Medizin', 'Chirurgie', 'Urologie']),
            'specialityDetail' => self::faker()->randomElement(['Allgemein Innere Medizin', 'Gastroenterologie', 'Allgemein- und Viszeralchirurgie', 'Urologie']),
            'handoverPoint' => 'ZNA',
            'specialityWasClosed' => self::faker()->boolean(),
            'PZC' => self::faker()->randomNumber(6),
            'PZCText' => self::faker()->sentence(3),
            'SecondaryPZC' => self::faker()->randomNumber(6),
            'SecondaryPZCText' => self::faker()->sentence(3),
            'isWorkAccident' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Allocation $allocation): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Allocation::class;
    }
}
