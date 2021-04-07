<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

namespace Opis\Database\ORM\Traits;

use Closure;

trait LoaderTrait
{
    protected array $with = [];
    protected bool $immediate = false;

    /**
     * @param $value
     * @param bool $immediate
     * @return $this
     */
    public function with($value, bool $immediate = false): static
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->with = $value;
        $this->immediate = $immediate;
        return $this;
    }

    /**
     * @return  array
     */
    protected function getWithAttributes(): array
    {
        $with = [];
        $extra = [];

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

            if ($fullName === '') {
                if (!isset($with[$name]) || $callback !== null) {
                    $with[$name] = $callback;

                    if (!isset($extra[$name])) {
                        $extra[$name] = [];
                    }
                }
            } else {
                if (!isset($extra[$name])) {
                    $with[$name] = null;
                    $extra[$name] = [];
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

        return [
            'with' => $with,
            'extra' => $extra,
        ];
    }
}