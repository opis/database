<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\ORM;

use Closure;
use Opis\Database\Model;

abstract class BaseLoader
{
    /** @var    array */
    protected $with = array();

    /** @var    bool */
    protected $immediate = false;

    /**
     * @param   mixed   $value
     * @param   bool    $immediate  (optional)
     *
     * @return  $this
     */
    public function with($value, $immediate = false)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $this->with = $value;
        $this->immediate = $immediate;
        return $this;
    }

    /**
     * @param   Model   $model
     * @param   array   &$result
     */
    protected function prepareResults(Model $model, array &$results)
    {
        if (!empty($results) && !empty($this->with)) {
            $pk = $model->getPrimaryKey();
            $attr = $this->getWithAttributes();
            $ids = array();

            foreach ($results as $result) {
                $ids[] = $result->{$pk};
            }

            foreach ($attr['with'] as $with => $callback) {
                if (!method_exists($model, $with)) {
                    continue;
                }

                $loader = $model->{$with}()->getLazyLoader(array(
                    'ids' => $ids,
                    'callback' => $callback,
                    'with' => $attr['extra'][$with],
                    'immediate' => $this->immediate,
                ));

                if ($loader === null) {
                    continue;
                }

                foreach ($results as $result) {
                    $result->setLazyLoader($with, $loader);
                }
            }
        }
    }

    /**
     * @return  array
     */
    protected function getWithAttributes()
    {
        $with = array();
        $extra = array();

        foreach ($this->with as $key => $value) {
            $fullName = $value;
            $callback = null;

            if ($value instanceof Closure) {
                $fullName = $key;
                $callback = $value;
            }

            $fullName = explode('.', $fullName);
            $name = array_shift($fullName);
            $fullName = implode('.', $fullName);

            if ($fullName == '') {
                if (!isset($with[$name]) || $callback !== null) {
                    $with[$name] = $callback;

                    if (!isset($extra[$name])) {
                        $extra[$name] = array();
                    }
                }
            } else {
                if (!isset($extra[$name])) {
                    $with[$name] = null;
                    $extra[$name] = array();
                }

                $t = &$extra[$name];

                if (isset($t[$fullName]) || in_array($fullName, $t)) {
                    continue;
                }

                if ($callback === null) {
                    $t[] = $fullName;
                } else {
                    $t[$fullName] = $callback;
                }
            }
        }

        return array(
            'with' => $with,
            'extra' => $extra,
        );
    }
}
