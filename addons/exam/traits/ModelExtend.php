<?php

namespace addons\exam\traits;

use think\Db;

/**
 * Trait ModelExtend
 * 自封装的模型扩展
 * 基本来源于Laravel操作
 * by zgc
 */
trait ModelExtend
{
    /**
     * 获取对象，空直接抛错
     *
     * @param integer $pk      主键
     * @param string  $message 提示的错误信息
     * @param array   $with    预加载
     */
    public static function findOrFail(int $pk, string $message = '数据不存在', $with = []): self
    {
        if (!$pk) {
            exam_fail('缺少主键信息');
        }

        $model = $with ? self::with($with)->find($pk) : self::get($pk);

        if (!$model) {
            exam_fail($message);
        }

        return $model;
    }

    /**
     * 更新或创建
     *
     * @param array $attributes 条件
     * @param array $values     值
     * @return mixed
     */
    public static function updateOrCreate(array $attributes, array $values = [], string $type = 'count')
    {
        $self = new static();
        // $model = $self->where($attributes)->find();
        $model = $self::get($attributes);

        if ($model) {
            $model->data($values, true);
        } else {
            $model = new static();
            $model->data($values);
        }

        $count = $model->allowField(true)->save();
        if ($type == 'count') {
            return $count;
        }

        return $model;
    }

    /**
     * 批量插入或更新
     *
     * @param array $values
     */
    public static function upsert(array $values, $pk = 'id')
    {
        Db::transaction(function () use ($values, $pk) {
            collection($values)->each(function ($item) use ($pk) {
                $id = isset($item[$pk]) ? $item[$pk] : 0;
                self::updateOrCreate(
                    [$pk => $id],
                    $item
                );
            });
        });
    }

    /**
     * 只取模型部分key数据
     *
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        // return only_keys($this->toArray(), $keys);
        $result = [];
        foreach ($this->toArray() as $k => $value) {
            if (in_array($k, $keys)) {
                $result[$k] = $value;
            }
        }
        return $result;
    }

    /**
     * 隐藏模型部分key数据
     *
     * @param array $keys
     * @return array
     */
    public function makeHidden(array $keys): array
    {
        // return only_keys($this->toArray(), $keys);
        $result = [];
        foreach ($this->toArray() as $k => $value) {
            if (in_array($k, $keys)) {
                unset($value[$k]);
                $result[$k] = $value;
            }
        }
        return $result;
    }
}
