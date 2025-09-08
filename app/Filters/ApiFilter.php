<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class ApiFilter
{
    /** Campos permitidos => operadores permitidos por campo */
    protected array $safeParms = [];

    /** alias => columna real (si usas alias en query string) */
    protected array $columnMap = [];

    /** campos fecha que aceptan _from/_to */
    protected array $dateFilters = [];

    /** alias operador => “tipo” de operación */
    protected array $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'lk' => 'like',     // %valor%
        'in' => 'in',       // whereIn
        'btn' => 'between',  // whereBetween
        'isnull' => 'isnull',   // IS NULL
        'notnull' => 'notnull',  // IS NOT NULL
    ];

    public function apply(Request $request, Builder $query): Builder
    {
        // 1) Filtros campo/op
        foreach ($this->safeParms as $param => $operators) {
            $column = $this->columnMap[$param] ?? $param;
            if (! $request->has($param)) continue;

            $conditions = $request->input($param);
            // Soporta ?campo=valor (eq implícito) y ?campo[op]=valor
            if (! is_array($conditions) || $this->isAssoc($conditions) === false) {
                $conditions = ['eq' => $conditions];
            }

            foreach ($conditions as $op => $value) {
                if (! in_array($op, $operators, true)) continue;

                $type = $this->operatorMap[$op] ?? '=';
                $value = $this->normalizeValue($value);

                switch ($type) {
                    case 'in':
                        $vals = is_array($value) ? $value : explode(',', (string) $value);
                        $query->whereIn($column, $vals);
                        break;

                    case 'like':
                        $query->where($column, 'like', '%' . $value . '%');
                        break;

                    case 'between':
                        // ?campo[btn]=a,b  o  ?campo[btn][]=a&campo[btn][]=b
                        $vals = is_array($value) ? array_values($value) : explode(',', (string) $value);
                        if (count($vals) >= 2) {
                            $query->whereBetween($column, [$vals[0], $vals[1]]);
                        }
                        break;

                    case 'isnull':
                        if ($this->truthy($value)) $query->whereNull($column);
                        break;

                    case 'notnull':
                        if ($this->truthy($value)) $query->whereNotNull($column);
                        break;

                    default: // operadores binarios (=, !=, >, >=, <, <=)
                        $query->where($column, $type, $value);
                        break;
                }
            }
        }

        // 2) Rangos de fecha por sufijo _from/_to
        foreach ($this->dateFilters as $dateField) {
            $col = $this->columnMap[$dateField] ?? $dateField;
            $fromKey = $dateField . '_from';
            $toKey   = $dateField . '_to';

            if ($request->filled($fromKey)) $query->whereDate($col, '>=', $request->input($fromKey));
            if ($request->filled($toKey))   $query->whereDate($col, '<=', $request->input($toKey));
        }

        // 3) Orden: ?sort=created_at,-id
        if ($request->filled('sort')) {
            $sorts = explode(',', $request->get('sort'));
            $allowedSorts = array_unique(array_merge(
                array_keys($this->safeParms),
                $this->dateFilters,
                ['id', 'created_at', 'updated_at']
            ));
            foreach ($sorts as $s) {
                $direction = str_starts_with($s, '-') ? 'desc' : 'asc';
                $field = ltrim($s, '-');
                if (! in_array($field, $allowedSorts, true)) continue;
                $column = $this->columnMap[$field] ?? $field;
                $query->orderBy($column, $direction);
            }
        } else {
            $query->latest('id');
        }

        return $query;
    }

    protected function isAssoc(array $arr): bool
    {
        // devuelve true si tiene claves tipo ['eq'=>..., 'lk'=>...]
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function normalizeValue(mixed $v): mixed
    {
        if (is_string($v)) {
            $l = strtolower($v);
            if ($l === 'true')  return 1;   // MySQL boolean
            if ($l === 'false') return 0;
            if ($l === 'null')  return null;
        }
        return $v;
    }

    protected function truthy(mixed $v): bool
    {
        if (is_bool($v)) return $v;
        if (is_null($v)) return false;
        $l = strtolower((string) $v);
        return in_array($l, ['1','true','yes','y','on'], true);
    }
}

/*
class ApiFilter{
    protected $safeParms = [];

    protected $columnMap = [];

    protected  $operatorMap = [];

    public function transform(Request $request){
        $eloQuery = [];

        foreach($this->safeParms as $parm => $operators){
            $query = $request->query($parm);

            if (!isset($query)){
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;

            foreach($operators as $operator){
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloQuery;
    }
}
*/
