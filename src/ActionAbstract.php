<?php

namespace SprintF\Bundle\Workflow;

use Doctrine\ORM\EntityManagerInterface;
use SprintF\Bundle\Workflow\Exception\CanNotException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Абстрактный класс "Действия" над каким-либо объектом (сущностью).
 */
abstract class ActionAbstract
{
    protected ActionLogEntryInterface $actionLogEntry;

    protected EntityManagerInterface $em;

    protected Security $security;

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): static
    {
        $this->em = $entityManager;

        return $this;
    }

    #[Required]
    public function setSecurity(Security $security): static
    {
        $this->security = $security;

        return $this;
    }

    /*
     * Сущность, над которой будет производиться действие
     */
    protected WorkflowEntityInterface $entity;

    public function getEntity(): WorkflowEntityInterface
    {
        return $this->entity;
    }

    public function setEntity(WorkflowEntityInterface $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    /*
     * Контекст действия - данные, которые нужны для его выполнения
     */
    protected ?ContextAbstract $context = null;

    public function setContext(?ContextAbstract $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Метод, определяющий, разрешено ли произвести действие.
     *
     * @throws CanNotException
     */
    protected function can(): bool
    {
        return false;
    }

    /**
     * Метод, собственно производящий действие.
     */
    abstract protected function do(): void;

    protected function start()
    {
        $class = $this->entity->getActionLogEntryClass();
        /** @var ActionLogEntryInterface $entry */
        $entry = new $class();
        $entry
            ->setEntity($this->entity)
            ->setActionClass(static::class)
            ->setUser($this->security->getUser())
            ->setContext($this->context)
            ->setStartedAt(new \DateTime('now'))
            ->setResult(ActionResult::PROGRESS)
        ;
        $this->em->persist($entry);
        $this->em->flush();
        $this->actionLogEntry = $entry;
    }

    /**
     * Основной метод действия.
     *
     * @throws CanNotException
     */
    public function __invoke(): ActionResult
    {
        $this->start();

        try {
            $can = $this->can();
            if (!$can) {
                throw new CanNotException();
            }

            $this->do();
            $this->success();

            return ActionResult::SUCCESS;
        } catch (CanNotException $e) {
            $this->cannot($e);
            throw $e;
        } catch (\Throwable $e) {
            $this->fail($e);
            throw new CanNotException($e->getMessage(), previous: $e);
        }
    }

    protected function cannot(CanNotException $exception): void
    {
        $this->close(ActionResult::CANNOT, trim(get_class($exception).': '.$exception->getMessage(), ':'));
    }

    protected function fail(\Throwable $exception): void
    {
        $this->close(ActionResult::FAIL, trim(get_class($exception).': '.$exception->getMessage(), ':'));
    }

    protected function success(): void
    {
        $this->close(ActionResult::SUCCESS);
    }

    /**
     * Завершение выполнения действия с тем или иным результатом.
     *
     * Мы вынуждены применять здесь прямые запросы к БД, поскольку в результате ошибки Entity Manager может быть закрыт.
     */
    protected function close(ActionResult $result, string $reason = null): void
    {
        $table = $this->em->getClassMetadata(get_class($this->actionLogEntry))->table['name'];
        $idColumn = $this->em->getClassMetadata(get_class($this->actionLogEntry))->getColumnName('id');
        $entityIdColumn = $this->em->getClassMetadata(get_class($this->actionLogEntry))->getAssociationMapping('entity')['joinColumns'][0]['name'];
        $finishedAtColumn = $this->em->getClassMetadata(get_class($this->actionLogEntry))->getColumnName('finishedAt');
        $resultColumn = $this->em->getClassMetadata(get_class($this->actionLogEntry))->getColumnName('result');
        $reasonColumn = $this->em->getClassMetadata(get_class($this->actionLogEntry))->getColumnName('reason');

        $this->em->getConnection()->update(
            $table,
            [
                $entityIdColumn => $this->entity->getEntityId(),
                $finishedAtColumn => (new \DateTime('now'))->format('c'),
                $resultColumn => $result->value,
                $reasonColumn => trim($reason),
            ],
            [$idColumn => $this->actionLogEntry->getId()]
        );
    }
}
