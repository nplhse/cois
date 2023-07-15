<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Allocation;
use App\Entity\AuditLog;
use App\Entity\CookieConsent;
use App\Service\AuditLogger;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class which listens on Doctrine events and writes an audit log of any entity changes made via Doctrine.
 */
class AuditSubscriber implements EventSubscriberInterface
{
    private array $excludedEntities = [
        Allocation::class,
        AuditLog::class,
        CookieConsent::class,
    ];

    private array $includedAttributes = [
        'id',
        'name',
        'title',
        'category' => ['id', 'name'],
        'comment' => ['id', 'id'],
        'contactRequest' => ['id', 'id'],
        'dispatchArea' => ['id', 'name'],
        'hospital' => ['id', 'name'],
        'import' => ['id', 'name'],
        'owner' => ['id', 'name'],
        'page' => ['id', 'name'],
        'post' => ['id', 'name'],
        'resetPasswordRequest' => ['id'],
        'skippedRow' => ['id'],
        'state' => ['id', 'name'],
        'supplyArea' => ['id', 'name'],
        'tag' => ['id', 'name'],
        'user' => ['id', 'name'],
    ];

    private array $ignoredFields = [
        'createdAt',
        'updatedAt',
        'password',
    ];

    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly string $kernelEnvironment,
        private array $removals = [],
    ) {
    }

    public function getSubscribedEvents(): array
    {
        if ('test' === $this->kernelEnvironment) {
            return [];
        }

        return [
            'postPersist',
            'postUpdate',
            'preRemove',
            'postRemove',
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (in_array($entity::class, $this->excludedEntities, true)) {
            return;
        }

        $entityData = $this->normalizeEntity($entity);

        $this->auditLogger->log($this->getEntityType($entity), $entity->getId(), 'insert', $entityData);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (in_array($entity::class, $this->excludedEntities, true)) {
            return;
        }

        /** @var EntityManager $em */
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $entityData = $uow->getEntityChangeSet($entity);

        foreach ($entityData as $key => $value) {
            if (is_object($value[0])) {
                $entityData[$key][0] = $this->normalizeEntityId($value[0]);
            }
            if (is_object($value[1])) {
                $entityData[$key][1] = $this->normalizeEntityId($value[1]);
            }
        }

        foreach ($entityData as $field => $change) {
            if (in_array($field, $this->ignoredFields, true)) {
                unset($entityData[$field]);
                continue;
            }

            if ('content' === $field) {
                $entityData[$field] = [
                    'from' => (strlen($change[0]) > 53) ? substr($change[0], 0, 50).'...' : $change[0],
                    'to' => (strlen($change[1]) > 53) ? substr($change[1], 0, 50).'...' : $change[1],
                ];

                continue;
            }

            $entityData[$field] = [
                'from' => $change[0],
                'to' => $change[1],
            ];
        }

        $this->auditLogger->log($this->getEntityType($entity), $entity->getId(), 'update', $entityData);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->removals[] = $this->normalizeEntity($entity);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (in_array($entity::class, $this->excludedEntities, true)) {
            return;
        }

        $entityData = array_pop($this->removals);

        $this->auditLogger->log($this->getEntityType($entity), $entityData['id'], 'delete', $entityData);
    }

    private function getEntityType(object $entity): string
    {
        return str_replace('App\Entity\\', '', $entity::class);
    }

    private function normalizeEntity(object $entity): array
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return (new Serializer($normalizers, $encoders))->normalize($entity, null, [AbstractNormalizer::ATTRIBUTES => $this->includedAttributes]);
    }

    private function normalizeEntityId(object $entity): mixed
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return (new Serializer($normalizers, $encoders))->normalize($entity, null, [AbstractNormalizer::ATTRIBUTES => ['id']]);
    }
}
