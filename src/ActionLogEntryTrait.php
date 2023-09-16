<?php

namespace SprintF\Bundle\Workflow;

use App\Entity\User; // @todo: remove this
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping as ORM;
use SprintF\Bundle\Admin\Attribute as Admin;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Общая часть свойств и методов всех записей лога действий над разными сущностями.
 *
 * @implements  ActionLogEntryInterface
 */
trait ActionLogEntryTrait
{
    /**
     * Последовательный монотонно возрастающий ID записи в логе бизнес-действий.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')] // Тут должно быть strategy: 'SEQUENCE', но это ломает генератор миграций напрочь и толком не работает...
    #[ORM\SequenceGenerator(sequenceName: 'action_log___id_seq')]
    #[ORM\Column(name: '__id', type: 'bigint')]
    #[Admin\AdminField(label: '#')]
    protected int $id;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Класс сущности, над которой было произведено действие.
     */
    #[ORM\Column(name: 'entity_class', type: 'string', length: 500)]
    #[Admin\AdminField(label: 'Класс сущности')]
    protected string $entityClass;

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Идентификатор сущности, над которой было произведено действие.
     */
    #[ORM\Column(name: '__entity_id', type: 'bigint', nullable: true)]
    #[Admin\AdminField(label: 'ID сущности')]
    protected ?int $entityId = null;

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    /**
     * Сущность, над которой было произведено действие.
     */
    public function getEntity(): ?WorkflowEntityInterface
    {
        if (property_exists($this, 'entity') && !empty($this->getEntityId())) {
            return $this->entity;
        }

        return null;
    }

    public function setEntity(WorkflowEntityInterface $entity): static
    {
        $this->entityClass = $entity->getEntityClass();
        $this->entityId = $entity->getEntityId();
        if (property_exists($this, 'entity') && null !== $this->entityId) {
            $this->entity = $entity;
        }

        return $this;
    }

    /**
     * Класс действия, которое было произведено над сущностью.
     */
    #[ORM\Column(name: 'action_class', type: 'string', length: 500)]
    #[Admin\AdminField(label: 'Класс действия')]
    protected string $actionClass;

    public function getActionClass(): string
    {
        return $this->actionClass;
    }

    public function setActionClass(string $actionClass): static
    {
        $this->actionClass = $actionClass;

        return $this;
    }

    /**
     * Контекст действия, которое было произведено над сущностью.
     */
    #[ORM\Column(name: 'context', type: 'json', nullable: true, options: ['jsonb' => true])]
    #[Admin\AdminField(label: 'Контекст')]
    protected ?array $context = null;

    public function getContext(): ?ContextAbstract
    {
        // TODO: Implement getContext() method.
    }

    public function setContext(?ContextAbstract $context): static
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return (string) $object;
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ];
        $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer(classMetadataFactory: $classMetadataFactory, defaultContext: $defaultContext)], []);

        $this->context = null !== $context ? $serializer->normalize($context) : null;

        return $this;
    }

    /**
     * Пользователь системы, от лица которого производилось действие.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: '__user_id', referencedColumnName: '__id')]
    #[Admin\AdminField(label: 'Кто произвел?')]
    protected ?UserInterface $user = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    #[ORM\Column(name: 'started_at', type: 'datetime')]
    #[Admin\AdminField(label: 'Дата-время начала действия')]
    protected \DateTimeInterface $startedAt;

    /**
     * Дата и время начала выполнения действия.
     */
    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Дата и время окончания выполнения действия.
     */
    #[ORM\Column(name: 'finished_at', type: 'datetime', nullable: true)]
    #[Admin\AdminField(label: 'Дата-время окончания действия')]
    protected ?\DateTimeInterface $finishedAt = null;

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeInterface $finishedAt): static
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Результат выполнения действия.
     */
    #[ORM\Column(type: 'action_result', enumType: ActionResult::class, options: ['default' => 'progress'])]
    #[Admin\AdminField(label: 'Результат')]
    protected ActionResult $result = ActionResult::PROGRESS;

    public function getResult(): ActionResult
    {
        return $this->result;
    }

    public function setResult(ActionResult $result): static
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Причина, по которой действие не было выполнено.
     */
    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    #[Admin\AdminField(label: 'Причина')]
    protected ?string $reason = null;

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }
}
