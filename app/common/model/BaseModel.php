<?php

namespace app\common\model;

use think\db\Query;
use think\Model;

/**
 * 模型基类
 */
class BaseModel extends Model
{
    /**
     * 查找单条记录
     * @access public
     * @param mixed $data 主键值或者查询条件（闭包）
     * @param array|string $with 关联预查询
     * @param bool $cache 是否缓存
     * @return array|Model|void|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function get($data, $with = [], $cache = false)
    {
        if (is_null($data)) {
            return;
        }

        if (true === $with || is_int($with)) {
            $cache = $with;
            $with = [];
        }
        $query = static::parseQuery($data, $with, $cache);
        return $query->find($data);
    }

    /**
     * 查找所有记录
     * @access public
     * @param mixed $data 主键列表或者查询条件（闭包）
     * @param array|string $with 关联预查询
     * @param bool $cache 是否缓存
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function all($data = null, $with = [], $cache = false)
    {
        if (true === $with || is_int($with)) {
            $cache = $with;
            $with = [];
        }
        $query = static::parseQuery($data, $with, $cache);
        return $query->select($data);
    }

    /**
     * 分析查询表达式
     * @access public
     * @param mixed  $data  主键列表或者查询条件（闭包）
     * @param string $with  关联预查询
     * @param bool   $cache 是否缓存
     * @return Query
     */
    protected static function parseQuery(&$data, $with, $cache)
    {
        $result = self::with($with)->cache($cache);
        if (is_array($data) && key($data) !== 0) {
            $result = $result->where($data);
            $data   = null;
        } elseif ($data instanceof \Closure) {
            call_user_func_array($data, [ & $result]);
            $data = null;
        } elseif ($data instanceof Query) {
            $result = $data->with($with)->cache($cache);
            $data   = null;
        }
        return $result;
    }
}
