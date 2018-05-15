<?php declare(strict_types=1);

namespace Shopware\System\Snippet\Definition;

use Shopware\Application\Application\Definition\ApplicationDefinition;
use Shopware\Api\Entity\EntityDefinition;
use Shopware\Api\Entity\EntityExtensionInterface;
use Shopware\Api\Entity\Field\BoolField;
use Shopware\Api\Entity\Field\DateField;
use Shopware\Api\Entity\Field\FkField;
use Shopware\Api\Entity\Field\IdField;
use Shopware\Api\Entity\Field\LongTextField;
use Shopware\Api\Entity\Field\ManyToOneAssociationField;
use Shopware\Api\Entity\Field\StringField;
use Shopware\Api\Entity\Field\TenantIdField;
use Shopware\Api\Entity\FieldCollection;
use Shopware\Api\Entity\Write\Flag\PrimaryKey;
use Shopware\Api\Entity\Write\Flag\Required;
use Shopware\Api\Entity\Write\Flag\SearchRanking;
use Shopware\System\Snippet\Collection\SnippetBasicCollection;
use Shopware\System\Snippet\Collection\SnippetDetailCollection;
use Shopware\System\Snippet\Event\Snippet\SnippetDeletedEvent;
use Shopware\System\Snippet\Event\Snippet\SnippetWrittenEvent;
use Shopware\System\Snippet\Repository\SnippetRepository;
use Shopware\System\Snippet\Struct\SnippetBasicStruct;
use Shopware\System\Snippet\Struct\SnippetDetailStruct;

class SnippetDefinition extends EntityDefinition
{
    /**
     * @var FieldCollection
     */
    protected static $primaryKeys;

    /**
     * @var FieldCollection
     */
    protected static $fields;

    /**
     * @var EntityExtensionInterface[]
     */
    protected static $extensions = [];

    public static function getEntityName(): string
    {
        return 'snippet';
    }

    public static function getFields(): FieldCollection
    {
        if (self::$fields) {
            return self::$fields;
        }

        self::$fields = new FieldCollection([
            new TenantIdField(),
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            (new FkField('application_id', 'applicationId', ApplicationDefinition::class))->setFlags(new Required()),
            (new StringField('namespace', 'namespace'))->setFlags(new Required(), new SearchRanking(self::MIDDLE_SEARCH_RANKING)),
            (new StringField('locale', 'locale'))->setFlags(new Required()),
            (new StringField('name', 'name'))->setFlags(new Required(), new SearchRanking(self::HIGH_SEARCH_RANKING)),
            (new LongTextField('value', 'value'))->setFlags(new Required()),
            new DateField('created_at', 'createdAt'),
            new DateField('updated_at', 'updatedAt'),
            new BoolField('dirty', 'dirty'),
            new ManyToOneAssociationField('application', 'application_id', ApplicationDefinition::class, false),
        ]);

        foreach (self::$extensions as $extension) {
            $extension->extendFields(self::$fields);
        }

        return self::$fields;
    }

    public static function getRepositoryClass(): string
    {
        return SnippetRepository::class;
    }

    public static function getBasicCollectionClass(): string
    {
        return SnippetBasicCollection::class;
    }

    public static function getDeletedEventClass(): string
    {
        return SnippetDeletedEvent::class;
    }

    public static function getWrittenEventClass(): string
    {
        return SnippetWrittenEvent::class;
    }

    public static function getBasicStructClass(): string
    {
        return SnippetBasicStruct::class;
    }

    public static function getTranslationDefinitionClass(): ?string
    {
        return null;
    }

    public static function getDetailStructClass(): string
    {
        return SnippetDetailStruct::class;
    }

    public static function getDetailCollectionClass(): string
    {
        return SnippetDetailCollection::class;
    }
}
