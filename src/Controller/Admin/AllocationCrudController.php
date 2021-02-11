<?php

namespace App\Controller\Admin;

use App\Entity\Allocation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class AllocationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Allocation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Allocations')
            ->setSearchFields(['id', 'dispatchArea', 'supplyArea', 'hospital.name', 'PZC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('Basics');
        $id = IntegerField::new('id', 'Id');
        $dispatchArea = TextField::new('dispatchArea', 'Dispatch Area');
        $supplyArea = TextField::new('supplyArea', 'Supply Area');
        $hospital = AssociationField::new('hospital', 'Hospital');
        $basics = [$panel1, $dispatchArea, $supplyArea, $hospital];

        $panel2 = FormField::addPanel('Creation');
        $createdAt = DateTimeField::new('createdAt', 'Created At');
        $creationDate = TextField::new('creationDate', 'Creation Date');
        $creationTime = TextField::new('creationTime', 'Creation Time');
        $creationDay = IntegerField::new('creationDay', 'Creation Day');
        $creationWeekday = TextField::new('creationWeekday', 'Creation Weekday');
        $creationYear = IntegerField::new('creationYear', 'Creation Year');
        $creationMonth = IntegerField::new('creationMonth', 'Creation Month');
        $creationHour = IntegerField::new('creationHour', 'Creation Hour');
        $creationMinute = IntegerField::new('creationMinute', 'Creation Minute');
        $creation = [$panel2, $createdAt, $creationDate, $creationTime, $creationDay, $creationWeekday, $creationYear, $creationMonth, $creationHour, $creationMinute];

        $panel3 = FormField::addPanel('Arrival');
        $arrivalAt = DateTimeField::new('arrivalAt', 'Arrival At');
        $arrivalDate = TextField::new('arrivalDate', 'Creation Date');
        $arrivalTime = TextField::new('arrivalTime', 'Creation Time');
        $arrivalDay = IntegerField::new('arrivalDay', 'Creation Day');
        $arrivalWeekday = TextField::new('arrivalWeekday', 'Creation Weekday');
        $arrivalYear = IntegerField::new('arrivalYear', 'Creation Year');
        $arrivalMonth = IntegerField::new('arrivalMonth', 'Creation Month');
        $arrivalHour = IntegerField::new('arrivalHour', 'Creation Hour');
        $arrivalMinute = IntegerField::new('arrivalMinute', 'Creation Minute');
        $arrival = [$panel3, $arrivalAt, $arrivalDate, $arrivalTime, $arrivalDay, $arrivalWeekday, $arrivalYear, $arrivalMonth, $arrivalHour, $arrivalMinute];

        $panel4 = FormField::addPanel('Patients data');
        $reqResus = BooleanField::new('requiresResus');
        $reqCathlab = BooleanField::new('requiresCathlab');
        $gender = TextField::new('gender');
        $age = IntegerField::new('age');
        $isCPR = BooleanField::new('isCPR');
        $isVentilated = BooleanField::new('isVentilated');
        $isShock = BooleanField::new('isShock');
        $isInfectious = TextField::new('isInfectious');
        $isPregnant = BooleanField::new('isPregnant');
        $patient = [$panel4, $reqResus, $reqCathlab, $gender, $age, $isCPR, $isVentilated, $isShock, $isInfectious, $isPregnant];

        $panel5 = FormField::addPanel('Deployment data');
        $occasion = TextField::new('occasion');
        $assignment = TextField::new('assignment');
        $isWithPhysician = BooleanField::new('isWithPhysician');
        $modeOfTransport = TextField::new('modeOfTransport');
        $speciality = TextField::new('speciality');
        $specialityDetail = TextField::new('specialityDetail');
        $specialityWasClosed = BooleanField::new('specialityWasClosed');
        $PZC = IntegerField::new('PZC');
        $PZCText = TextField::new('PZCText');
        $SecondaryPZC = IntegerField::new('SecondaryPZC');
        $SecondaryPZCText = TextField::new('SecondaryPZCText');
        $comment = TextField::new('comment');
        $deployment = [$panel5, $occasion, $assignment, $isWithPhysician, $modeOfTransport, $speciality, $specialityDetail, $specialityWasClosed, $PZC, $PZCText, $SecondaryPZC, $SecondaryPZCText, $comment];

        $info = [$createdAt, $arrivalAt, $PZC];

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            array_unshift($basics, $id);
            $fields = array_merge($basics, $info);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            array_unshift($basics, $id);
            $fields = array_merge($basics, $creation, $arrival, $patient, $deployment);
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = array_merge($basics, $creation, $arrival, $patient, $deployment);
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = array_merge($basics, $creation, $arrival, $patient, $deployment);
        }

        return $fields;
    }
}
