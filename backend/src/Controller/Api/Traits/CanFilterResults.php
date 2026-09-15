<?php

declare(strict_types=1);

namespace App\Controller\Api\Traits;

use App\Exception\ValidationException;
use App\Http\ServerRequest;
use App\Utilities\ListFilter;
use App\Utilities\Types;
use Doctrine\ORM\QueryBuilder;

trait CanFilterResults
{
    /**
     * Apply "=" or "IN" conditions from a filter[key]=value query parameter.
     * - Keys not in the lookup are ignored
     * - Values outside allowed values throw a 400 error
     *
     * @param array<string, ListFilter> $filterLookup
     */
    protected function filterQueryBuilder(
        ServerRequest $request,
        QueryBuilder $queryBuilder,
        array $filterLookup,
        string $filterParam = 'filter'
    ): QueryBuilder {
        $filters = Types::array($request->getParam($filterParam));

        foreach ($filterLookup as $key => $filter) {
            $values = self::getFilterValues($key, $filters[$key] ?? null, $filter);
            if ($values === []) {
                continue;
            }

            $parameter = "filter_{$key}";
            $isSingleValue = count($values) === 1;

            $queryBuilder->andWhere(
                $isSingleValue
                    ? "{$filter->field} = :{$parameter}"
                    : "{$filter->field} IN (:{$parameter})"
            )->setParameter(
                $parameter,
                $isSingleValue ? $values[0] : $values
            );
        }

        return $queryBuilder;
    }

    /**
     * @return list<string>
     */
    private static function getFilterValues(
        string $key,
        mixed $rawValue,
        ListFilter $filter
    ): array {
        $rawValues = is_array($rawValue) ? $rawValue : [$rawValue];

        $values = [];
        foreach ($rawValues as $rawItem) {
            if ($rawItem === null) {
                continue;
            }

            if (!is_scalar($rawItem)) {
                throw self::invalidFilterValue(
                    $key,
                    get_debug_type($rawItem),
                    $filter
                );
            }

            $value = trim((string) $rawItem);
            if ($value === '') {
                continue;
            }

            if (!in_array($value, $filter->allowedValues, true)) {
                throw self::invalidFilterValue($key, $value, $filter);
            }

            $values[] = $value;
        }

        return $values;
    }

    private static function invalidFilterValue(
        string $key,
        string $value,
        ListFilter $filter
    ): ValidationException {
        return new ValidationException(
            sprintf(
                __('Invalid value "%s" for filter "%s". Allowed values: %s'),
                $value,
                $key,
                implode(', ', $filter->allowedValues)
            )
        );
    }
}
