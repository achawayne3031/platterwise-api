<?php /**
 *
 *
 * @package
 * @author	Platterwise
 * @copyright
 * @version	1.0.0
 */

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DBHelpers
{
    ////// get top column
    public static function top_column($dataModel, $column, $limit = 10)
    {
        try {
            return $dataModel
                ::query()
                ->orderBy($column, 'DESC')
                ->take($limit)
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function data_with_where_paginate(
        $dataModel,
        $where_data,
        $with_data,
        $limit = 20
    ) {
        try {
            return $dataModel
                ::query()
                ->with($with_data)
                ->where($where_data)
                ->orderBy('id', 'DESC')
                ->paginate($limit);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function data_where_paginate_active(
        $dataModel,
        $where_data,
        $limit = 20
    ) {
        try {
            return $dataModel
                ::query()
                ->active()
                ->where($where_data)
                ->orderBy('id', 'DESC')
                ->paginate($limit);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function data_where_paginate(
        $dataModel,
        $where_data,
        $limit = 20
    ) {
        try {
            return $dataModel
                ::query()
                ->where($where_data)
                ->orderBy('id', 'DESC')
                ->paginate($limit);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function data_with_paginate(
        $dataModel,
        $with_data,
        $limit = 20
    ) {
        try {
            return $dataModel
                ::query()
                ->with($with_data)
                ->orderBy('id', 'DESC')
                ->paginate($limit);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function data_paginate($dataModel, $limit = 20)
    {
        try {
            return $dataModel
                ::query()
                ->orderBy('id', 'DESC')
                ->paginate($limit);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function query_like_with_filter(
        $dataModel,
        $column,
        $value,
        $with = null
    ) {
        try {
            return $dataModel
                ::where($column, 'LIKE', "%{$value}")
                ->with($with)
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function query_like_filter($dataModel, $column, $value)
    {
        try {
            return $dataModel
                ::where($column, 'LIKE', "%{$value}")
                ->with(['user'])
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    //////  query where filter first data
    public static function query_filter_first($dataModel, $filter)
    {
        try {
            return $dataModel
                ::where($filter)
                ->get()
                ->first();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function insert_update_query($table, $where, $data)
    {
        try {
            return DB::table($table)->updateOrInsert($where, $data);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    // catch (\Illuminate\Database\QueryException $e) {
    //     if ($e->getCode() === '23000') { // integrity constraint violation
    //         return back()->withError('Invalid data');
    //     }
    // }

    public static function count($dataModel, $data = null)
    {
        try {
            if ($data == null) {
                return $dataModel::count();
            }
            return $dataModel
                ::query()
                ->where($data)
                ->count();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function exists($dataModel, $data)
    {
        try {
            return $dataModel
                ::query()
                ->where($data)
                ->exists();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function init_model($dataModel)
    {
        try {
            return $dataModel::query();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function with_take_query(
        $dataModel,
        $with_clause = [],
        $take = 8
    ) {
        try {
            return $dataModel
                ::query()
                ->with($with_clause)
                ->orderBy('id', 'DESC')
                ->take($take)
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function with_query($dataModel, $with_clause = [])
    {
        try {
            return $dataModel
                ::query()
                ->with($with_clause)
                ->orderBy('id', 'DESC')
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function where_query($dataModel, $where_clause = [])
    {
        try {
            return $dataModel
                ::query()
                ->where($where_clause)
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    public static function with_where_query_filter_first(
        $dataModel,
        $with_clause = [],
        $where = null
    ) {
        try {
            if ($where == null) {
                return $dataModel
                    ::query()
                    ->with($with_clause)
                    ->get()
                    ->first();
            } else {
                return $dataModel
                    ::query()
                    ->with($with_clause)
                    ->where($where)
                    ->get()
                    ->first();
            }
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    /////// where with query ////
    public static function with_where_query_filter(
        $dataModel,
        $with_clause = [],
        $where = null
    ) {
        try {
            if ($where == null) {
                return $dataModel
                    ::query()
                    ->with($with_clause)
                    ->orderBy('id', 'DESC')
                    ->get();
            } else {
                return $dataModel
                    ::query()
                    ->with($with_clause)
                    ->where($where)
                    ->orderBy('id', 'DESC')
                    ->get();
            }
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    //////  query where filter data
    public static function query_filter($dataModel, $filter)
    {
        try {
            return $dataModel
                ::where($filter)
                ->orderBy('id', 'DESC')
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    //////  query order by
    public static function query_order_by_desc($dataModel)
    {
        try {
            return $dataModel
                ::query()
                ->orderBy('id', 'DESC')
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get first query data
    public static function first_data($dataModel)
    {
        try {
            return $dataModel::first();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function data_desc_take($dataModel, $take = 5)
    {
        try {
            return $dataModel
                ::query()
                ->orderBy('id', 'DESC')
                ->take($take)
                ->get();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// get all query data
    public static function all_data($dataModel)
    {
        try {
            return $dataModel::all();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    /////// Delete query data
    public static function delete_query($dataModel, $id)
    {
        try {
            return $dataModel::where('id', $id)->delete();
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    /////// Delete query data for multiple records
    public static function delete_query_multi($dataModel, $filter)
    {
        try {
            DB::beginTransaction();

            $status = $dataModel::where($filter)->delete();

            DB::commit(); // execute the operations above and commit transaction

            return $status;
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    /////// update query v1 //////
    public static function update_query_v2($dataModel, $data, $id = 0)
    {
        DB::beginTransaction();

        try {
            if ($id != 0) {
                $status = $dataModel
                    ::where([
                        'id' => $id,
                    ])
                    ->update($data);

                DB::commit(); // execute the operations above and commit transaction

                return $status;
            } else {
                return null;
            }
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    ////// Update flexible /////
    public static function update_query_v3($dataModel, $data, $filter = null)
    {
        DB::beginTransaction();
        $status = null;

        try {
            if ($filter != null) {
                $status = $dataModel::where($filter)->update($data);
            } else {
                $status = $dataModel::query()->update($data);
            }
            DB::commit(); // execute the operations above and commit transaction
            return $status;
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    /////// Update query data
    public static function update_query($dataModel, $data, $id = 0)
    {
        DB::beginTransaction();
        $status = null;

        try {
            if ($id != 0) {
                $status = $dataModel::where('id', $id)->update($data);
                DB::commit(); // execute the operations above and commit transaction
            } else {
                $status = $dataModel::query()->update($data);
                DB::commit(); // execute the operations above and commit transaction
            }
            return $status;
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }

    //////// Insert query data /////////
    public static function create_query($dataModel, $data)
    {
        try {
            return $dataModel::create($data);
        } catch (Exception $e) {
            return ResponseHelper::error_response(
                'Server Error',
                $e->getMessage(),
                401,
                $e->getLine()
            );
        }
    }
}
