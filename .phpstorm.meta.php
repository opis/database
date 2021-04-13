<?php
namespace PHPSTORM_META {
    override(\Opis\Database\EntityManager::create(), type(0));
}
namespace Opis\Database\SQL {
    use Opis\Database\ORM\Internal\{EntityQuery as E, Query as Q};

    class Where {
        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function is($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function isNot($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function lessThan($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function greaterThan($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function atLeast($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function atMost($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   int|float|string $value1
         * @param   int|float|string $value2
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function between($value1, $value2): WhereStatement {}

        /**
         * @param   int|float|string $value1
         * @param   int|float|string $value2
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function notBetween($value1, $value2): WhereStatement {}

        /**
         * @param   string  $value
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function like(string $value): WhereStatement {}

        /**
         * @param   string  $value
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function notLike(string $value): WhereStatement {}

        /**
         * @param   array|Closure   $value
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function in($value): WhereStatement {}

        /**
         * @param   array|Closure   $value
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function notIn($value): WhereStatement {}

        /**
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function isNull(): WhereStatement {}

        /**
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function notNull(): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function eq($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function ne($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function lt($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function gt($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function gte($value, bool $iscolumn = false): WhereStatement {}

        /**
         * @param   mixed   $value
         * @param   bool    $iscolumn   (optional)
         *
         * @return  WhereStatement|Select|Delete|Update|E|Q
         */
        public function lte($value, bool $iscolumn = false): WhereStatement {}
    }
}