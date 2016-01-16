<?php
class Builder {
    public function build($query) {
        static $regExp = '/([^{"\']+|\'(?:\\\\\'.|[^\'])*\'|"(?:\\\\"|[^"])*"|{[^}{]+})/';
        return preg_replace_callback($regExp, array(&$this, "replaceCallback"), $query);
    }
}
class QueryBuilder extends Builder{
    public function getQueryObject($query) {
        $self = $this;
        return function() use($self,$query) {
            $argv = func_get_args();
            foreach($argv as $i => $arg) {
                $argv[$i]  = mysql_real_escape_string($arg);
                array_unshift($argv, $self->build($query));
                return call_user_func_array('sprintf',$argv);
            }
        };
    }
}

$builder = new QueryBuilder();
$deleteItem = $builder->getQueryObject("DELETE FROM {ITEM_TABLE} WHERE id =%d");
$deleteItem($_GET['id']);
