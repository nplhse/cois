<?php

namespace App\DataFixtures;

use App\Entity\Allocation;
use App\Entity\Hospital;
use App\Entity\Import;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $date = Carbon::create(rand(2019, 2021), rand(1, 12), rand(1, 31), rand(0, 23), rand(0, 59), rand(0, 59), 'Europe/Berlin');

        $hospital = new Hospital();
        $hospital->setName('Sacred Heart Hospital');
        $hospital->setAddress('123 Fake Street');
        $hospital->setDispatchArea('Test Area');
        $hospital->setSupplyArea('Test Area');
        $hospital->setOwner($this->getReference(UserFixtures::BASE_USER_REFERENCE));
        $hospital->setBeds(rand(100, 1250));
        $hospital->setLocation($this->array_random(['rural', 'urban'], 1));
        $hospital->setCreatedAt($date);
        $hospital->setUpdatedAt($date);

        $manager->persist($hospital);

        $import = new Import();
        $import->setName('DemoImport');
        $import->setExtension('.csv');
        $import->setPath('dummy');
        $import->setMimeType('CSV');
        $import->setSize(1234);
        $import->setUser($this->getReference(UserFixtures::BASE_USER_REFERENCE));
        $import->setHospital($hospital);
        $import->setIsFixture(true);
        $import->setCaption('Demo Import');
        $import->setContents('allocation');
        $import->setStatus('finished');

        $import->setCreatedAt($date->addMinutes(rand(10, 150)));

        $manager->persist($import);

        for ($i = 1; $i <= 100; ++$i) {
            $date = Carbon::create(rand(2019, 2021), rand(1, 12), rand(1, 31), rand(0, 23), rand(0, 59), rand(0, 59), 'Europe/Berlin');

            $allocation = new Allocation();
            $allocation->setHospital($hospital);
            $allocation->setImport($import);

            $allocation->setDispatchArea('Test Area');
            $allocation->setSupplyArea('Test Area');

            $allocation->setCreatedAt($date);
            $allocation->setCreationDate($date->toDateString());
            $allocation->setCreationTime($date->toTimeString());
            $allocation->setCreationDay($date->day);
            $allocation->setCreationWeekday($date->locale('de')->dayName);
            $allocation->setCreationYear($date->year);
            $allocation->setCreationMonth($date->locale('de')->month);
            $allocation->setCreationHour($date->hour);
            $allocation->setCreationMinute($date->minute);

            $date->addMinutes(rand(1, 42));

            $allocation->setArrivalAt($date);
            $allocation->setArrivalDate($date->toDateString());
            $allocation->setArrivalTime($date->toTimeString());
            $allocation->setArrivalDay($date->day);
            $allocation->setArrivalWeekday($date->locale('de')->dayName);
            $allocation->setArrivalYear($date->year);
            $allocation->setArrivalMonth($date->locale('de')->month);
            $allocation->setArrivalHour($date->hour);
            $allocation->setArrivalMinute($date->minute);

            $allocation->setRequiresResus($this->array_random([true, false, false, false, false]));
            $allocation->setRequiresCathlab($this->array_random([true, false, false, false, false]));

            $allocation->setOccasion($this->array_random(['Häuslicher Einsatz', 'Verkehrsunfall', 'Sonstiger Einsatz', 'aus Arztpraxis', 'Öffentlicher Raum', ''], 1));
            $allocation->setGender($this->array_random(['M', 'M', 'M', 'W', 'W', 'W', 'D'], 1));
            $allocation->setAge(rand(1, 100));
            $allocation->setIsCPR($this->array_random([true, false, false, false, false]));
            $allocation->setIsVentilated($this->array_random([true, false, false, false, false]));
            $allocation->setIsShock($this->array_random([true, false, false, false, false]));
            $allocation->setIsInfectious($this->array_random(['Keine', 'MRSA', '3MRGN', '4MRGN/CRE', 'Sonstiges'], 1));
            $allocation->setIsPregnant($this->array_random([true, false, false, false, false, false, false]));
            $allocation->setIsWithPhysician($this->array_random([true, true, false, false, false]));
            $allocation->setIsWorkAccident($this->array_random([true, false, false, false, false]));
            $allocation->setAssignment($this->array_random(['RD', 'Arzt/Arzt', 'Patient', 'ZLST'], 1));
            $allocation->setModeOfTransport('Boden');

            $specialities = ['Innere Medizin', 'Chirurgie', 'Urologie'];
            $specialityDetail = ['Allgemein Innere Medizin', 'Gastroenterologie', 'Allgemein- und Viszeralchirurgie', 'Urologie'];

            $allocation->setSpeciality($this->array_random($specialities, 1));
            $allocation->setSpecialityDetail($this->array_random($specialityDetail, 1));
            $allocation->setHandoverPoint('ZNA');
            $allocation->setSpecialityWasClosed($this->array_random([true, false, false, false, false]));

            $allocation->setPZC(rand(111111, 999999));
            $allocation->setPZCText('Noch kein Text');

            $allocation->setSecondaryPZC(123456);
            $allocation->setSecondaryPZCText('Noch kein Text');

            $manager->persist($allocation);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    private function array_random(array $arr, int $num = 1): mixed
    {
        shuffle($arr);

        $r = [];
        for ($i = 0; $i < $num; ++$i) {
            $r[] = $arr[$i];
        }

        return 1 == $num ? $r[0] : $r;
    }
}
