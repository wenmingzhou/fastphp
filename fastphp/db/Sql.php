<?php
namespace fastphp\db;
use PDOStatement;

class sql
{
    // WHERE和ORDER拼装后的条件
    private $filter = '';

    // Pdo bindParam()绑定的参数集合
    private $param = array();

    protected $table;

    private $primary ='id';


    /**
     * 查询条件拼接，使用方式：
     *
     * $this->where(['id = 1','and title="Web"', ...])->fetch();
     * 为防止注入，建议通过$param方式传入参数：
     * $this->where(['id = :id'], [':id' => $id])->fetch();
     *
     * @param array $where 条件
     * @return $this 当前对象
     */

    public function where($where =array(),$param=array())
    {
        if($where)
        {
            $this->filter .=' where ';
            $this->filter .= implode(' ',$where);
            $this->param   =$param;
        }
        //print_r($this);die;
        return $this;

    }

    public function order($order =array())
    {
        if($order)
        {
            $this->filter .=' order by ';
            $this->filter .=implode(',',$order);
        }
        return $this;
    }

    public function limit($limit =array())
    {
        if($limit)
        {
            $this->filter .=' limit ';
            $this->filter .=implode(',',$limit);

        }
        return $this;
    }

    // 查询多条
    public function fetchAll()
    {
        $sql = sprintf("select * from `%s` %s", $this->table, $this->filter);

        $sth = Db::pdo($this->db)->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        return $sth->fetchAll();
    }

    // 查询一条
    public function fetch()
    {
        $sql = sprintf("select * from `%s` %s", $this->table, $this->filter);
        $sth = Db::pdo($this->db)->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetch();
    }

    public function update($data)
    {

        $sql = sprintf("update `%s` set %s %s", $this->table, $this->formatUpdate($data), $this->filter);

        $sth = Db::pdo($this->db)->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        //print_r($sth);die;
        $sth->execute();

        return $sth->rowCount();
    }

    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s", $this->table, $this->formatInsert($data));
        //echo $sql;die;
        $sth = Db::pdo($this->db)->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

    }

    // 根据条件 (id) 删除
    public function delete($id)
    {
        //delete from `item` where `id` = :id
        $sql = sprintf("delete from `%s` where `%s` = :%s", $this->table, $this->primary, $this->primary);

        $sth = Db::pdo($this->db)->prepare($sql);
        $sth = $this->formatParam($sth, [$this->primary => $id]);
        $sth->execute();

        return $sth->rowCount();
    }


    // 将数组转换成更新格式的sql语句
    private function formatUpdate($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = :%s", $key, $key);
        }
        return implode(',', $fields);
    }

    // 将数组转换成插入格式的sql语句
    private function formatInsert($data)
    {
        $fields = array();
        $names = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf(":%s", $key);
        }
        $field = implode(',', $fields);
        $name = implode(',', $names);
        return sprintf("(%s) values (%s)", $field, $name);
    }


    /**
     * 占位符绑定具体的变量值
     * @param PDOStatement $sth 要绑定的PDOStatement对象
     * @param array $params 参数，有三种类型：
     * 1）如果SQL语句用问号?占位符，那么$params应该为
     *    [$a, $b, $c]
     * 2）如果SQL语句用冒号:占位符，那么$params应该为
     *    ['a' => $a, 'b' => $b, 'c' => $c]
     *    或者
     *    [':a' => $a, ':b' => $b, ':c' => $c]
     *
     * @return PDOStatement
     */
    public function formatParam(PDOStatement $sth, $params = array())
    {
        //print_r($params);
        /*
         * Array(
                    [item_name] => Lets go
                    [description] => uuu  22211
                )
         */
        //insert into `item` (`item_name`,`description`) values (:item_name,:description)
        //update `item` set `item_name` = :item_name,`description` = :description where id = :id
        foreach ($params as $param => &$value) {
            $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
            $sth->bindParam($param, $value);
        }
        return $sth;
    }


}