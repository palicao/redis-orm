<?php

namespace Tystr\RedisOrm\Tests\Integration\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use DateTime;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Predis\Client;
use Tystr\RedisOrm\Criteria\AndGroupInterface;
use Tystr\RedisOrm\Criteria\Criteria;
use Tystr\RedisOrm\Criteria\OrGroupInterface;
use Tystr\RedisOrm\Criteria\Restrictions;
use Tystr\RedisOrm\KeyNamingStrategy\ColonDelimitedKeyNamingStrategy;
use Tystr\RedisOrm\Metadata\AnnotationMetadataLoader;
use Tystr\RedisOrm\Metadata\MetadataRegistry;
use Tystr\RedisOrm\Repository;
use Tystr\RedisOrm\Tests\Integration\Model\Car;
use Tystr\RedisOrm\Tests\Integration\Model\User;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MainContext implements SnippetAcceptingContext
{
    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var Repository
     */
    protected $userRepository;

    /**
     * @var array
     */
    protected $lists = array();

    /**
     * @var array|Car[]
     */
    protected $cars = array();

    public function __construct()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $redisHost = getenv('REDIS_HOST') ?: 'localhost';
        $this->redis = new Client(sprintf('tcp://%s:6379', $redisHost));

        $keyNamingStrategy = new ColonDelimitedKeyNamingStrategy();
        $loader = new AnnotationMetadataLoader('/tmp');
        $metadataRegistry = new MetadataRegistry($loader);
        $this->repository = new Repository(
            $this->redis,
            $keyNamingStrategy,
            Car::class,
            $metadataRegistry
        );
        $this->userRepository = new Repository(
            $this->redis,
            $keyNamingStrategy,
            User::class,
            $metadataRegistry
        );
    }

    /**
     * @BeforeScenario
     */
    public function flushRedis()
    {
        $this->redis->flushall();
    }

    /**
     * @Transform /^(\d+)$/
     */
    public function castStringToNumber($string)
    {
        return intval($string);
    }

    /**
     * @Given /the following Cars?:/
     */
    public function theFollowingCars(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $car = new Car();
            $car->setId($data['id']);
            $car->setColor(isset($data['color']) ? $data['color'] : null);
            $car->setEngineType($data['engine_type']);
            $car->setMake($data['make']);
            $car->setModel($data['model']);
            $car->setManufactureDate(new DateTime('2013-01-01'));
            if (isset($data['active'])) {
                $car->setActive((bool)$data['active']);
            }
            $this->repository->save($car);
        }
    }
    /**
     * @Given the car with id :id has the property :propertyName with the following values:
     */
    public function theCarWithIdHasThePropertyWithTheFollowingValues($id, $propertyName, TableNode $values)
    {
        $car = $this->repository->find($id);
        $data = array();
        foreach ($values->getRowsHash() as $key => $value) {
            $data[$key] = $value;
        }
        $setter = 'set'.ucfirst(strtolower($propertyName));
        $car->$setter($data);
        $this->repository->save($car);
    }

    /**
     * @Then the car with the id :id should have property :propertyName with the following values:
     */
    public function theCarWithTheIdShouldHavePropertyWithTheFollowingValues($id, $propertyName, TableNode $values)
    {
        $car = $this->repository->find($id);
        $getter = 'get'.ucfirst(strtolower($propertyName));
        $data = $car->$getter();
        foreach ($values->getRowsHash() as $key => $value) {
            assertTrue(isset($data[$key]));
            assertEquals($value, $data[$key]);
        }
    }

    /**
     * @Then the car with the id :id should have property :propertyName with the value :expectedValue:
     */
    public function theCarWithTheIdShouldHavePropertyWithValue($id, $propertyName, $expectedValue)
    {
        if ('true' === $expectedValue) {
            $expectedValue = true;
        } elseif ('false' === $expectedValue) {
            $expectedValue = false;
        }

        $car = $this->repository->find($id);
        $getter = 'get'.ucfirst(strtolower($propertyName));
        assertSame($expectedValue, $car->$getter());
    }

    /**
     * @Then there should be :count keys in the database
     */
    public function thereShouldBeKeysInTheDatabase($count)
    {
        assertCount($count, $this->redis->keys('*'));
    }

    /**
     * @Then the following keys should exist:
     */
    public function theFollowingKeysShouldExist(TableNode $table)
    {
        foreach ($table->getHash() as $key) {
            assertEquals(1, $this->redis->sismember($key['name'], $key['value']));
        }
    }

    /**
     * @When I find a Car by id :id
     */
    public function iFindACarById($id)
    {
        $this->cars[] = $this->repository->find($id);
    }

    /**
     * @When I find cars where the property :name is :value
     */
    public function iFindCarsThatHavePropertyValue($name, $value)
    {
        $restriction = Restrictions::equalTo($name, $value);
        $criteria = new Criteria();
        $criteria->addRestriction($restriction);
        $this->cars = $this->repository->findBy($criteria);
    }

    /**
     * @Then there should be :count car
     */
    public function iThereShouldBeCarReturned($count)
    {
        assertCount($count, $this->cars);
        assertInstanceOf(Car::class, $this->cars[0]);
    }

    /**
     * @Then the car with the id :arg1 should have the following properties:
     */
    public function theCarWithTheIdShouldHaveTheFollowingProperties($id, TableNode $table)
    {
        $car = $this->getObjectById($id);

        $expected = $table->getHash();
        assertEquals($expected[0]['make'], $car->getMake());
        assertEquals($expected[0]['model'], $car->getModel());
        assertEquals($expected[0]['engine_type'], $car->getEngineType());
        assertEquals($expected[0]['color'], $car->getColor());
    }

    /**
     * @When I set the manufacture date to null
     */
    public function iSetTheManufactureDateToNull()
    {
        $car = $this->getObjectById(1);
        $car->setManufactureDate(null);
        $this->repository->save($car);
    }

    /**
     * @Then When I set the color for the car :id to :color
     */
    public function whenISetTheColorForTheCarTo($id, $color)
    {
        $car = $this->getObjectById($id);
        $color = $color === 'null' ? null : $color;
        $car->setColor($color);
        $this->repository->save($car);
    }

    /**
     * @Then When I set the active for the car :id to :active
     */
    public function whenISetTheActiveForTheCarTo($id, $active)
    {
        $car = $this->getObjectById($id);
        if ($active === 'true') {
            $active = true;
        } elseif ($active === 'false') {
            $active = false;
        }
        $car->setActive($active);
        $this->repository->save($car);
    }

    /**
     * @Then there should be :count items in the :key key
     */
    public function thereShouldBeItemsInTheKey($count, $key)
    {
        $type = $this->redis->type($key);
        if ('set' === (string) $type) {
            assertEquals($count, $this->redis->scard($key));
        } else {
            assertEquals($count, $this->redis->zcard($key));
        }
    }

    /**
     * @Given the following users:
     */
    public function theFollowingUsers(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $email = $row['email'];
            unset($row['email']);
            $dob = $row['dob'];
            unset($row['dob']);
            $signup = $row['signup'];
            unset($row['signup']);
            $lastOpen = $row['last_open'];
            unset($row['last_open']);
            $lastClick = $row['last_click'];
            unset($row['last_click']);

            $user = new User($email, $row);
            $user->setDateOfBirth(new DateTime($dob));
            $user->setSignupDate(new DateTime($signup));
            $user->setLastOpen(new DateTime($lastOpen));
            $user->setLastClick(new DateTime($lastClick));
            $this->userRepository->save($user);
        }
    }

    /**
     * @Given the list :listName has the following criteria:
     */
    public function theListHasTheFollowingCriteria($listName, TableNode $table)
    {
        $restrictionRows = $table->getHash();

        $restrictions = array();
        $restrictionsBelongingToParent = array();

        foreach ($restrictionRows as $rowNum => $row) {
            $methodName = $row['name'];
            $dummyRestriction = Restrictions::$methodName('', array());
            if ($dummyRestriction instanceof AndGroupInterface || $dummyRestriction instanceof OrGroupInterface) {
                $value = array();
                foreach (explode(',', $row['value']) as $childId) {
                    $childId = trim($childId);
                    if ($childId !== null) {
                        $value[] = $restrictions[$childId];
                        $restrictionsBelongingToParent[] = $childId;
                    }
                }
            } else {
                $value = $this->translateRestrictionValue($row);
            }
            $restriction = Restrictions::$methodName($row['key'], $value);
            $restrictions[$rowNum + 1] = $restriction;
        }

        $parentlessRestrictions = array();
        $parentlessRestrictionIds = array_diff(array_keys($restrictions), $restrictionsBelongingToParent);
        foreach ($parentlessRestrictionIds as $id) {
            $parentlessRestrictions[] = $restrictions[$id];
        }

        $criteria = new Criteria(new ArrayCollection($parentlessRestrictions));
        $this->lists[$listName] = $criteria;
    }

    /**
     * @param $restriction
     *
     * @return DateTime|string
     */
    private function translateRestrictionValue($restriction)
    {
        if (in_array($restriction['key'], array('signup', 'last_open', 'last_click', 'dob'))) {
            $value = new DateTime($restriction['value']);
            $value = $value->format('U');
        } else {
            $value = $restriction['value'];
        }

        return $value;
    }

    /**
     * @Then the list :listName should have :count users
     */
    public function theListShouldHaveRecipients($listName, $count)
    {
        assertCount($count, $this->userRepository->findBy($this->lists[$listName]));
    }

    /**
     * @Then the list :listName should have the ids :ids
     */
    public function theListShouldHaveIds($listName, $ids)
    {
        $resultIds = $this->userRepository->findIdsBy($this->lists[$listName]);
        $expectedIds = array_map('trim', explode(',', $ids));
        $idDiff = array_diff($resultIds, $expectedIds);
        assertEquals(array(), $idDiff);
        assertCount(count($expectedIds), $resultIds);
    }

    /**
     * @param int$id
     *
     * @return object
     */
    public function getObjectById($id)
    {
        $object = $this->repository->find($id);
        assertNotNull($object);

        return $object;
    }
}
