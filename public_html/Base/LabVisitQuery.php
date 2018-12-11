<?php

namespace Base;

use \LabVisit as ChildLabVisit;
use \LabVisitQuery as ChildLabVisitQuery;
use \Exception;
use \PDO;
use Map\LabVisitTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'labvisit' table.
 *
 *
 *
 * @method     ChildLabVisitQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLabVisitQuery orderByCheckin($order = Criteria::ASC) Order by the checkin column
 * @method     ChildLabVisitQuery orderByCheckout($order = Criteria::ASC) Order by the checkout column
 * @method     ChildLabVisitQuery orderByCourseid($order = Criteria::ASC) Order by the courseid column
 *
 * @method     ChildLabVisitQuery groupById() Group by the id column
 * @method     ChildLabVisitQuery groupByCheckin() Group by the checkin column
 * @method     ChildLabVisitQuery groupByCheckout() Group by the checkout column
 * @method     ChildLabVisitQuery groupByCourseid() Group by the courseid column
 *
 * @method     ChildLabVisitQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLabVisitQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLabVisitQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLabVisitQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildLabVisitQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildLabVisitQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildLabVisitQuery leftJoinCourse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Course relation
 * @method     ChildLabVisitQuery rightJoinCourse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Course relation
 * @method     ChildLabVisitQuery innerJoinCourse($relationAlias = null) Adds a INNER JOIN clause to the query using the Course relation
 *
 * @method     ChildLabVisitQuery joinWithCourse($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Course relation
 *
 * @method     ChildLabVisitQuery leftJoinWithCourse() Adds a LEFT JOIN clause and with to the query using the Course relation
 * @method     ChildLabVisitQuery rightJoinWithCourse() Adds a RIGHT JOIN clause and with to the query using the Course relation
 * @method     ChildLabVisitQuery innerJoinWithCourse() Adds a INNER JOIN clause and with to the query using the Course relation
 *
 * @method     \CourseQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildLabVisit findOne(ConnectionInterface $con = null) Return the first ChildLabVisit matching the query
 * @method     ChildLabVisit findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLabVisit matching the query, or a new ChildLabVisit object populated from the query conditions when no match is found
 *
 * @method     ChildLabVisit findOneById(int $id) Return the first ChildLabVisit filtered by the id column
 * @method     ChildLabVisit findOneByCheckin(string $checkin) Return the first ChildLabVisit filtered by the checkin column
 * @method     ChildLabVisit findOneByCheckout(string $checkout) Return the first ChildLabVisit filtered by the checkout column
 * @method     ChildLabVisit findOneByCourseid(int $courseid) Return the first ChildLabVisit filtered by the courseid column *

 * @method     ChildLabVisit requirePk($key, ConnectionInterface $con = null) Return the ChildLabVisit by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLabVisit requireOne(ConnectionInterface $con = null) Return the first ChildLabVisit matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildLabVisit requireOneById(int $id) Return the first ChildLabVisit filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLabVisit requireOneByCheckin(string $checkin) Return the first ChildLabVisit filtered by the checkin column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLabVisit requireOneByCheckout(string $checkout) Return the first ChildLabVisit filtered by the checkout column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLabVisit requireOneByCourseid(int $courseid) Return the first ChildLabVisit filtered by the courseid column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildLabVisit[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildLabVisit objects based on current ModelCriteria
 * @method     ChildLabVisit[]|ObjectCollection findById(int $id) Return ChildLabVisit objects filtered by the id column
 * @method     ChildLabVisit[]|ObjectCollection findByCheckin(string $checkin) Return ChildLabVisit objects filtered by the checkin column
 * @method     ChildLabVisit[]|ObjectCollection findByCheckout(string $checkout) Return ChildLabVisit objects filtered by the checkout column
 * @method     ChildLabVisit[]|ObjectCollection findByCourseid(int $courseid) Return ChildLabVisit objects filtered by the courseid column
 * @method     ChildLabVisit[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class LabVisitQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\LabVisitQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'labcheck', $modelName = '\\LabVisit', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLabVisitQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLabVisitQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildLabVisitQuery) {
            return $criteria;
        }
        $query = new ChildLabVisitQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildLabVisit|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LabVisitTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = LabVisitTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildLabVisit A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, checkin, checkout, courseid FROM labvisit WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildLabVisit $obj */
            $obj = new ChildLabVisit();
            $obj->hydrate($row);
            LabVisitTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildLabVisit|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LabVisitTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LabVisitTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LabVisitTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the checkin column
     *
     * Example usage:
     * <code>
     * $query->filterByCheckin('2011-03-14'); // WHERE checkin = '2011-03-14'
     * $query->filterByCheckin('now'); // WHERE checkin = '2011-03-14'
     * $query->filterByCheckin(array('max' => 'yesterday')); // WHERE checkin > '2011-03-13'
     * </code>
     *
     * @param     mixed $checkin The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterByCheckin($checkin = null, $comparison = null)
    {
        if (is_array($checkin)) {
            $useMinMax = false;
            if (isset($checkin['min'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_CHECKIN, $checkin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($checkin['max'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_CHECKIN, $checkin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LabVisitTableMap::COL_CHECKIN, $checkin, $comparison);
    }

    /**
     * Filter the query on the checkout column
     *
     * Example usage:
     * <code>
     * $query->filterByCheckout('2011-03-14'); // WHERE checkout = '2011-03-14'
     * $query->filterByCheckout('now'); // WHERE checkout = '2011-03-14'
     * $query->filterByCheckout(array('max' => 'yesterday')); // WHERE checkout > '2011-03-13'
     * </code>
     *
     * @param     mixed $checkout The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterByCheckout($checkout = null, $comparison = null)
    {
        if (is_array($checkout)) {
            $useMinMax = false;
            if (isset($checkout['min'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_CHECKOUT, $checkout['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($checkout['max'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_CHECKOUT, $checkout['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LabVisitTableMap::COL_CHECKOUT, $checkout, $comparison);
    }

    /**
     * Filter the query on the courseid column
     *
     * Example usage:
     * <code>
     * $query->filterByCourseid(1234); // WHERE courseid = 1234
     * $query->filterByCourseid(array(12, 34)); // WHERE courseid IN (12, 34)
     * $query->filterByCourseid(array('min' => 12)); // WHERE courseid > 12
     * </code>
     *
     * @see       filterByCourse()
     *
     * @param     mixed $courseid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterByCourseid($courseid = null, $comparison = null)
    {
        if (is_array($courseid)) {
            $useMinMax = false;
            if (isset($courseid['min'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_COURSEID, $courseid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($courseid['max'])) {
                $this->addUsingAlias(LabVisitTableMap::COL_COURSEID, $courseid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LabVisitTableMap::COL_COURSEID, $courseid, $comparison);
    }

    /**
     * Filter the query by a related \Course object
     *
     * @param \Course|ObjectCollection $course The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildLabVisitQuery The current query, for fluid interface
     */
    public function filterByCourse($course, $comparison = null)
    {
        if ($course instanceof \Course) {
            return $this
                ->addUsingAlias(LabVisitTableMap::COL_COURSEID, $course->getId(), $comparison);
        } elseif ($course instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LabVisitTableMap::COL_COURSEID, $course->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCourse() only accepts arguments of type \Course or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Course relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function joinCourse($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Course');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Course');
        }

        return $this;
    }

    /**
     * Use the Course relation Course object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \CourseQuery A secondary query class using the current class as primary query
     */
    public function useCourseQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCourse($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Course', '\CourseQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildLabVisit $labVisit Object to remove from the list of results
     *
     * @return $this|ChildLabVisitQuery The current query, for fluid interface
     */
    public function prune($labVisit = null)
    {
        if ($labVisit) {
            $this->addUsingAlias(LabVisitTableMap::COL_ID, $labVisit->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the labvisit table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LabVisitTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            LabVisitTableMap::clearInstancePool();
            LabVisitTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LabVisitTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LabVisitTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            LabVisitTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            LabVisitTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // LabVisitQuery
